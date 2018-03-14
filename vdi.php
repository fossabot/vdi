<!DOCTYPE html>
<?php
require 'include/sql-connect.php';

//check if vehicle id has been set
if(isset($_GET['veh'])) {
	$value = $_GET['veh'];
	$decode = base64_decode($value);
	$identifier = explode("-", $decode);

	//check if this is a valid vdi sheet by subtracting the submission time from now
	$submission_time = time() - $identifier[1];
	if($submission_time > 3600) {
		echo "Error - Invalid time stamp";
		exit;
	}

	//get vehicle information from the database
	$sql = "SELECT * FROM vehicle_list WHERE id=" . $identifier[0] . " LIMIT 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    // output data of each row
		while($row = $result->fetch_assoc()) {
			$veh_id = $row['id'];
			$veh_callsign = $row['callsign'];
			$veh_type_number = $row['vehicle_type'];
			$veh_reg = $row['registration'];
			$veh_mot = $row['mot'];
			$veh_service = $row['service'];
			$veh_mot_diff = $veh_mot - time();
			$veh_service_diff = $veh_service - time();
			$unix_month = 60*60*24*30;

			//format menu colour based on vehicle status
			if ($row['veh_status'] == 0) {
				$color = "w3-green";
			} elseif ($row['veh_status'] == 1) {
				$color = "w3-orange";
			} elseif ($row['veh_status'] == 2) {
				$color = "w3-red";
			}

			//set vehicle use and get vehicle type
			$sql_in = "SELECT * FROM vehicle_types WHERE id=" . $veh_type_number . " LIMIT 1";
			$result_in = $conn->query($sql_in);

			if ($result_in->num_rows > 0) {
			// output data of each row
				while($row_in = $result_in->fetch_assoc()) {
					$veh_type = $row_in['vehicle_type'];
					$veh_use = $row_in['veh_use'];
				}
			}
		}
	} else {
		echo "0 results";
	}
} else {
	echo "Error - No vehicle set";
	exit;
}

?>
<html lang="en">
<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-115788103-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'UA-115788103-1');
	</script>
	<title>Vehicle Daily Inspection</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

	<!-- This script hides the fault description boxes if the corresponding checkbox has been ticked -->
	<script>
		function hidetext(vehicle) {
			var cdl = 'detail_' + vehicle;
			var display = document.getElementById(cdl).style.display;
			if (display == "inline") {
				document.getElementById(cdl).style.display = "none";
				document.getElementById(cdl).required = false;
			} else if (display == "none") {
				document.getElementById(cdl).style.display = "inline";
				document.getElementById(cdl).required = true;
			}
		};

		//staff number validation script
		$(document).ready(function() {

			// check change event of the text field
			$("#staff_id").keyup(function() {

				// get text username text field value
				var username = $("#staff_id").val();

				// check username name only if length is equal to 8
				if(username.length == 8) {
					$("#status").html('<i class="fa fa-spinner"</i> Checking availability...');
					// check username
					$.post("functions/staff_id_check.php", {username: username}, function(data, status) {
						$("#status").html(data);
					});
				}
			});
		});

		//shrink menu on mobile devices script
		function shrink_menu() {
			var x = document.getElementById("small_bar");
			if (x.className.indexOf("w3-show") == -1) {
				x.className += " w3-show";
			} else {
				x.className = x.className.replace(" w3-show", "");
			}
		}
	</script>
</head>
	<body>
		<!-- create menu bar that remains at the top of the page when scrolling -->
		<div class="w3-top">
			<div class="w3-bar <?php echo  $color; ?>">
				<a class="w3-bar-item w3-mobile w3-hide-small"><?php echo $veh_callsign . " - " . $veh_reg . " - " . $veh_type; ?></a>
				<a class="w3-bar-item w3-mobile w3-hide-small w3-button w3-right" href="/vdi/">Vehicle Board</a>
				<a href="javascript:void(0)" class="w3-bar-item w3-button w3-right w3-hide-large w3-hide-medium" onclick="shrink_menu()">&#9776;</a>
			</div>
			<div id="small_bar" class="w3-bar-block <?php echo  $color; ?> w3-hide w3-hide-large w3-hide-medium">
				<a class="w3-bar-item"><?php echo $veh_callsign . " - " . $veh_reg . " - " . $veh_type; ?></a>
				<a class="w3-bar-item w3-button" href="/vdi/">Vehicle Board</a>
			</div>
		</div>
		<br /><br />
		<div class="w3-container">
			<form action="vdi-submit.php" method="post" id="vdi_post" name="vdi_post" onsubmit="return confirm('Are you sure you wish to submit this VDI?');">
				<fieldset>
					<legend>Details</legend>
					<div>
						<input type="hidden" value="<?php echo $veh_id; ?>" name="veh_id">
						<input class="w3-input" type="number" name="staff_id" id="staff_id" placeholder="Staff ID Number" pattern="[0-9] {8}" required>
						<div id="status"></div>

						<!-- create a datalist for typing in the vehicle location -->
						<datalist id="locations">
							<?php
							$sql = "SELECT id, location FROM location ORDER BY location";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								// output data of each row
								while($row = $result->fetch_assoc()) {
									?>
									<option value="<?php echo $row["location"]; ?>" id="loc<?php echo $row["id"]; ?>">
									<?php
								}
							} else {
								echo "0 results";
							}
							?>
						</datalist>
						<br /><input class="w3-input" type="text" list="locations" placeholder="Vehicle Location" name="veh_loc" required>

						<!-- section for vehicle notes, MOT and service dates including auto notify -->
						<fieldset><legend>Notes</legend>
							<?php
							//select any live notes associated with this vehicle
							$sql_b = "SELECT timestamp,note FROM vehicle_notes WHERE vehicle_id = '$veh_id' AND expired = '0' ORDER BY timestamp";
							$result_b = $conn->query($sql_b);

							if ($result_b->num_rows > 0) {
								// output data of each row
								while($row_b = $result_b->fetch_assoc()) {
									echo "<div style='color:red'>" . date('d/m/y H:i', strtotime($row_b['timestamp'])) . "<br />" . $row_b['note'] . "</div><hr>";
								}
							} else {
								echo "<div>No active notes</div>";
							}
							?>
						</fieldset>
						<fieldset><legend>MOT Status</legend>
						<?php
						if($veh_mot_diff > $unix_month) {
							$print = "Ok";
							$warn = "w3-green";
						} elseif (($veh_mot_diff <= $unix_month) && ($veh_mot_diff > 0)) {
							$days = intval(intval($veh_mot_diff) / (3600*24));
							if($days > 0)
							{
								$days_out = $days;
							} else {
								$days_out = 0;
							}
							$print = "MOT is due in $days_out days";
							$warn = "w3-orange";
						} elseif ($veh_mot_diff <= 0) {
							$print = "OVERDUE MOT";
							$warn = "w3-red";
							$exit = 1;
						}
						echo "<div class='$warn w3-panel w3-center w3-card-4'>$print</div>";

						//stop the script and display a warning if the MOT has expired.
						if (isset($exit)) {
							?>
							<div class="w3-panel w3-red w3-card-4 w3-center">
								<h3>WARNING!</h3>
								<p>This vehicle is overdue it's MOT and must not be driven</p>
							</div>
							<?php
						}
						?>
						</fieldset>
						<fieldset><legend>Service Status</legend>
						<?php
						if($veh_service_diff > $unix_month) {
							$print = "Ok";
							$warn = "w3-green";
						} elseif (($veh_service_diff <= $unix_month) && ($veh_service_diff > 0)) {
							$days = intval(intval($veh_mot_diff) / (3600*24));
							if($days > 0)
							{
								$days_out = $days;
							} else {
								$days_out = 0;
							}
							$print = "Service is due in $days_out days";
							$warn = "w3-orange";
						} elseif ($veh_service_diff <= 0) {
							$print = "OVERDUE SERVICE";
							$warn = "w3-red";
						}
						echo "<div class='$warn w3-panel w3-center w3-card-4'>$print</div>";
						?>
						</fieldset>
					</div>
				</fieldset>
				<fieldset>
					<legend><b>Statutory Check / Trust Requirements</b></legend>
					<div style="text-align:justify">
						The statutory check and pre-response (Trust requirement) checks must be undertaken prior to using the vehicle. The only exception is if you are allocated to respond to a C1 or predicted C1 call. You must complete these checks at the commencement of your duty and these should take no longer than 15 minutes to complete. The remaining checks should be completed at the earliest opportunity in your shift. Vehcile inspection should be carried out at the beginning of each shift and at any subsequent shift change.
					</div>
				</fieldset>
				<fieldset>
					<legend class="w3-text-red">Statutory Vehicle Inspection</legend>
						<?php
							$sql = "SELECT * FROM inspection_points WHERE $veh_use = 1 AND section = 1";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								// output data of each row
								while($row = $result->fetch_assoc()) {
									?>
									<div>
										<input class="w3-check" type="checkbox" id="check_<?php echo $row["id"]; ?>" name="check_<?php echo $row["id"]; ?>" onclick="hidetext(<?php echo $row["id"]; ?>)">
										<label for="check_<?php echo $row["id"]; ?>"><?php echo $row["criteria"]; ?></label>
										<br /><input class="w3-input" type="text" style="display:inline" id="detail_<?php echo $row["id"]; ?>" name="detail_<?php echo $row["id"]; ?>" placeholder="Fault Description" required>
									</div>
									<?php
								}
							} else {
								echo "0 results";
							}
						?>
				</fieldset>
				<fieldset>
					<legend class="w3-text-red">Pre-Response Checks (Trust Requirement)</legend>
						<?php
							$sql = "SELECT * FROM inspection_points WHERE $veh_use = 1 AND section = 2";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								// output data of each row
								while($row = $result->fetch_assoc()) {
									?>
									<div>
										<input class="w3-check" type="checkbox" id="check_<?php echo $row["id"]; ?>" name="check_<?php echo $row["id"]; ?>" onclick="hidetext(<?php echo $row["id"]; ?>)">
										<label for="check_<?php echo $row["id"]; ?>"><?php echo $row["criteria"]; ?></label>
										<br /><input class="w3-input" type="text" style="display:inline" id="detail_<?php echo $row["id"]; ?>" name="detail_<?php echo $row["id"]; ?>" placeholder="Fault Description" required>
									</div>
									<?php
								}
							} else {
								echo "0 results";
							}
						?>
				</fieldset>
				<!-- consider putting this on a second page so as to update any extra information based on the vehicle - or some kind of dynamic update -->
				<fieldset>
					<legend>Miscellaneous</legend>
						<?php
							$sql = "SELECT * FROM inspection_points WHERE $veh_use = 1 AND section = 3";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								// output data of each row
								while($row = $result->fetch_assoc()) {
									?>
									<div>
										<input class="w3-check" type="checkbox" id="check_<?php echo $row["id"]; ?>" name="check_<?php echo $row["id"]; ?>" onclick="hidetext(<?php echo $row["id"]; ?>)">
										<label for="check_<?php echo $row["id"]; ?>"><?php echo $row["criteria"]; ?></label>
										<br /><input class="w3-input" type="text" style="display:inline" id="detail_<?php echo $row["id"]; ?>" name="detail_<?php echo $row["id"]; ?>" placeholder="Fault Description" required>
										<?php
										if ($row['extra'] == 1) {
											echo " - EXTRA DETAIL RQD!!!!!";
										}
										?>
									</div>
									<?php
								}
							} else {
								echo "0 results";
							}
						?>
				</fieldset>
				<fieldset>
					<legend>Clinical/Rear Compartment</legend>
						<?php
							$sql = "SELECT * FROM inspection_points WHERE $veh_use = 1 AND section = 4";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								// output data of each row
								while($row = $result->fetch_assoc()) {
									?>
									<div>
										<input class="w3-check" type="checkbox" id="check_<?php echo $row["id"]; ?>" name="check_<?php echo $row["id"]; ?>" onclick="hidetext(<?php echo $row["id"]; ?>)">
										<label for="check_<?php echo $row["id"]; ?>"><?php echo $row["criteria"]; ?></label>
										<br /><input class="w3-input" type="text" style="display:inline" id="detail_<?php echo $row["id"]; ?>" name="detail_<?php echo $row["id"]; ?>" placeholder="Fault Description" required>
										<?php
										if ($row['extra'] == 1) {
											echo " - EXTRA DETAIL RQD!!!!!";
										}
										?>
									</div>
									<?php
								}
							} else {
								echo "0 results";
							}
						?>
				</fieldset>
				<fieldset>
					<legend>Cleaning</legend>
						<?php
							$sql = "SELECT * FROM inspection_points WHERE $veh_use = 1 AND section = 5";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								// output data of each row
								while($row = $result->fetch_assoc()) {
									?>
									<div>
										<input class="w3-check" type="checkbox" id="check_<?php echo $row["id"]; ?>" name="check_<?php echo $row["id"]; ?>" onclick="hidetext(<?php echo $row["id"]; ?>)">
										<label for="check_<?php echo $row["id"]; ?>"><?php echo $row["criteria"]; ?></label>
										<br /><input class="w3-input" type="text" style="display:inline" id="detail_<?php echo $row["id"]; ?>" name="detail_<?php echo $row["id"]; ?>" placeholder="Fault Description" required>
										<?php
										if ($row['extra'] == 1) {
											echo " - EXTRA DETAIL RQD!!!!!";
										}
										?>
									</div>
									<?php
								}
							} else {
								echo "0 results";
							}
						?>
				</fieldset>
				<br />
				<div class="w3-bar w3-green">
					<button class="w3-bar-item w3-green -button w3-center w3-mobile" type="submit" form="vdi_post">Submit VDI</button>
				</div>
			</form>
		</div>
	</body>
</html>
<?php
require 'include/footer.php';
?>
