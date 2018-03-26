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
		<div class="w3-container">
			Last refreshed on <?php echo date('D d M Y H:i:s', time()); ?>
			<fieldset>
				<legend><b> Actions from Crew VDI</b></legend>
				<div class="w3-container">
					<?php
					//list all outstanding reports
					$sqla = "SELECT vdi_log_detail.id, vdi_log_detail.comments, inspection_points.criteria, inspection_points.section, vdi_log.timestamp, vehicle_list.callsign, vehicle_list.registration, location.location, CONCAT(users.forename, ' ', users.surname) AS name, users.email, vehicle_types.vehicle_type FROM vdi_log_detail ";
					$sqlb = "LEFT JOIN inspection_points ON vdi_log_detail.inspection_point_id = inspection_points.id LEFT JOIN vdi_log ON vdi_log_detail.vdi_log_id = vdi_log.id LEFT JOIN vehicle_list ON vdi_log.vehicle_list_id = vehicle_list.id LEFT JOIN location ON vdi_log.location_id = location.id LEFT JOIN users ON vdi_log.staff_id = users.staff_number LEFT JOIN vehicle_types ON vehicle_list.vehicle_type = vehicle_types.id ";
					$sqlc = "WHERE report = '0' AND action_closed = '0' ORDER BY vdi_log_detail.id";
					$sql = $sqla.$sqlb.$sqlc;
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						?>
						<table class="w3-table-all" >
							<tr>
								<th>Callsign</th>
								<th>Criteria Failed</th>
								<th>Crew Comments</th>
								<th>Date/Time Reported</th>
								<th>Reported By</th>
								<th colspan="2" class="w3-center">Notes</th>
							</tr>
						<?php
						// output data of each row
						while($row = $result->fetch_assoc()) {
							//highlight rows in high priority sections
							if ($row['section'] == 1) {
								$w3 = "class='w3-red'";
							} elseif ($row['section'] == 2){
								$w3 = "class='w3-orange'";
							} else {
								$w3 = NULL;
							}
							?>
							<tr <?php echo $w3; ?>>
								<td><button onclick="document.getElementById('veh<?php echo $row['id']; ?>').style.display='block'" class="w3-button"><?php echo $row['callsign']; ?></button></td>
								<td><?php echo $row['criteria']; ?></td>
								<td><?php echo $row['comments']; ?></td>
								<td><?php echo $row['timestamp']; ?></td>
								<td><?php echo $row['name']; ?></a></td>
								<td><button onclick="document.getElementById('notes<?php echo $row['id']; ?>').style.display='block'" class="w3-button w3-green">View</button></td>
								<td><button onclick="document.getElementById('id<?php echo $row['id']; ?>').style.display='block'" class="w3-button w3-green">Update</button></td>
							</tr>
							<div id="id<?php echo $row['id']; ?>" class="w3-modal">
								<div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
									<form method="post" class="w3-container" name="form_comment<?php echo $row['id']; ?>" action="include/submit-update.php?row=<?php echo $row['id']; ?>&veh=<?php echo $row['callsign']; ?>">
										<div class="w3-section w3-margin">
											<label><b>Details</b></label>
											<textarea class="w3-input w3-border w3-margin-bottom" rows="10" name="comment<?php echo $row['id']; ?>" placeholder="Enter a note here..." required></textarea>
											<label><b>Vehicle Status</b></label>
											<select class="w3-select" name="status<?php echo $row['id']; ?>" required>
												<option value="" disabled selected>Choose an status</option>
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
											<select class="w3-select" name="outcome<?php echo $row['id']; ?>" required>
												<option value="" disabled selected>Choose an outcome</option>
												<option value="1">3rd party action required (ie mechanic needed)</option>
												<option value="2">Providing an update to the report</option>
												<option value="3">Resolved</option>
											</select>
										</div>
										<div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
											<button class="w3-button w3-green w3-left" type="submit">Update</button>
											<button onclick="document.getElementById('id<?php echo $row['id']; ?>').style.display='none'" type="button" class="w3-button w3-red w3-right">Cancel</button>
										</div>
									</form>

								</div>
							</div>
							<div id="notes<?php echo $row['id']; ?>" class="w3-modal">
								<div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
									<div class="w3-section w3-margin">
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
									<div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
										<button onclick="document.getElementById('notes<?php echo $row['id']; ?>').style.display='none'" type="button" class="w3-button w3-green w3-left">Ok</button>
									</div>
								</div>
							</div>
							<div id="veh<?php echo $row['id']; ?>" class="w3-modal">
								<div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:400px">
									<header class="w3-container w3-green">
										<h2><?php echo $row['callsign']; ?> - <?php echo $row['registration']; ?></h2>
									</header>
									<div class="w3-section w3-margin">
										<?php echo $row['vehicle_type']; ?><br /><br />
										At the time of reporting, this vehicle was at <b><?php echo $row['location']; ?></b><br />
									</div>
									<div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
										<button onclick="document.getElementById('veh<?php echo $row['id']; ?>').style.display='none'" type="button" class="w3-button w3-green w3-left">Ok</button>
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
			</fieldset>
		</div>
		<?php
		require 'include/footer.php';
		?>
	</body>
</html>
