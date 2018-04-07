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
    echo "<h3>SESSION</h3>";
    print_r($_SESSION);
    echo "<br /><br />";
    echo "<h3>COOKIE</h3>";
    print_r($_COOKIE);
		echo "<br /><br />";
		echo "<h3>SERVER</h3>";
		print_r($_SERVER);
    ?>
	</pre>
  </div>
</body>
