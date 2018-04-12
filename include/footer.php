<?php
include 'help-text.php';
if (isset($_GET['eng'])) {
  include 'eng.php';
}
//close any active database connections
$conn->close();
?>
