<?php
if(!isset($_COOKIE['vdiuser'])) { // if no cookie is set then go to the login page
  header('Location: ../login.php');
} else {  // if a cookie is set...
  $ck_check = $_COOKIE['vdiuser'];
  //see if the cookie data is valid
  require 'sql-connect.php';
  $sql = "SELECT * FROM users WHERE session_key = '$ck_check'";
  $result = $conn->query($sql);

  $conn->close();

  if ($result->num_rows > 0) {
      //header('Location: /vdi/');
      echo "logged in";
  } else {
      header('Location: ../login.php');
  }
}
?>
