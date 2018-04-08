<?php
require 'include/login-check.php';
require 'functions/functions.php';
check_auth(1);
?>
<!DOCTYPE html>
<?php
require 'include/sql-connect.php';
//set page specific functions
function printCriteria($query) { //function for printing all of the VDI criteria for each section
	if ($query->num_rows > 0) {
		// output data of each row
		while($row = $query->fetch_assoc()) {
			?>
				<div class="btn-group btn-group-toggle" data-toggle="buttons">
					<label class="btn btn-outline-danger active">
						<input type="radio" onfocus="hidetext('<?php echo $row["id"]; ?>')" name="check_<?php echo $row["id"]; ?>" id="check_<?php echo $row["id"]; ?>" value="0" autocomplete="off" checked> <i class="fas fa-times"></i>
					</label>
					<label class="btn btn-outline-success">
						<input type="radio" onfocus="hidetext('<?php echo $row["id"]; ?>')" name="check_<?php echo $row["id"]; ?>" id="check_<?php echo $row["id"]; ?>" value="1" autocomplete="off"> <i class="fas fa-check"></i>
					</label>
				</div>
				<label for="check_<?php echo $row["id"]; ?>"><?php echo $row["criteria"]; ?></label>
				<input class="form-control" type="text" style="display:inline" id="detail_<?php echo $row["id"]; ?>" name="detail_<?php echo $row["id"]; ?>" placeholder="Fault Description" required>
				<hr />
			<?php
		}
	} else {
		echo "Error";
	}
}

//check if vehicle id has been set
if(isset($_GET['veh'])) {
	$value = $_GET['veh'];
	$decode = base64_decode($value);
	$identifier = explode("-", $decode);

	//check if this is a valid vdi sheet by subtracting the submission time from now
	$submission_time = time() - $identifier[1];
	if($submission_time > 43200) { //set timeout to 12 hours
		echo "Error - Invalid time stamp. Use the back button on your browser.";
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
			if ($row['veh_status'] == 1) {
				$color = "w3-green";
			} elseif ($row['veh_status'] == 2) {
				$color = "w3-orange";
			} elseif ($row['veh_status'] == 3) {
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
	<?php include_once 'include/scripts.php'; ?>
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
	</script>
</head>
	<body>
		<?php include "include/header.php" ; require "include/sql-connect.php"; ?>
		<div class="container-fluid">
			<form action="include/submit-vdi.php" method="post" id="vdi_post" name="vdi_post" onsubmit="return confirm('Are you sure you wish to submit this VDI?');">
				<div class="card">
					<div class="card-header"><h5>Details</h5></div>
					<div class="card-body">
						<h6 class="card-subtitle mb-2 text-muted">VDI for <?php echo $veh_callsign . " which is a " . $veh_type . ", registration " . $veh_reg; ?>.</h6>
						<p class="card-text">VDI started by <?php echo $_SESSION['name'] . " at " . date('H:i d/m/Y', time()); ?></p>
						<div class="form-group">
							<input type="hidden" value="<?php echo $veh_id; ?>" name="veh_id">
							<input type="hidden" name="staff_id" value="<?php echo $_SESSION['staff_number']; ?>">

							<!-- create a datalist for typing in the vehicle location -->
							<datalist id="locations">
								<?php
								$sql = "SELECT * FROM location ORDER BY location";
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
							<input class="form-control" type="text" list="locations" placeholder="Vehicle Location" name="veh_loc" required>
						</div>
					</div>
				</div>
				<!-- section for vehicle notes, MOT and service dates including auto notify -->
				<div class="card">
					<div class="card-header"><h5>Notes</h5></div>
					<div class="card-body">
						<?php
						//select any live notes associated with this vehicle
						$sql_b = "SELECT timestamp,note FROM vehicle_notes WHERE vehicle_id = '$veh_id' AND expired = 0 ORDER BY timestamp";
						$result_b = $conn->query($sql_b);

						if ($result_b->num_rows > 0) {
							// output data of each row
							while($row_b = $result_b->fetch_assoc()) {
								echo "<p class='card-text text-danger'>" . date('d/m/y H:i', strtotime($row_b['timestamp'])) . "<br />" . $row_b['note'] . "</p><hr>";
							}
						} else {
							echo '<h6 class="card-subtitle mb-2 text-muted">No active notes</h6>';
						}
						?>
					</div>
				</div>
				<div class="card">
					<div class="card-header"><h5>MOT Status</h5></div>
					<div class="card-body">
						<?php
						if($veh_mot_diff > $unix_month) {
							$print = "Ok";
							$warn = "btn-success";
						} elseif (($veh_mot_diff <= $unix_month) && ($veh_mot_diff > 0)) {
							$days = intval(intval($veh_mot_diff) / (3600*24));
							if($days > 0)
							{
								$days_out = $days;
							} else {
								$days_out = 0;
							}
							$print = "MOT is due in $days_out days";
							$warn = "btn-warning";
						} elseif ($veh_mot_diff <= 0) {
							$print = "OVERDUE MOT";
							$warn = "btn-danger";
							$exit = 1;
						}
						echo "<button type='button' class='btn $warn w-100'>$print</button>";

						//stop the script and display a warning if the MOT has expired.
						if (isset($exit)) {
							?>
							<div class="alert alert-danger" role="alert">
								<h4 class="alert-heading">WARNING!</h4>
								This vehicle is overdue it's MOT and must not be driven
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<div class="card">
					<div class="card-header"><h5>Service Status</h5></div>
					<div class="card-body">
						<?php
						if($veh_service_diff > $unix_month) {
							$print = "Ok";
							$warn = "btn-outline-success";
						} elseif (($veh_service_diff <= $unix_month) && ($veh_service_diff > 0)) {
							$days = intval(intval($veh_mot_diff) / (3600*24));
							if($days > 0)
							{
								$days_out = $days;
							} else {
								$days_out = 0;
							}
							$print = "Service is due in $days_out days";
							$warn = "btn-outline-warning";
						} elseif ($veh_service_diff <= 0) {
							$print = "OVERDUE SERVICE";
							$warn = "btn-outline-danger";
						}
						echo "<button type='button' class='btn $warn w-100'>$print</button>";
						?>
					</div>
				</div>
				<div class="card">
					<div class="card-header"><h5>Statutory Check / Trust Requirements</h5></div>
					<div class="card-body">
						<p class="text-justify">
							The statutory check and pre-response (Trust requirement) checks must be undertaken prior to using the vehicle. The only exception is if you are allocated to respond to a C1 or predicted C1 call. You must complete these checks at the commencement of your duty and these should take no longer than 15 minutes to complete. The remaining checks should be completed at the earliest opportunity in your shift. Vehicle inspection should be carried out at the beginning of each shift and at any subsequent shift change.
						</p>
					</div>
				</div>
				<div class="card">
					<div class="card-header text-danger"><h5>Statutory Vehicle Inspection</h5></div>
					<div class="card-body">
						<div class="form-group">
							<?php
								$sql = "SELECT * FROM inspection_points WHERE $veh_use = 1 AND section = 1";
								$result = $conn->query($sql);
								printCriteria($result);
							?>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-header text-danger"><h5>Pre-Response Checks (Trust Requirement)</h5></div>
					<div class="card-body">
						<div class="form-group">
							<?php
								$sql = "SELECT * FROM inspection_points WHERE $veh_use = 1 AND section = 2";
								$result = $conn->query($sql);
								printCriteria($result);
							?>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-header"><h5>Miscellaneous</h5></div>
					<div class="card-body">
						<div class="form-group">
							<?php
								$sql = "SELECT * FROM inspection_points WHERE $veh_use = 1 AND section = 3";
								$result = $conn->query($sql);
								printCriteria($result);
							?>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-header"><h5>Clinical/Rear Compartment</h5></div>
					<div class="card-body">
						<div class="form-group">
							<?php
								$sql = "SELECT * FROM inspection_points WHERE $veh_use = 1 AND section = 4";
								$result = $conn->query($sql);
								printCriteria($result);
							?>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-header"><h5>Cleaning</h5></div>
					<div class="card-body">
						<div class="form-group">
							<?php
								$sql = "SELECT * FROM inspection_points WHERE $veh_use = 1 AND section = 5";
								$result = $conn->query($sql);
								printCriteria($result);
							?>
						</div>
					</div>
				</div>
				<br />
				<button type='submit' class='btn btn-success w-100' form="vdi_post">Submit VDI</button>
			</form>
		</div>
	</body>
</html>
<?php
require 'include/footer.php';
?>
