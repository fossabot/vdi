<?php
function check_auth() { //check if a user's authorisation level will let them view a specific page
  require $_SERVER['DOCUMENT_ROOT'] . '/vdi-dev/include/sql-connect.php';
  $page = trim(basename($_SERVER['PHP_SELF']).PHP_EOL);
  $sql = "SELECT code FROM page_permissions WHERE page = '".$page."'";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      $x = 0;
      $return = 0;
      $user = $_SESSION['access_level'];
      $userbin = sprintf("%032b", $user);
      $pagebin = sprintf("%032b", $row['code']);
      $usersplit = str_split($userbin);
      $pagesplit = str_split($pagebin);
      foreach ($usersplit as $value) {
        if ($value == $pagesplit[$x] AND $value == 1) {
          $return++;
        }
        $x++;
      }
      if ($return == 1) {
        return 1;
      } elseif ($return > 1) {
        echo "Permissions error - too many matches identified." . $return;
        exit;
      } else {
        echo "You don't have the correct access permissions for this page. Please contact an administrator.<br />$pagebin<br />$userbin";
        exit;
      }
    }
  } else {
    echo "SQL Error - Too many results returned. $sql"; exit;
  }
}

function check_user($action) { //check if a user's authorisation level will let them carry out a specific function
  require $_SERVER['DOCUMENT_ROOT'] . '/vdi-dev/include/sql-connect.php';
  $sql = "SELECT code FROM user_permissions WHERE id = $action";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      $x = 0;
      $return = 0;
      $user = $_SESSION['user_level'];
      $userbin = sprintf("%032b", $user);
      $pagebin = sprintf("%032b", $row['code']);
      $usersplit = str_split($userbin);
      $pagesplit = str_split($pagebin);
      foreach ($usersplit as $value) {
        if ($value == $pagesplit[$x] AND $value == 1) {
          $return++;
        }
        $x++;
      }
      if ($return == 1) {
        return 1;
      } else {
        return 0;
      }
    }
  } else {
    echo "SQL Error - Too many results returned. $sql"; exit;
  }
}
