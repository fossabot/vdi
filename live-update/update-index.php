<!DOCTYPE html>
<?php
session_start();
require '../include/sql-connect.php';
$count = 1;
$q = $_GET['q']; //gets the live search information
?>
<div class="w3-container">
	Last refreshed on <?php echo date('D d M Y H:i:s', time()); ?>
	<fieldset>
		<legend>Vehicle Board</legend>
		<div class="w3-container">
			<?php
			if ($q === "all") {
				$sql = "SELECT * FROM vehicle_list ORDER BY callsign";
			} elseif (strlen($q) > 0) {
				$sql = "SELECT * FROM vehicle_list WHERE callsign LIKE '%$q%' ORDER BY callsign";
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
						$button = "w3-green";
					} elseif ($row['veh_status'] == 2) {
						$button = "w3-orange";
					} elseif ($row['veh_status'] == 3) {
						$button = "w3-red";
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
						echo "<div class='w3-cell-row'>";
					}
					?>
					<div class="w3-cell w3-mobile">
					<fieldset>
						<legend>
							<button onclick="location.href='vdi.php?veh=<?php echo base64_encode($row["id"] . "-" . time()); ?>';" id="<?php echo $row["id"]; ?>" class="w3-button <?php echo $button; ?>"><?php echo $row["callsign"] . " - " . $row["registration"]; ?></button>
							<?php
							//add a VDI history button if supervisor or above
							if ($_SESSION['role'] >= 2) {
								?>
								<div class="w3-dropdown-hover">
									<button class="w3-button w3-blue">Vehicle Sub-Menu</button>
									<div class="w3-dropdown-content w3-bar-block w3-card-4">
										<a href='vdi-history.php?veh=<?php echo $row["id"]; ?>' class='w3-bar-item w3-button'>VDI History</a>
										<?php
										//allow authorised users to add a comment to the vehicle screen
										if ($_SESSION['role'] >= 3) {
											?><a onclick="document.getElementById('comment<?php echo $row['id']; ?>').style.display='block'; clearTimeout(timer);" class="w3-bar-item w3-button">Add Note</a><?php
										}
										?>
									</div>
								</div>
								<?php
							}
							?>
						</legend>
						<table class="w3-table">
							<?php
							//select any live notes associated with this vehicle
							$sql_b = "SELECT * FROM vehicle_notes WHERE vehicle_id = '" . $row['id'] . "' AND expired = '0' ORDER BY timestamp DESC";
							$result_b = $conn->query($sql_b);

							if ($result_b->num_rows > 0) {
								// output data of each row
								while($row_b = $result_b->fetch_assoc()) {
									echo "<tr><td colspan='4' style='color:red'>" . date('d/m/y H:i', strtotime($row_b['timestamp'])) . "<br />" . $row_b['note'] . "</td></tr>";
									//allow authorised users to close notes on a vehicle
									if ($_SESSION['role'] >= 3) {
										?><tr><td colspan="4"><button onclick="location.href='/vdi/include/submit-index-comment.php?delnote=<?php echo $row_b["id"]; ?>&veh=<?php echo $row["id"]; ?>'" class="w3-button w3-pale-blue">Remove Note</button></tr></td><?php
									}
								}
							}
							?>
							<tr>
								<td colspan="4"><?php echo $veh_type; ?></td>
							</tr>
							<tr>
								<td>ISSI</td>
								<td>HH1: <?php echo $issi_hh1; ?></td>
								<td>HH2: <?php echo $issi_hh2; ?></td>
								<td>Veh: <?php echo $issi_veh; ?></td>
							</tr>
							<tr>
								<td colspan="2">Svc: <?php echo date('d M y', $row['service']); ?></td>
								<td colspan="2">MOT: <?php echo date('d M y', $row['mot']); ?></td>
							</tr>
						</table>
					</fieldset>
					<div id="comment<?php echo $row['id']; ?>" class="w3-modal">
						<div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
							<form method="post" class="w3-container" name="form_comment<?php echo $row['id']; ?>" action="include/submit-index-comment.php?row=<?php echo $row['id']; ?>">
								<div class="w3-section w3-margin">
									<label><b>Note</b></label>
									<textarea class="w3-input w3-border w3-margin-bottom" rows="4" name="comment<?php echo $row['id']; ?>" placeholder="Enter a note here..." required></textarea>
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
								</div>
								<div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
									<button class="w3-button w3-green w3-left" type="submit">Update</button>
									<button onclick="document.getElementById('comment<?php echo $row['id']; ?>').style.display='none'" type="button" class="w3-button w3-red w3-right">Cancel</button>
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
		</div>
	</fieldset>
</div>
