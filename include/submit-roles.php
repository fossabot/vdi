<?php
require 'login-check.php';
require '../functions/functions.php';
check_auth();
require 'sql-connect.php';
$user = $_POST['userid'];

//page permissions
$sql = "SELECT id, CONV(code,2,10) AS hrc FROM page_permissions WHERE display_in_menu = 1"; //select all codes from displayed pages (where permissions are generated from)
$result = $conn->query($sql);
$pgSum = 0;
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $pgBuild = "pg{$user}attr{$row['id']}";
    if ($_POST[$pgBuild] == 1) {
      $pgSum = $pgSum + $row['hrc'];
    }
  }
}

//user Permissions
$sql = "SELECT id, CONV(code,2,10) AS hrc FROM user_permissions";
$result = $conn->query($sql);
$userSum = 0;
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $userBuild = "user{$user}attr{$row['id']}";
    if ($_POST[$userBuild] == 1) {
      $userSum = $userSum + $row['hrc'];
    }
  }
}

//insert new permissions value into the user's account - also wipe their session key to force user to login again to refresh permissions
$sql = "UPDATE users SET user_access_level = $userSum, page_access_level = $pgSum, session_key = 0 WHERE id = $user LIMIT 1";
if ($conn->query($sql) === TRUE) {
    $last_id = $conn->insert_id;
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
	exit;
}

//return to vehicle page
$phpself = explode('/', $_SERVER['PHP_SELF']);
$url = $phpself[1];
//return to action page
$header = "Location: /$url/user-manager.php";
header($header);
?>
