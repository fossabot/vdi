<?php
session_start();
if(!isset($_COOKIE['vdiuser'])) { // if no cookie is set then go to the login page
  header('Location: /vdi/login.php');
  exit;
} else {  // if a cookie is set...
  $ck_check = $_COOKIE['vdiuser'];
  //see if the cookie data is valid
  require 'sql-connect.php';
  $sql = "SELECT * FROM users WHERE session_key = '$ck_check' LIMIT 1";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // reset one time hash
    // set the user cookie
    /*require '/vdi/include/ppp.php';
    $cookie_name = "vdiuser";
    $cookie_value = GenerateRandomSequenceKey();
    $cookie_expire = 0; // cookie expires when the browser closes
    setcookie($cookie_name, $cookie_value, $cookie_expire,"/vdi/", "vremote.theparkys.net", 1, 1);

    // update user record with cookie value
    $sql = "UPDATE users SET session_key = '$cookie_value' WHERE staff_number = '" . $_SESSION['staff_number'] . "' LIMIT 1";

    if ($conn->query($sql) === TRUE) {
      //update session key value
      $_SESSION['key'] = $cookie_value;
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
      exit;
    }*/
  } else {
    header('Location: /vdi/login.php');
    exit;
  }
}
$conn->close();
?>
