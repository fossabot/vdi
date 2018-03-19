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
		function showResult(str) {
			if (str.length==0) {
				document.getElementById("livesearch").innerHTML="";
				document.getElementById("livesearch").style.border="0px";
				return;
			}
			if (window.XMLHttpRequest) {
				// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp=new XMLHttpRequest();
			} else {  // code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange=function() {
				if (this.readyState==4 && this.status==200) {
					document.getElementById("livesearch").innerHTML=this.responseText;
					document.getElementById("livesearch").style.border="1px solid #A5ACB2";
				}
			}
			xmlhttp.open("GET","live-update/update-index.php?q="+str,true);
			xmlhttp.send();
		}
	</script>
</head>
	<body onload="showResult('all')">
		<?php include "include/header.php" ; ?>
		<form class="w3-container">
			<input class="w3-input w3-border w3-margin-bottom" placeholder="Type to search..." type="text" size="30" onkeyup="showResult(this.value)">
			<div id="livesearch"></div>
		</form>
	</body>
</html>
