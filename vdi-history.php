<?php
require 'include/login-check.php';
require 'functions/functions.php';
check_auth(2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include_once 'include/scripts.php'; ?>
</head>
<body>
	<?php
	//check that url data is set
	if (isset($_GET['veh'])) {
		$veh_id = $_GET['veh'];
	} else {
		echo "Error: No vehicle ID set";
		exit;
	}
	require 'include/header.php';
	require 'include/sql-connect.php';
	?>
	<div class="container-fluid">
	<?php
		$table = array();

		//select VDI logs
		$sql_log = "SELECT vdi_log.id, vdi_log.vehicle_list_id, vdi_log.location_id, vdi_log.staff_id, vdi_log.timestamp, users.staff_number, users.forename, users.surname, users.email FROM vdi_log LEFT JOIN users ON vdi_log.staff_id = users.staff_number WHERE vehicle_list_id = $veh_id ORDER BY vdi_log.timestamp";
		$result_log = $conn->query($sql_log);

		if ($result_log->num_rows > 0) {
			while($row_log = $result_log->fetch_assoc()) {
				//column data here
				$col_number = $row_log['id'];

				if (!isset($table[0])) {
					$table[0] = $row_log['timestamp'] . "!" . $row_log['forename'] . "!" . $row_log['surname'];
				} else {
					$table[0] .= "," . $row_log['timestamp'] . "!" . $row_log['forename'] . "!" . $row_log['surname'];
				}

				//select detail associated with VDI log
				$sql_detail = "SELECT * FROM vdi_log_detail WHERE vdi_log_id = " . $row_log['id'] . " ORDER BY vdi_log_id ASC, inspection_point_id ASC";
				$result_detail = $conn->query($sql_detail);
				if ($result_detail->num_rows > 0) {
					while($row_detail = $result_detail->fetch_assoc()) {
						//row data here
						$row_number = $row_detail['inspection_point_id'];

						if (!isset($table[$row_number])) {
							$table[$row_number] = $row_detail['report'] . "-" . $row_detail['id'];
						} else {
							$table[$row_number] .= "," . $row_detail['report'] . "-" . $row_detail['id'];
						}
						$row_number++;
					}
				} else {
					echo "Error: No VDI detail associated with vdi_log " . $row_log['id'];
					exit;
				}
			}
		} else {
			echo "<div class='alert alert-warning' role='alert'><h2>No VDI data for this vehicle</h2></div>";
			exit;
		}
		$info = array();
		//get vehicle details
		$sql = "SELECT * FROM vehicle_list WHERE id = $veh_id";
		$result = $conn->query($sql);
		$veh = $result->fetch_assoc();

		//generate table
		$sql = "SELECT * FROM inspection_points ORDER BY criteria ASC";
		$result = $conn->query($sql);
		?>
		<table class="table">
			<tr>
				<th scope="col"><?php echo "VDI History for " . $veh['callsign'] . " - " . $veh['registration']; ?></th>
				<?php
				$data = explode(",", $table[0]);

				foreach ($data as $value) {
					//split user details from timestamp of VDI
					$heading = explode("!", $value);
					$date = date('d/m/y H:i', strtotime($heading[0]));
					echo "<th scope='col' data-toggle='tooltip' data-placement='bottom' title='Inspected by " . $heading[1] . " " . $heading[2] . "'>$date</th>";
				}
				?>
			</tr>
			<?php
			while($row = $result->fetch_assoc()) {
				$select = $row['id'];
				?>
				<tr>
					<td class="align-middle"><?php echo $row['criteria']; ?></td>
					<?php
					$data = explode(",", $table[$select]);
					foreach ($data as $value) {
						//split array data into value [0] and vdi_log_detail.id [1]
						$vsplit = explode("-", $value);

						//create table cells
						if ($vsplit[0] == 0) {
							//add a button to display further information regarding the criteria failure
							?><td><button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#info<?php echo $vsplit[1]; ?>"><i class='fas fa-times fa-lg'></i></button></td><?php
							array_push($info,$vsplit[1]);
						} elseif ($vsplit[0] == 1) {
							echo "<td><button type='button' class='btn btn-outline-success'><i class='fas fa-check'></i></button></td>";
						}
					}
					?>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
		//create modal boxes containing further information on failed critera
		foreach ($info as $id) {
			?>
			<div id="info<?php echo $id; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="info_title_<?php echo $id; ?>" aria-hidden="true">
				<div class="modal-dialog" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title" id="info_title_<?php echo $id; ?>">Detail</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
							<?php
							$hx_sql = "SELECT * FROM vdi_log_detail LEFT JOIN inspection_points ON vdi_log_detail.inspection_point_id = inspection_points.id LEFT JOIN vdi_log ON vdi_log_detail.vdi_log_id = vdi_log.id LEFT JOIN users ON vdi_log.staff_id = users.staff_number WHERE vdi_log_detail.id = " . $id;
							$hx_result = $conn->query($hx_sql);

							if ($hx_result->num_rows > 0) {
								// output data of each row
								while($hx_row = $hx_result->fetch_assoc()) {
									echo "<h6>" . $hx_row['criteria'] . "</h6>";
									echo "<p><b>" . date('d/m/y H:i', strtotime($hx_row['timestamp'])) . "</b><br /><i>Comment from <a href='mailto:" . $hx_row['email'] . "'>" . $hx_row['forename'] . " " . $hx_row['surname'] . "</a> (inspector):</i><br />" . $hx_row['comments'] . "</p>";
									//get comments from vdi_log_actions
									$action_sql = "SELECT * FROM vdi_log_actions LEFT JOIN users ON vdi_log_actions.user_id = users.staff_number WHERE vdi_log_actions.vehicle_log_detail_id = " . $id;
									$action_result = $conn->query($action_sql);

									if ($action_result->num_rows > 0) {
										// output data of each row
										while($action_row = $action_result->fetch_assoc()) {
											echo "<p><b>" . date('d/m/y H:i', strtotime($action_row['timestamp'])) . "</b><br /><i>Comment from <a href='mailto:" . $action_row['email'] . "'>" . $action_row['forename'] . " " . $action_row['surname'] . "</a>:</i><br />" . $action_row['comment'] . "</p>";
										}
									} else {
										echo "<p class='text-secondary'>No actions recorded</p>";
									}
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
		<?php
		}
		?>
	</div>
	<?php include 'include/footer.php'; ?>
</body>
</html>
