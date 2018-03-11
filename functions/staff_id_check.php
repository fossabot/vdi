<?php
if(isset($_POST['username'])) {
	// include Database connection file 
	require_once('../include/sql-connect.php');

	$username = mysqli_real_escape_string($conn, $_POST['username']);

	$sql = "SELECT * FROM users WHERE staff_number = '$username'";
	$result = $conn->query($sql);

	if ($result->num_rows > 0)
	{
		while ($row = $result->fetch_assoc()) {
			//print user name
			echo "<div class='w3-green'>" . $row['forename'] . " " . $row['surname'] . "</div>";
		}
	} else {
		//flag an error that staff number isn't recognised
		echo "<div class='w3-red'><b>WARNING</b> $username is not a valid staff number</div>";
	}
}
?>