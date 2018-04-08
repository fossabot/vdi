<?php
require 'include/login-check.php';
require 'functions/functions.php';
check_auth(4); // 1 = all users, 2 = supervisor, 3 = DLO & 4 = admin
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include_once 'include/scripts.php'; ?>
</head>
<body>
  <?php
  require 'include/header.php';
  require 'include/sql-connect.php';
  ?>
  <div class="container-fluid">
    <?php
    //get all active shifts
    $sql = "SELECT crip_shifts.id, crip_shifts.description, location.location, location.id FROM crip_shifts LEFT JOIN location ON crip_shifts.location_id = location.id WHERE crip_shifts.hidden = 0 ORDER BY location.location, crip_shifts.description";
    $result = $conn->query($sql);
    $loc = NULL;
    $i = 0;

    if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {
        if ($loc == $row['location'].['id']) {
          echo "<br />{$row['description']}";
        } else {
          if($i > 0) { echo "</div></div>"; }
          echo "<div class='row border'>";
          echo "<div class='col'>{$row['location']}</div>";
          echo "<div class='col'>{$row['description']}";
          $i++;
        }
        $loc = $row['location'].['id'];
      }
      echo "</div>";
    }
    ?>
  </div>
</body>
</html>
