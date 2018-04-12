<!-- create menu bar that remains at the top of the page when scrolling -->
<?php
require 'include/sql-connect.php';
?>
<div class="alert bg-danger" role="alert">
	<h5>THIS IS THE <b>DEVELOPMENT</b> SYSTEM</h5>
</div>
<nav class="navbar sticky-top navbar-dark navbar-expand-lg" style="background-color: #005EB8">
	<a class="navbar-brand" href="/vdi/">eVIMS</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
	<div class="collapse navbar-collapse" id="navbarNavDropdown">
		<ul class="navbar-nav">
			<?php
			//list all menu items
			$sql = "SELECT icon, page, human_readable, code FROM page_permissions WHERE display_in_menu = 1 ORDER BY item_position";
			$result = $conn->query($sql);
			$mobile = NULL;

			if ($result->num_rows > 0) {
			// output data of each row
				while($row = $result->fetch_assoc()) {
					$x = 0;
		      $return = 0;
		      $user = $_SESSION['access_level'];
		      $userbin = sprintf("%032b", $user);
		      $pagebin = sprintf("%032b", $row['code']);
		      $usersplit = str_split($userbin);
		      $pagesplit = str_split($pagebin);
		      foreach ($usersplit as $value) {
		        if ($value == $pagesplit[$x] AND $value == 1) {
		          $return++;
		        }
		        $x++;
		      }
		      if ($return == 1) {
		        echo "<li class='nav-item'><a class='nav-link d-none d-lg-block' href='" . $row['page'] . "'>" . $row['icon'] . "</a><a class='nav-link d-lg-none text-light' href='" . $row['page'] . "'>" . $row['icon'] . " " . $row['human_readable'] . "</a></li>";
		      }
				}
			}
			$conn->close();
			?>
			<li class="nav-item d-lg-none"><a class="nav-link text-light" data-toggle="modal" data-target="#helpMe"><i class="fas fa-question-circle fa-lg text-white" data-toggle="tooltip" data-placement="bottom" title="Help"></i> Help</a></li>
			<li><span class="navbar-text">Logged in as <?php echo $_SESSION['name']; ?></span></li>
		</ul>
	</div>
	<div class="mr-auto d-none d-lg-block">
		<ul class="navbar-nav">
			<li class="nav-item"><a class="nav-link" href="https://github.com/chssn/vdi"><i class="fab fa-github fa-lg text-white" data-toggle="tooltip" data-placement="bottom" title="GitHub Code Repositry"></i></a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="modal" data-target="#helpMe"><i class="fas fa-question-circle fa-lg text-white" data-toggle="tooltip" data-placement="bottom" title="Help"></i></a></li>
		</ul>
	</div>
</nav>
