<!-- create menu bar that remains at the top of the page when scrolling -->
<?php require 'include/sql-connect.php'; ?>
<nav class="navbar sticky-top navbar-dark bg-dark navbar-expand-lg">
	<a class="navbar-brand" href="#">eVDI</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
	<div class="collapse navbar-collapse" id="navbarNavDropdown">
		<ul class="navbar-nav">
			<?php
			//get user role
			$role = $_SESSION['role'] + 1;

			//user role name
			$sql = "SELECT user_role FROM user_role WHERE id = " . $_SESSION['role'];
			$result = $conn->query($sql);
			$row = $result->fetch_assoc();
			$role_txt = $row['user_role'];

			//list all menu items for user role
			$sql = "SELECT name, link FROM menu WHERE (user_role > 0 AND user_role < $role) ORDER BY position";
			$result = $conn->query($sql);
			$mobile = NULL;

			if ($result->num_rows > 0) {
			// output data of each row
				while($row = $result->fetch_assoc()) {
					echo "<li class='nav-item'><a class='nav-link' href='" . $row['link'] . "'>" . $row['name'] . "</a></li>";
				}
			}
			$conn->close();
			?>
			<li class="nav-item"><a class="nav-link" href="https://github.com/chssn/vdi"><i class="fab fa-github fa-lg text-white"></i></a></li>
			<li><span class="navbar-text">Logged in as <?php echo $_SESSION['name'] . " ($role_txt)"; ?></span></li>
		</ul>
	</div>
		<!--<a class="nav-link" href="mailto:chris.parkinson@eastamb.nhs.uk"><b>UNDER DEVELOPMENT</b> - Contact Chris with any problems or suggestions.</a>-->
</nav>
