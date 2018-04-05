<?php
require 'include/login-check.php';
require 'functions/functions.php';
check_auth(2); //requires supervisor access as a minimum
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include_once 'include/scripts.php'; ?>
</head>
<body>
  <?php include "include/header.php" ; ?>
</body>
</html>
