<?php
if (isset($_GET['row'])) {
  $form_note = "comment" . $_GET['row'];
  if (isset($_POST[$form_note])) {
    session_start();
    require 'sql-connect.php';
    //get variables
    $vehicle_id = $_GET['row'];
    $form_status = "status" . $_GET['row'];
    $status = $_POST[$form_status];
    $note = $_POST[$form_note];

    //insert note
    $sql = "INSERT INTO vehicle_notes (user_id, vehicle_id, note) VALUES (" . $_SESSION['staff_number'] . ", $vehicle_id, '$note')";

    if ($conn->query($sql) === TRUE) {
        $last_id = $conn->insert_id;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    	exit;
    }

    //update vehicle status
    $sql = "UPDATE vehicle_list SET veh_status = $status WHERE id = '$vehicle_id'";
    if ($conn->query($sql) === TRUE) {
        $last_id = $conn->insert_id;
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    	exit;
    }

    //return to vehicle page
    header("Location: /vdi/");
  }
} elseif (isset($_GET['delnote'])) { //section to hide the note from the vehicle screen
  require 'sql-connect.php';
  $vehicle_id = $_GET['veh'];
  $note_id = $_GET['delnote'];

  $sql = "UPDATE vehicle_notes SET expired = 1 WHERE id = $note_id";
  if ($conn->query($sql) === TRUE) {
      $last_id = $conn->insert_id;
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    exit;
  }

  //update vehicle status
  $sql = "UPDATE vehicle_list SET veh_status = 0 WHERE id = '$vehicle_id'";
  if ($conn->query($sql) === TRUE) {
      $last_id = $conn->insert_id;
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    exit;
  }

  //return to vehicle page
  header("Location: /vdi/");
}
?>
