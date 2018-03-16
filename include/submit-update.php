<?php
session_start();
require 'sql-connect.php';

//create variables
$row_id = $_GET['row'];
$veh_callsign = $_GET['veh'];
$comment = $_POST['comment' . $row_id];
$status = $_POST['status' . $row_id];
$outcome = $_POST['outcome' . $row_id];
$user_id = $_SESSION['staff_number'];

//vdi_log_actions sql query
$sql = "INSERT INTO vdi_log_actions (vehicle_log_detail_id, user_id, comment) VALUES ($row_id, $user_id, '$comment')";
if ($conn->query($sql) === TRUE) {
    $last_id = $conn->insert_id;
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
	exit;
}

//update vehicle status
$sql = "UPDATE vehicle_list SET veh_status = $status WHERE callsign = '$veh_callsign'";
if ($conn->query($sql) === TRUE) {
    $last_id = $conn->insert_id;
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
	exit;
}

//closing an action point
if ($outcome == 3) {
  $sql = "UPDATE vdi_log_detail SET action_closed = 1 WHERE id = $row_id";
  if ($conn->query($sql) === TRUE) {
      $last_id = $conn->insert_id;
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  	exit;
  }
}

//return to action page
header("Location: /vdi/vdi-action.php");
?>
