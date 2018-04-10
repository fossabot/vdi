<?php
require 'include/login-check.php';
require 'functions/functions.php';
check_auth(2);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include_once 'include/scripts.php'; ?>
  <script type="text/javascript">
  function showResult(str) {
    if (str.length==0) {
      document.getElementById("livesearch").innerHTML="";
      document.getElementById("livesearch").style.border="0px";
      return;
    }
    if (window.XMLHttpRequest) {
      // code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp=new XMLHttpRequest();
    } else {  // code for IE6, IE5
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
      if (this.readyState==4 && this.status==200) {
        document.getElementById("livesearch").innerHTML=this.responseText;
        document.getElementById("livesearch").style.border="1px solid #A5ACB2";
      }
    }
    xmlhttp.open("GET","live-update/epcr-search.php?q="+str,true);
    xmlhttp.send();
  }
  </script>
</head>
<body>
  <?php include 'include/header.php'; ?>
  <div class="container-fluid">
    <h3>ePCR Asset Tracking</h3>
    <label class="sr-only" for="search">Vehicle Search</label>
    <input class="form-control" placeholder="Search by asset tag, battery id, vehicle, service desk reference or datix reference..." id="search" type="text" onkeyup="showResult(this.value)">
    <div id="livesearch"></div>

    <?php
    require 'include/sql-connect.php';
    if ($_GET['mode'] === "view") { //script to view an asset
      $id = $_GET['id'];
      $table = $_GET['type'];
      //work out which table data is going to be pulled from
      if ($table === "tb") {
        $selector = "laptop_id";
      } elseif ($table === "loc") {
        $selector = "location_id";
      } elseif ($table === "bat") {
        $selector = "battery_id";
      } elseif ($table === "inc") {
        $selector = "incident_id";
      }
      //build SQL query
      $sqla = "SELECT epcr_log.id, epcr_laptops.asset_tag, epcr_laptops.version, epcr_batteries.serial, epcr_batteries.model_number, vehicle_list.callsign, epcr_actions.action, epcr_incidents.id AS helpdesk_id, epcr_incidents.incident_ref, epcr_log.dtg FROM epcr_log ";
			$sqlb = "LEFT JOIN epcr_laptops ON epcr_log.laptop_id = epcr_laptops.id LEFT JOIN epcr_batteries ON epcr_log.battery_id = epcr_batteries.id LEFT JOIN vehicle_list ON epcr_log.location_id = vehicle_list.id LEFT JOIN epcr_actions ON epcr_log.action_id = epcr_actions.id LEFT JOIN epcr_incidents ON epcr_log.incident_id = epcr_incidents.id ";
      $sqlc = "WHERE $selector = $id ORDER BY epcr_log.dtg DESC";
      $sql = $sqla.$sqlb.$sqlc;

      $result = $conn->query($sql);

      if (!$result) {
        die($sql . "<br />" . $conn->error);
      }

			if ($result->num_rows > 0) {
				?>
        <div class="card">
					<div class="card-header"><h5>Details</h5></div>
					<div class="card-body">
      				<table class="table" >
      					<tr>
      						<th scope="col">Toughbook</th>
      						<th scope="col">Laptop Mk</th>
      						<th scope="col">Battery S/N</th>
      						<th scope="col">Battery Model</th>
      						<th scope="col">Location</th>
                  <th scope="col">Status</th>
                  <th scope="col" data-toggle="tooltip" data-placement="bottom" title="Links only available from a trust computer">Service Desk</th>
                  <th scope="col">Date of Entry</th>
      					</tr>
      				<?php
      				// output data of each row
      				while($row = $result->fetch_assoc()) {
                echo "<tr>
                <td>{$row['asset_tag']}</td>
                <td>{$row['version']}</td>
                <td>{$row['serial']}</td>
                <td>{$row['model_number']}</td>
                <td>{$row['callsign']}</td>
                <td>{$row['action']}</td>
                <td><a href='http://eoe-nrsdesk01/supportdesk/?A=E&F=0&R={$row['incident_ref']}'>{$row['incident_ref']}</a></td>
                <td>{$row['dtg']}</td>
                </tr>";
              }
            } else {
              echo "<p>No results found...</p>";
              die($sql . "<br />" . $conn->error);
            }
            ?>
          </table>
        </div>
      </div>
      <?php
    } else {
      //something
    }
      ?>
  </div>
	<?php include 'include/footer.php'; ?>
</body>
</html>
