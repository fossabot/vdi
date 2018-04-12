<!DOCTYPE html>
<?php
session_start();
require '../include/sql-connect.php';
require '../functions/functions.php';
check_auth();
$count = 1;
$q = $_GET['q']; //gets the live search information
?>
<div class="container-fluid">
	Last refreshed on <?php echo date('D d M Y H:i:s', time()); ?>
	<fieldset>
		<legend>Vehicle Board</legend>
		<?php
		if ($q === "all") {
			$sql = "SELECT * FROM vehicle_list WHERE hidden = 0 ORDER BY callsign";
		} elseif (strlen($q) > 0) {
			$sql = "SELECT * FROM vehicle_list WHERE callsign LIKE '%$q%' AND hidden = 0 ORDER BY callsign";
		} else {
			echo "Error: Live data not available";
			exit;
		}

		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			// output data of each row
			while($row = $result->fetch_assoc()) {
				//format button colour based on vehicle status
				if ($row['veh_status'] == 1) {
					$button = "btn-success";
				} elseif ($row['veh_status'] == 2) {
					$button = "btn-warning";
				} elseif ($row['veh_status'] == 3) {
					$button = "btn-danger";
				}
				//set vehicle use and get vehicle type
				$sql_in = "SELECT * FROM vehicle_types WHERE id=" . $row['vehicle_type'] . " LIMIT 1";
				$result_in = $conn->query($sql_in);

				if ($result_in->num_rows > 0) {
				// output data of each row
					while($row_in = $result_in->fetch_assoc()) {
						$veh_type = $row_in['vehicle_type'];
						$veh_use = $row_in['veh_use'];
					}
				}
				//format ISSIs correctly as 4 digit numbers
				if ($row['issi_hh1'] < 1000) {
					$issi_hh1 = "0" . $row['issi_hh1'];
				} else {
					$issi_hh1 = $row['issi_hh1'];
				}
				if ($row['issi_hh2'] < 1000) {
					$issi_hh2 = "0" . $row['issi_hh2'];
				} else {
					$issi_hh2 = $row['issi_hh2'];
				}
				if ($row['issi_veh'] < 1000) {
					$issi_veh = "0" . $row['issi_veh'];
				} else {
					$issi_veh = $row['issi_veh'];
				}
				//create rows of 3
				if ($count == 1) {
					echo "<div class='row'>";
				}
				?>
				<div class="col border">
				<fieldset>
					<legend>
						<?php

						if (check_user(1) == 1) { //add a VDI history button if supervisor or above
							?>
							<div class="dropdown">
								<button class="btn <?php echo $button; ?> dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $row["callsign"] . " - " . $row["registration"]; ?></button>
								<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
									<button onclick="location.href='vdi.php?veh=<?php echo base64_encode($row["id"] . "-" . time()); ?>';" id="<?php echo $row["id"]; ?>" class="dropdown-item" type="button">Carry Out VDI</button>
									<button onclick='location.href="vdi-history.php?veh=<?php echo $row["id"]; ?>";' class='dropdown-item' type="button">VDI History</button>
									<button data-target="#note<?php echo $row['id']; ?>" data-toggle="modal" class="dropdown-item" type="button">Add Note</button><?php
									?>
								</div>
							</div>
							<?php
						} else { //for all other users display this
							?><button onclick="location.href='vdi.php?veh=<?php echo base64_encode($row["id"] . "-" . time()); ?>';" id="<?php echo $row["id"]; ?>" class="btn <?php echo $button; ?>"><?php echo $row["callsign"] . " - " . $row["registration"]; ?></button><?php
						}
						?>
					</legend>
					<table class="table">
						<?php
						//select any live notes associated with this vehicle
						$sql_b = "SELECT * FROM vehicle_notes WHERE vehicle_id = '" . $row['id'] . "' AND expired = '0' ORDER BY timestamp DESC";
						$result_b = $conn->query($sql_b);

						if ($result_b->num_rows > 0) {
							// output data of each row
							while($row_b = $result_b->fetch_assoc()) {
								echo "<tr><td colspan='3' style='color:red'>" . date('d/m/y H:i', strtotime($row_b['timestamp'])) . "<br />" . $row_b['note'] . "</td>";
								//allow authorised users to close notes on a vehicle
								if (check_user(1) == 1) {
									?><td><button onclick="location.href='include/submit-index-comment.php?delnote=<?php echo $row_b["id"]; ?>&veh=<?php echo $row["id"]; ?>'" class="btn btn-primary">Remove Note</button></tr></td><?php
								}
								echo "</tr>";
							}
						}
						?>
						<tr>
							<td colspan="4"><?php echo $veh_type; ?></td>
						</tr>
						<tr>
							<td><abbr title="Individual Short Subscriber Identity">ISSI</abbr></td>
							<td><abbr title="Hand Held Radio 1">HH1</abbr>: <?php echo $issi_hh1; ?></td>
							<td><abbr title="Hand Held Radio 2">HH2</abbr>: <?php echo $issi_hh2; ?></td>
							<td><abbr title="Vehicle Radio">Veh</abbr>: <?php echo $issi_veh; ?></td>
						</tr>
						<tr>
							<td colspan="2"><abbr title="Next service date">Svc</abbr>: <?php echo date('d M y', $row['service']); ?></td>
							<td colspan="2">MOT: <?php echo date('d M y', $row['mot']); ?></td>
						</tr>
					</table>
				</fieldset>
				<div id="note<?php echo $row['id']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal<?php echo $row['id']; ?>" aria-hidden="true">
					<div class="modal-dialog" role="document">
						<form method="post" action="include/submit-index-comment.php?row=<?php echo $row['id']; ?>">
							<div class="modal-content">
								<div class="modal-header" style="background-color: #e3f2fd;">
									<h5 class="modal-title"><?php echo $row["callsign"] . " - " . $row["registration"]; ?></h5>
								</div>
								<div class="modal-body">
									<div class="form-group">
										<label for="comment<?php echo $row['id']; ?>"><b>Note</b></label>
										<textarea class="form-control" rows="4" id="comment<?php echo $row['id']; ?>" name="comment<?php echo $row['id']; ?>" placeholder="Enter a note here..." required></textarea>
									</div>
									<div class="form-group">
										<label for="status<?php echo $row['id']; ?>"><b>Vehicle Status</b></label>
										<select class="custom-select" id="status<?php echo $row['id']; ?>" name="status<?php echo $row['id']; ?>" required>
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
									</div>
								</div>
								<div class="modal-footer">
									<button type="submit" class="btn btn-primary">Update</button>
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
								</div>
							</div>
						</form>

					</div>
				</div>
				</div>
				<?php
				//create rows of 3
				if ($count == 3) {
					echo "</div>";
					$count = 0;
				}
				$count++;
			}
		} else {
			echo "0 results";
		}
		?>
	</fieldset>
</div>
