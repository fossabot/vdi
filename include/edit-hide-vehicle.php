<?php
require 'login-check.php';
require '../functions/functions.php';
check_auth(); 

if (isset($_GET['f'])) {
  require 'sql-connect.php';
  $hash_rx = $_GET['f'];

  if (isset($_GET['r'])) { //section to allow a vehicle to be un-hidden
    $hide = 0;
  } else {
    $hide = 1;
  }
  $sql = "UPDATE vehicle_list SET hidden = $hide WHERE SHA2(CONCAT_WS('-',callsign,registration,id,'$salt'), 256) = '$hash_rx' LIMIT 1";

  if($conn->query($sql) != TRUE) {
    die("Error: $sql <br />" . $conn->error);
  }

  header("Location: ".$_SERVER['HTTP_REFERER']);
}
?>
