<!-- create menu bar that remains at the top of the page when scrolling -->
<?php require 'include/sql-connect.php'; ?>
<div class="w3-top">
	<div class="w3-bar w3-green">
		<div class="w3-dropdown-hover">
			<button class="w3-button w3-green w3 w3-hide-small">Menu</button>
			<div class="w3-dropdown-content w3-bar-block w3-card-4">
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
						echo "<a href='" . $row['link'] . "' class='w3-bar-item w3-button w3-hide-small'>" . $row['name'] . "</a>";
						$mobile .= "<a class='w3-bar-item w3-button' href='" . $row['link'] . "'>" . $row['name'] . "</a>";
					}
				}
				$conn->close();
				?>
			</div>
		</div>
		<a class="w3-bar-item w3-mobile w3-hide-small">Logged in as <?php echo $_SESSION['name'] . " ($role_txt)"; ?></a>
		<a class="w3-bar-item w3-mobile w3-hide-small w3-button w3-right" href="mailto:chris.parkinson@eastamb.nhs.uk"><b>UNDER DEVELOPMENT</b> - Contact Chris with any problems or suggestions.</a>
		<a href="javascript:void(0)" class="w3-bar-item w3-button w3-right w3-hide-large w3-hide-medium" onclick="shrink_menu()">&#9776;</a>
	</div>
	<div id="small_bar" class="w3-bar-block w3-light-gray w3-hide w3-hide-large w3-hide-medium">
		<?php	echo $mobile; ?>
		<a class="w3-bar-item w3-button" href="mailto:chris.parkinson@eastamb.nhs.uk"><b>UNDER DEVELOPMENT</b> - Contact Chris with any problems or suggestions.</a>
	</div>
	<script>
		//shrink menu on mobile devices script
		function shrink_menu() {
			var x = document.getElementById("small_bar");
			if (x.className.indexOf("w3-show") == -1) {
				x.className += " w3-show";
			} else {
				x.className = x.className.replace(" w3-show", "");
			}
		}
	</script>
</div>
<br /><br />
