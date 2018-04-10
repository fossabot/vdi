<?php
//function to generate datalist for search box
function datalist($db, $col, $search, $post, $btn) {
  require '../include/sql-connect.php';
  $sql = "SELECT * FROM $db WHERE $col LIKE '%".$search."%' ORDER BY $col";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_array()) {
      ?><button type="button" class="btn btn-outline-<?php echo $btn; ?>" onclick="location.href='epcr.php?mode=view&type=<?php echo $post; ?>&id=<?php echo $row[0]; ?>'"><?php echo $row[1]; ?></button><?php
    }
  }
}

$q = $_GET['q']; //gets the live search information

if (strlen($q) > 0) {
	datalist('epcr_laptops', 'asset_tag', $q, 'tb', 'primary');
  datalist('epcr_incidents', 'incident_ref', $q, 'inc', 'dark');
  datalist('epcr_batteries', 'serial', $q, 'bat', 'info');
  datalist('vehicle_list', 'callsign', $q, 'loc', 'success');
} else {
	echo "Error: Live data not available";
	exit;
}
?>
