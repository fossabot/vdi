<?php
require 'include/login-check.php';
require 'functions/functions.php';
check_auth(3);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include_once 'include/scripts.php'; ?>
</head>
	<body>
		<?php
		require 'include/header.php';
		require 'include/sql-connect.php';
		$count = 1;
		?>
		<div class="container-fluid">
			<p>Last refreshed on <?php echo date('D d M Y H:i:s', time()); ?></p>
			<h4><b>VDI Faults Raised</b></h4>
			<?php
			//list all outstanding reports
			$sqla = "SELECT vdi_log_detail.id, vdi_log_detail.comments, inspection_points.criteria, inspection_points.section, vdi_log.timestamp, vehicle_list.callsign, vehicle_list.registration, location.location, CONCAT(users.forename, ' ', users.surname) AS name, users.email, vehicle_types.vehicle_type FROM vdi_log_detail ";
			$sqlb = "LEFT JOIN inspection_points ON vdi_log_detail.inspection_point_id = inspection_points.id LEFT JOIN vdi_log ON vdi_log_detail.vdi_log_id = vdi_log.id LEFT JOIN vehicle_list ON vdi_log.vehicle_list_id = vehicle_list.id LEFT JOIN location ON vdi_log.location_id = location.id LEFT JOIN users ON vdi_log.staff_id = users.staff_number LEFT JOIN vehicle_types ON vehicle_list.vehicle_type = vehicle_types.id ";
			$sqlc = "WHERE report = '0' AND action_closed = '0' ORDER BY vdi_log_detail.id";
			$sql = $sqla.$sqlb.$sqlc;
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
				?>
				<table class="table" >
					<tr>
						<th scope="col">Callsign</th>
						<th scope="col">Criteria Failed</th>
						<th scope="col">Crew Comments</th>
						<th scope="col">Date/Time Reported</th>
						<th scope="col">Reported By</th>
						<th scope="col" colspan="2">Notes</th>
					</tr>
				<?php
				// output data of each row
				while($row = $result->fetch_assoc()) {
					//highlight rows in high priority sections
					if ($row['section'] == 1) {
						$w3 = "class='table-danger'";
					} elseif ($row['section'] == 2){
						$w3 = "class='table-warning'";
					} else {
						$w3 = NULL;
					}
					?>
					<tr <?php echo $w3; ?>>
						<td><button class="btn btn-outline-dark" data-toggle="modal" data-target="#veh<?php echo $row['id']; ?>"><?php echo $row['callsign']; ?></button></td>
						<td><?php echo $row['criteria']; ?></td>
						<td><?php echo $row['comments']; ?></td>
						<td><?php echo $row['timestamp']; ?></td>
						<td><?php echo $row['name']; ?></a></td>
						<td><button class="btn btn-outline-dark" data-toggle="modal" data-target="#notes<?php echo $row['id']; ?>">View</button></td>
						<td><button class="btn btn-outline-dark" data-toggle="modal" data-target="#id<?php echo $row['id']; ?>">Update</button></td>
					</tr>
					<div id="id<?php echo $row['id']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="id_title_<?php echo $row['id']; ?>" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
      					<div class="modal-header">
					        <h5 class="modal-title" id="id_title_<?php echo $row['id']; ?>">Add new update...</h5>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
								<form method="post" name="form_comment<?php echo $row['id']; ?>" action="include/submit-update.php?row=<?php echo $row['id']; ?>&veh=<?php echo $row['callsign']; ?>">
					      	<div class="modal-body">
										<label for="comment<?php echo $row['id']; ?>"><b>Details</b></label>
										<textarea class="form-control" rows="10" name="comment<?php echo $row['id']; ?>" placeholder="Enter an update here..." required></textarea>
										<label for="status<?php echo $row['id']; ?>"><b>Vehicle Status</b></label>
										<select class="form-control" name="status<?php echo $row['id']; ?>" required>
											<option disabled selected>Choose an status</option>
											<?php
											//get status options from db
											$sql_status = "SELECT * FROM vehicle_status";
											$result_status = $conn->query($sql_status);
											while($row_status = mysqli_fetch_assoc($result_status)) {
												echo "<option value='" . $row_status['id'] . "'>" . $row_status['vehicle_status'] . "</option>";
											}
											?>
										</select>
										<label><b>Outcome</b></label>
										<select class="form-control" name="outcome<?php echo $row['id']; ?>" required>
											<option disabled selected>Choose an outcome</option>
											<option value="1">3rd party action required (ie mechanic needed)</option>
											<option value="2">Providing an update to the report</option>
											<option value="3">Resolved</option>
										</select>
									</div>
									<div class="modal-footer">
										<button class="btn btn-primary" type="submit">Update</button>
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div id="notes<?php echo $row['id']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="note_title_<?php echo $row['id']; ?>" aria-hidden="true">
						<div class="modal-dialog" role="document">
					    <div class="modal-content">
					      <div class="modal-header">
					        <h5 class="modal-title" id="note_title_<?php echo $row['id']; ?>">Fault History</h5>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
					      <div class="modal-body">
									<?php
									$hx_sql = "SELECT * FROM vdi_log_actions WHERE vehicle_log_detail_id = " . $row['id'] . " ORDER BY timestamp DESC";
									$hx_result = $conn->query($hx_sql);

									if ($hx_result->num_rows > 0) {
										// output data of each row
										while($hx_row = $hx_result->fetch_assoc()) {
											echo "<br /><div class='w3-panel w3-black'>" . $hx_row['timestamp'] . " - " . $hx_row['user_id'] . "</div>";
											echo $hx_row['comment'];
										}
									} else {
										echo "No comments recorded";
									}
									?>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>
					<div id="veh<?php echo $row['id']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="veh_title_<?php echo $row['id']; ?>" aria-hidden="true">
						<div class="modal-dialog" role="document">
					    <div class="modal-content">
					      <div class="modal-header">
					        <h5 class="modal-title" id="veh_title_<?php echo $row['id']; ?>"><?php echo $row['callsign'] . " - " . $row['registration']; ?></h5>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
					      <div class="modal-body">
									<p><?php echo $row['vehicle_type']; ?></p>
									<p>At the time of reporting, this vehicle was at <b><?php echo $row['location']; ?></b></p>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
				?></table><?php
			} else {
				echo "0 results";
			}
			?>

		</div>
		<?php include 'include/footer.php'; ?>
	</body>
</html>
