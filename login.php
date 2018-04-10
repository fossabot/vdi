<?php
session_start();
$phpself = explode('/', $_SERVER['PHP_SELF']);
$url = $phpself[1];
$host = $_SERVER['HTTP_HOST'];

if (isset($_GET['logout'])) {
	setcookie('vdiuser', 0, 1,"/$url/", "$host", 1, 1); //set the cookie so that it has expired
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
				setcookie($cookie_name, $cookie_value, $cookie_expire,"/$url/", "$host", 1, 1);
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
			<div class="container-fluid">
				<div class="row mx-auto">
					<div class="col">
						<?php
						if (isset($_GET['error'])) {
							?>
							<div class="alert alert-warning" role="alert">
								<h3 class="alert-heading">Warning</h3>
								<p>Your username or password is incorrect. Please try again.</p>
							</div>
							<?php
						} else {
							?>
							<!-- TEMPORARY ALERT -->
							<div class="alert alert-primary" role="alert">
								<h3 class="alert-heading">Password Information</h3>
								<p>All passwords have been set to <b>1234</b></p>
							</div>
							<!-- TEMPORARY ALERT -->
							<?php
						}
						?>
					</div>
				</div>
				<div class="row mx-auto">
					<div class="col">
						<!--<img src="images/logo.png" alt="EEAST" style="width:30%" class="img-fluid mx-auto d-block">
						<br />
						<h5 class="text-center">North & East Hertfordshire Operations</h5>-->
						<p class="text-center"><i class="fas fa-ambulance fa-10x" style="color: #009639"></i><p>
						<h5 class="text-center">Electronic Vehicle Inspection and Management System</h5> <!-- eVIMS -->
					</div>
				</div>
				<div class="row mx-auto">
					<div class="col">
						<form action="login.php" method="post">
							<div class="form-group">
								<label for="usrname"><b>Staff Number</b></label>
								<input class="form-control" type="number" placeholder="Enter Staff Number" name="usrname" id="usrname" required>
								<label for="psw"><b>Password</b></label>
								<input class="form-control" type="password" placeholder="Enter Password" name="psw" id="psw" required>
								<br />
								<button type='submit' class='btn btn-success w-100' name="login">Login</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</body>
	</html>
<?php
}
?>
