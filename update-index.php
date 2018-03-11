<!DOCTYPE html>
<?php
require 'include/sql-connect.php';
$count = 1;
?>
<div class="w3-container">
	Last refreshed on <?php echo date('D d M Y H:i:s', time()); ?>
	<fieldset>
		<legend>Vehicle Board</legend>
		<div class="w3-container">
			<?php
			$sql = "SELECT * FROM vehicle_list ORDER BY callsign";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
				// output data of each row
				while($row = $result->fetch_assoc()) {
					//format button colour based on vehicle status
					if ($row['veh_status'] == 0) {
						$button = "w3-green";
					} elseif ($row['veh_status'] == 1) {
						$button = "w3-orange";
					} elseif ($row['veh_status'] == 2) {
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
						<legend><button onclick="location.href='vdi.php?veh=<?php echo base64_encode($row["id"] . "-" . time()); ?>';" id="<?php echo $row["id"]; ?>" class="w3-button <?php echo $button; ?>"><?php echo $row["callsign"] . " - " . $row["registration"]; ?></button></legend>						
						<table class="w3-table">
							<?php
							//select any live notes associated with this vehicle
							$sql_b = "SELECT timestamp,note FROM vehicle_notes WHERE vehicle_id = '" . $row['id'] . "' AND expired = '0' ORDER BY timestamp";
							$result_b = $conn->query($sql_b);

							if ($result_b->num_rows > 0) {
								// output data of each row
								while($row_b = $result_b->fetch_assoc()) {
									echo "<tr><td colspan='4' style='color:red'>" . date('d/m/y H:i', strtotime($row_b['timestamp'])) . "<br />" . $row_b['note'] . "<hr></td></tr>";
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
<?php
require 'include/footer.php';
?>