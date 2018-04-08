<!-- create menu bar that remains at the top of the page when scrolling -->
<?php require 'include/sql-connect.php'; ?>
<nav class="navbar sticky-top navbar-dark navbar-expand-lg" style="background-color: #005EB8">
	<a class="navbar-brand" href="/vdi/">eVDI</a>
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
			$sql = "SELECT name, link, human FROM menu WHERE (user_role > 0 AND user_role < $role) ORDER BY position";
			$result = $conn->query($sql);
			$mobile = NULL;

			if ($result->num_rows > 0) {
			// output data of each row
				while($row = $result->fetch_assoc()) {
					echo "<li class='nav-item'><a class='nav-link d-none d-lg-block' href='" . $row['link'] . "'>" . $row['name'] . "</a><a class='nav-link d-lg-none text-light' href='" . $row['link'] . "'>" . $row['name'] . " " . $row['human'] . "</a></li>";
				}
			}
			$conn->close();
			?>
			<li class="nav-item d-lg-none"><a class="nav-link text-light" data-toggle="modal" data-target="#helpMe"><i class="fas fa-question-circle fa-lg text-white" data-toggle="tooltip" data-placement="bottom" title="Help"></i> Help</a></li>
			<li><span class="navbar-text">Logged in as <?php echo $_SESSION['name'] . " ($role_txt)"; ?></span></li>
		</ul>
	</div>
	<div class="mr-auto d-none d-lg-block">
		<ul class="navbar-nav">
			<li class="nav-item"><a class="nav-link" href="https://github.com/chssn/vdi"><i class="fab fa-github fa-lg text-white" data-toggle="tooltip" data-placement="bottom" title="GitHub Code Repositry"></i></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="modal" data-target="#helpMe"><i class="fas fa-question-circle fa-lg text-white" data-toggle="tooltip" data-placement="bottom" title="Help"></i></a></li>
		</ul>
	</div>
</nav>
