<!DOCTYPE html>
<?php
require 'include/sql-connect.php';
$count = 1;
?>
<div class="w3-container">
	Last refreshed on <?php echo date('D d M Y H:i:s', time()); ?> (page automatically refreshes every 5 minutes)
	<fieldset>
		<legend><b> Actions from Crew VDI</b></legend>
		<div class="w3-container">
			<?php
			//list all outstanding reports
			$sql = "SELECT * FROM vdi_log_detail INNER JOIN inspection_points ON vdi_log_detail.inspection_point_id = inspection_points.id WHERE report = '0' AND action_closed = '0' ORDER BY vdi_log_detail.id";
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
					//get information from vdi_log relating to this report
					$sql_log = "SELECT * FROM vdi_log INNER JOIN vehicle_list ON vdi_log.vehicle_list_id = vehicle_list.id WHERE vdi_log.id = '" . $row['vdi_log_id'] . "' LIMIT 1";
					$result_log = $conn->query($sql_log);
					if ($result_log->num_rows > 0) {
						$row_log = mysqli_fetch_assoc($result_log);
					}
					//get location name
					$sql_loc = "SELECT location FROM location WHERE id = '" . $row_log['location_id'] . "' LIMIT 1";
					$result_loc = $conn->query($sql_loc);
					if ($result_loc->num_rows > 0) {
						$row_loc = mysqli_fetch_assoc($result_loc);
					}
					//get vehicle type
					$sql_type = "SELECT vehicle_type FROM vehicle_types WHERE id = '" . $row_log['vehicle_type'] . "' LIMIT 1";
					$result_type = $conn->query($sql_type);
					if ($result_type->num_rows > 0) {
						$row_type = mysqli_fetch_assoc($result_type);
					}
					?>
					<tr <?php echo $w3; ?>>
						<td><button onclick="document.getElementById('veh<?php echo $row['id']; ?>').style.display='block'" class="w3-button"><?php echo $row_log['callsign']; ?></button></td>
						<td><?php echo $row['criteria']; ?></td>
						<td><?php echo $row['comments']; ?></td>
						<td><?php echo $row_log['timestamp']; ?></td>
						<td><?php echo $row_log['staff_id']; ?></td>
						<td><button onclick="document.getElementById('id<?php echo $row['id']; ?>').style.display='block'" class="w3-button w3-green">Update</button></td>
						<td><button onclick="document.getElementById('notes<?php echo $row['id']; ?>').style.display='block'" class="w3-button w3-green">View</button></td>
					</tr>
					<div id="id<?php echo $row['id']; ?>" class="w3-modal">
						<div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
							<form class="w3-container" action="/action_page.php">
								<div class="w3-section w3-margin">
									<label><b>Updated By</b></label>
									<input class="w3-input w3-border w3-margin-bottom" type="text" placeholder="Enter your staff number" name="staff_number" required>
									<label><b>Details</b></label>
									<textarea class="w3-input w3-border w3-margin-bottom" rows="10" name="comment" placeholder="Enter a note here..."></textarea>
									<label><b>Vehicle Status</b></label>
									<select class="w3-select" name="option">
										<option value="" disabled selected>Choose an status</option>
										<option value="2">Off The Road</option>
										<option value="1">Advisory Note</option>
										<option value="0">On The Road</option>
									</select>
									<label><b>Outcome</b></label>
									<select class="w3-select" name="option">
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
								<p>Txt here</p> <!-- php mysql bit in here to display historical notes with newest at the top -->
							</div>
							<div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
								<button onclick="document.getElementById('notes<?php echo $row['id']; ?>').style.display='none'" type="button" class="w3-button w3-green w3-left">Ok</button>
							</div>
						</div>
					</div>
					<div id="veh<?php echo $row['id']; ?>" class="w3-modal">
						<div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:400px">
							<header class="w3-container w3-green">
								<h2><?php echo $row_log['callsign']; ?> - <?php echo $row_log['registration']; ?></h2>
							</header>
							<div class="w3-section w3-margin">
								<?php echo $row_type['vehicle_type']; ?><br /><br />
								At the time of reporting, this vehicle was at <b><?php echo $row_loc['location']; ?></b><br />
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