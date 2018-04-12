<?php
require '../include/login-check.php';
require '../functions/functions.php';
check_auth(); // 1 = all users, 2 = supervisor, 3 = DLO & 4 = admin
if (isset($_GET['db'])) {
  include '../include/sql-connect.php';
  $table = $_GET['db'];
  $id = $_POST['pk'];
  $column = $_POST['name'];
  $value = $_POST['value'];

  if ($_GET['db'] === "vehicle_list") { //specific to table changes
        //convert dates into unix timestamps for vehicle_list
    if ($column === "mot" OR $column === "service") {
      $value = strtotime($value);
    }
  }

  if(!empty($value)) {
    //update db with new data
    $sql = "UPDATE $table SET $column = '$value' WHERE id = $id LIMIT 1";
    if ($conn->query($sql) === TRUE) {
        $last_id = $conn->insert_id;
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    	exit;
    }
  } else {
      header('HTTP/1.0 400 Bad Request', true, 400);
      echo "This field is required!";
  }
}
?>
