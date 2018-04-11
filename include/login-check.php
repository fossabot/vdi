<?php
session_start();
if(!isset($_COOKIE['vdiuser'])) { // if no cookie is set then go to the login page
  header('Location: login.php');
  exit;
} elseif (isset($_COOKIE['vdiuser'])) {  // if a cookie is set...
  $ck_check = $_COOKIE['vdiuser'];
  //see if the cookie data is valid
  require 'sql-connect.php';
  $sql = "SELECT id FROM users WHERE session_key = '$ck_check' LIMIT 1";
  $result = $conn->query($sql);

  if ($result->num_rows == 1) { //does the cookie match the db?
    //check if session exists
    if (!isset($_SESSION['key'])) {
      header('Location: login.php');
      exit;
    } elseif ($_SESSION['key'] === $ck_check) { //does the session match the cookie?
      $pass = 1;
    }
  } else {
    header('Location: login.php');
    exit;
  }
}
if (!isset($pass)) {
  header('Location: login.php');
  exit;
} elseif ($pass == 1) {
  //continue...
} else {
  header('Location: login.php');
  exit;
}
$conn->close();
?>
