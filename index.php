<?php
require 'include/login-check.php';
require 'functions/functions.php';
check_auth(1);
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
	<script>
		var timer;
		function loadXMLDoc(url) {
			if (window.XMLHttpRequest) {
				// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp=new XMLHttpRequest();
				xmlhttp.open("POST",url,false);
				xmlhttp.send();
			} else {
				// code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				xmlhttp.open("POST",url,false);
				xmlhttp.send();
			}
			document.getElementById('row').innerHTML=xmlhttp.responseText;
		}

		function TimeOut() {
			loadXMLDoc('live-update/update-index.php?');
			timer = setTimeout("TimeOut()", 5000);
		}
	</script>
</head>
	<body onload="TimeOut()">
		<?php include "include/header.php" ; ?>
		<div class="main" id="row"></div>
	</body>
</html>
