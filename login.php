<?php
if (isset($_POST['login'])) {
	require 'include/sql-connect.php';
	require 'include/ppp.php';

	$sql = "SELECT * FROM users WHERE staff_number = '" . $_POST['usrname'] . "' LIMIT 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		// if username is vaild then output data of each row
		while($row = $result->fetch_assoc()) {
			//section for checking password
			$password = $row['password'];
			$pwhash = strtoupper(hash("sha256", $_POST['psw']));
			if ($password == $pwhash) {
				// set the user cookie
				$cookie_name = "vdiuser";
				$cookie_value = GenerateRandomSequenceKey();
				$cookie_expire = 0; // cookie expires when the browser closes
				setcookie($cookie_name, $cookie_value, $cookie_expire);
				// update user record with cookie value
				$sql = "UPDATE users SET session_key = '$cookie_value' WHERE staff_number = '" . $_POST['usrname'] . "' LIMIT 1";

				if ($conn->query($sql) === TRUE) {
				    $last_id = $conn->insert_id;
						header('Location: /vdi/');
				} else {
				    echo "Error: " . $sql . "<br>" . $conn->error;
					exit;
				}
			} else {
				echo "Fail<br/>$password<br/>$pwhash";
			}
		}
	} else {
		// state that username is not recognised
		?>
		<div>
			<form action="login.php" method="post">
				Sorry, we didn't recognise your staff number.<br />
				Please fill out the form below to register.<br />
				<input class="largebox" type="number" name="staff_id" value="<?php echo $_POST['staff_id']; ?>" pattern="[0-9] {8}" required>
				<button class="btn default" type="submit" name="login">Create New User</button>
			</form>
		</div>
		<?php
	}
} else {
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
		<!--<link rel="stylesheet" href="css/styles.css">-->
		<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	</head>
		<body>
			<!-- TEMPORARY ALERT -->
			<div class="w3-panel w3-blue w3-display-container w3-top">
				<span onclick="this.parentElement.style.display='none'" class="w3-button w3-blue w3-large w3-display-topright">&times;</span>
				<h3>Password Information</h3>
				<p>All passwords have been set to <b>1234</b></p>
			</div>
			<!-- TEMPORARY ALERT -->

			<div class="w3-container w3-display-middle w3-mobile">

			<div class="w3-center"><br>
				<img src="images/logo.png" alt="EEAST" style="width:30%" class="w3-circle w3-margin-top">
				<br />
				North & East Hertfordshire Operations
			</div>

			<form class="w3-container" action="login.php" method="post">
				<div class="w3-section">
					<label><b>Staff Number</b></label>
					<input class="w3-input w3-border w3-margin-bottom" type="number" placeholder="Enter Staff Number" name="usrname" required>
					<label><b>Password</b></label>
					<input class="w3-input w3-border" type="password" placeholder="Enter Password" name="psw" required>
					<button class="w3-button w3-block w3-green w3-section w3-padding" type="submit" name="login">Login</button>
					<input class="w3-check w3-margin-top" type="checkbox" name="remember" checked="checked"> Remember me
				</div>
			</form>
		</div>
	</body>
	</html>
<?php
}
?>
