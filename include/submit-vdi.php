<?php require 'include/login-check.php'; ?>
<!DOCTYPE html>
<html>
<?php
require 'include/sql-connect.php';

//get location id
$sql = "SELECT id FROM location WHERE location='" . $_POST['veh_loc'] . "' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$veh_loc = $row['id'];
	}
} else {
	echo "0 results - $sql<br />" . $conn->error;
	exit;
}

//insert vdi overview data into a database and return the row id
$sql = "INSERT INTO vdi_log (vehicle_list_id, location_id, staff_id) VALUES (" . $_POST['veh_id'] . ", ". $veh_loc . ", ". $_POST['staff_id'] . ")";

if ($conn->query($sql) === TRUE) {
    $last_id = $conn->insert_id;
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
	exit;
}

//insert vdi log details
$sql = NULL;
$email = NULL;
$chk = 0;

foreach($_POST as $x=>$x_value) {
	//checks if the loop is reading a check key
	if(substr($x, 0, 5) == "check") {
		$split = explode('_', $x);
		$ipid_a = $split[1];
		//check if box was checked or not
		if($x_value == "on") {
			$report = 1;
		}
		$chk = 1;
	} elseif(substr($x, 0, 6) == "detail") {
		$split = explode('_', $x);
		$ipid_b = $split[1];
		$y_temp = $x_value;

		if($chk == 0) {
			$comment = $x_value;
			$ipid = $ipid_b;
			$report = 0;
			$ipid_a = 0;
		} else {
			$comment = NULL;
			$ipid = $ipid_b;
		}
		//checks if there is enough data to generate a query
		if($ipid_a == $ipid_b OR $chk == 0) {
			$sql .= "INSERT INTO vdi_log_detail (vdi_log_id, inspection_point_id, report, comments) VALUES ('$last_id', '$ipid', '$report', '$comment');";
			$email .= "<tr><td>$last_id</td><td>$ipid</td><td>$report</td><td>$comment</td></tr>";
		}
		$chk = 0;
	}
}

$sql = substr($sql, 0, -1); //remove the last ; from the SQL INSERT command.

//put something in here to generate an email to the user and DLO team (if there are any points raised)

if (mysqli_multi_query($conn, $sql)) {
    //echo "New records created successfully";
	?><script>alert('VDI successfully sent'); window.location.replace("index.php");</script><?php
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

require 'include/footer.php';
?>
</html>
