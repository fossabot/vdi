<!DOCTYPE html>
<?php
require 'include/sql-connect.php';
?>
<html lang="en">
<head>
	<title>Vehicle Daily Inspection</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/styles.css">
</head>
	<body>
		<div>
			<fieldset>
				<legend>Vehicle Board</legend>
				<div>
					<?php
					$sql = "SELECT * FROM vehicle_list ORDER BY callsign";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						// output data of each row
						while($row = $result->fetch_assoc()) {
							//format button colour based on vehicle status
							if ($row['veh_status'] == 0) {
								$button = "btn good";
							} elseif ($row['veh_status'] == 1) {
								$button = "btn warning";
							} elseif ($row['veh_status'] == 2) {
								$button = "btn danger";
							}
							?>
							<fieldset>
								<legend><button onclick="location.href='vdi.php?veh=<?php echo base64_encode($row["id"] . "-" . time()); ?>';" id="<?php echo $row["id"]; ?>" class="<?php echo $button; ?>"><?php echo $row["callsign"]; ?></button></legend>						
								Data about vehicle here...
							</fieldset> 
							<?php 
						}
					} else {
						echo "0 results";
					}
					?>
				</div>
			</fieldset>
		</div>
	</body>
</html>
<?php
require 'include/footer.php';
?>