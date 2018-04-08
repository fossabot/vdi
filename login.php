<?php
session_start();
$phpself = explode('/', $_SERVER['PHP_SELF']);
$url = $phpself[1];
if (isset($_GET['logout'])) {
	setcookie('vdiuser', 0, 1,"/$url/", "vremote.theparkys.net", 1, 1); //set the cookie so that it has expired
	session_unset();
  session_destroy();
}

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
				setcookie($cookie_name, $cookie_value, $cookie_expire,"/$url/", "vremote.theparkys.net", 1, 1);
				// update user record with cookie value
				$sql = "UPDATE users SET session_key = '$cookie_value' WHERE staff_number = '" . $_POST['usrname'] . "' LIMIT 1";

				if ($conn->query($sql) === TRUE) {
						//select user data to add to session variables
						$sql = "SELECT * FROM users WHERE staff_number = '" . $_POST['usrname'] . "' LIMIT 1";
						$result = $conn->query($sql);
						if ($result->num_rows > 0) {
							$row = mysqli_fetch_assoc($result);
						}

						$_SESSION['staff_number'] = $row['staff_number'];
						$_SESSION['name'] = $row['forename'] . " " . $row['surname'];
						$_SESSION['email'] = $row['email'];
						$_SESSION['role'] = $row['user_role'];
						$_SESSION['key'] = $cookie_value;

						$header = "Location: /$url/";
						header($header);
				} else {
				    echo "Error: " . $sql . "<br>" . $conn->error;
					exit;
				}
			} else {
				// incorrect password
				header('Location: login.php?error');
			}
		}
	} else {
		// incorrect username
		header('Location: login.php?error');
	}
} else {
	?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<?php include_once 'include/scripts.php'; ?>
	</head>
		<body>
			<?php
			if (isset($_GET['error'])) {
				?>
				<div class="w3-panel w3-red w3-display-container w3-top">
					<span onclick="this.parentElement.style.display='none'" class="w3-button w3-red w3-large w3-display-topright">&times;</span>
					<p>Your username or password is incorrect. Please try again.</p>
				</div>
				<?php
			} else {
				?>
				<!-- TEMPORARY ALERT -->
				<div class="w3-panel w3-blue w3-display-container w3-top">
					<span onclick="this.parentElement.style.display='none'" class="w3-button w3-blue w3-large w3-display-topright">&times;</span>
					<h3>Password Information</h3>
					<p>All passwords have been set to <b>1234</b></p>
				</div>
				<!-- TEMPORARY ALERT -->
				<?php
			}
			?>

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
				</div>
			</form>
		</div>
	</body>
	</html>
<?php
}
?>
