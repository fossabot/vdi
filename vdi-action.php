<!DOCTYPE html>
<html lang="en">
<head>
	<title>Vehicle Daily Inspection</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--<link rel="stylesheet" href="css/styles.css">-->
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script>
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
			loadXMLDoc('live-update/update-vdi-action.php');
			setTimeout("TimeOut()", 300000);
		}
	</script>
</head>
<body onload="TimeOut()">
	<!-- create menu bar that remains at the top of the page when scrolling -->
	<?php include "include/header.php" ; ?>
	<!-- <button onclick="TimeOut()">Refresh</button>&nbsp;This screen will automatically update every 5 seconds. -->
	<div class="main" id="row"></div>
</body>
</html>
