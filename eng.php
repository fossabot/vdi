<?php
require 'include/login-check.php';
require 'functions/functions.php';
check_auth(4);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-115788103-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'UA-115788103-1');
	</script>
	<title>Vehicle Daily Inspection</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
  <?php include 'include/header.php'; ?>
  <div class="w3-container">
    <?php
    echo "<b>SESSION</b><br /><br />";
    print_r($_SESSION);
    echo "<br /><br />";
    echo "<b>COOKIE</b><br /><br />";
    print_r($_COOKIE);
    ?>
  </div>
</body>
