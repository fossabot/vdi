<?php
require 'include/login-check.php';
require 'functions/functions.php';
check_auth(3);
?>
<!DOCTYPE html>
<?php require 'include/sql-connect.php'; ?>
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
		<!--<link rel="stylesheet" href="css/styles.css">-->
		<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	</head>
	<body>
		<div class="w3-container">
			<?php
			$sql = "SELECT * FROM vehicle_list ORDER BY callsign";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
				// output data of each row
				while($row = $result->fetch_assoc()) {
					?>
					<button onclick="expandsection('vdi-<?php echo $row['id']; ?>')" class="w3-btn w3-block w3-grey w3-left-align"><?php echo $row['callsign']; ?></button>
					<div id="vdi-<?php echo $row['id']; ?>" class="w3-container w3-hide">
					<?php
					//get vdi log data
					$sql_v = "SELECT * FROM vdi_log WHERE vehicle_list_id = '" . $row['id'] . "' ORDER BY timestamp";
					$result_v = $conn->query($sql_v);
					if ($result->num_rows > 0) {
						// output data of each row
						while($row_v = $result_v->fetch_assoc()) {
							?>
							<button onclick="expandsection('vdi-data-<?php echo $row_v['id']; ?>')" class="w3-btn w3-block w3-grey w3-left-align"><?php echo $row_v['timestamp']; ?></button>
							<div id="vdi-data-<?php echo $row_v['id']; ?>" class="w3-container w3-hide">
								<table class="w3-table-all w3-hoverable w3-responsive">
									<tr><th>Inspection Criteria</th><th>Check</th><th>Comments</th></tr>
								<?php
								//get vdi data
								$sql_d = "SELECT * FROM vdi_log_detail INNER JOIN inspection_points ON vdi_log_detail.inspection_point_id = inspection_points.id WHERE vdi_log_detail.vdi_log_id = '" . $row_v['id'] . "' ORDER BY vdi_log_detail.inspection_point_id";
								$result_d = $conn->query($sql_d);

								if ($result_d->num_rows > 0) {
									// output data of each row
									while($row_d = $result_d->fetch_assoc()) {
										echo "<tr><td>" . $row_d['criteria'] . "</td><td>" . $row_d['report'] . "</td><td>" . $row_d['comments'] . "</td></tr>";
									}
								}
								?>
								</table>
							</div>
							<?php
						}
					}
					?>
					</div>
					<?php
				}
			}
			?>
		</div>
	</body>
	<script>
		function expandsection(id) {
			var x = document.getElementById(id);
			if (x.className.indexOf("w3-show") == -1) {
				x.className += " w3-show";
				x.previousElementSibling.className =
				x.previousElementSibling.className.replace("w3-grey", "w3-green");
			} else {
				x.className = x.className.replace(" w3-show", "");
				x.previousElementSibling.className =
				x.previousElementSibling.className.replace("w3-green", "w3-grey");
			}
		}
	</script>
</html>
