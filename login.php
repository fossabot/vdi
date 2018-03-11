<!DOCTYPE html>
<html lang="en">
	<head>
		<title>VDI System Login</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="css/styles.css">
	</head>
	<body>
		<?php 
		if (isset($_POST['login'])) {
			require 'include/sql-connect.php';
			
			$sql = "SELECT * FROM users WHERE staff_number = '" . $_POST['staff_id'] . "' LIMIT 1";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
				// output data of each row
				while($row = $result->fetch_assoc()) {
					echo $row['surname'];
				}
			} else {
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
			<form action="login.php" method="post">
				<input class="largebox" type="number" name="staff_id" placeholder="Staff ID Number" pattern="[0-9] {8}" required>
				<button class="btn default" type="submit" name="login">Login</button>
			</form>
		<?php
		}
		?>
	</body>
</html>
		