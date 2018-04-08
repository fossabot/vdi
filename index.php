<?php
require 'include/login-check.php';
require 'functions/functions.php';
check_auth(1);
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
		<form class="w3-container">
			<input class="w3-input w3-border w3-margin-bottom" placeholder="Type to search..." type="text" size="30" onkeyup="showResult(this.value)">
		</form>
		<div id="livesearch"></div>
	</body>
</html>
