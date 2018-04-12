<?php
require 'include/login-check.php';
require 'functions/functions.php';
check_auth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include_once 'include/scripts.php'; ?>
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
			<label class="sr-only" for="search">Vehicle Search</label>
			<input class="form-control" placeholder="Type here to search for a vehicle..." id="search" type="text" size="30" onkeyup="showResult(this.value)">
		<div id="livesearch"></div>
		<?php include 'include/footer.php'; ?>
	</body>
</html>
