<?php
require 'include/login-check.php';
require 'functions/functions.php';
check_auth(4);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include_once 'include/scripts.php'; ?>
</head>
<body>
  <?php include 'include/header.php'; ?>
  <div class="container">
		<pre>
    <?php
    echo "<b>SESSION</b><br /><br />";
    print_r($_SESSION);
    echo "<br /><br />";
    echo "<b>COOKIE</b><br /><br />";
    print_r($_COOKIE);
		echo "<b>SERVER</b><br /><br />";
		print_r($_SERVER);
    ?>
	</pre>
  </div>
</body>
