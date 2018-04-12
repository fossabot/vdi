<pre>
  <?php
	echo "<h3>CODE TEST</h3><table class='table'>";
	$x = 0;
	$user = $_SESSION['access_level'];
	$page = 1;
	$userbin = sprintf("%032b", $user);
	$pagebin = sprintf("%032b", $page);
	echo "<tr><td>$userbin</td><td>$pagebin</td></tr>";
	$usersplit = str_split($userbin);
	$pagesplit = str_split($pagebin);
	foreach ($usersplit as $value) {
		if ($value == $pagesplit[$x] AND $value == 1) {
			echo "<tr class='table-success'><td>$value</td><td>{$pagesplit[$x]}</td></tr>";
		} else {
			echo "<tr><td>$value</td><td>{$pagebin[$x]}</td></tr>";
		}
		$x++;
	}
	echo "</table><br /><br />";
	echo "<h3>SERVER</h3>";
	print_r($_SERVER);
	echo "</table><br /><br />";
	//phpinfo();
  ?>
</pre>
