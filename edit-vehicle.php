<?php
require 'include/login-check.php';
require 'functions/functions.php';
check_auth(3); // 1 = all users, 2 = supervisor, 3 = DLO & 4 = admin
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include_once 'include/scripts.php'; ?>
  <!-- bootstrap -->
    <link href="https://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
    <script src="https://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>

    <!-- x-editable (bootstrap version) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap-editable/js/bootstrap-editable.min.js"></script>
    <script src="js/bootstrap-datepicker.en-GB.js"></script>
</head>
<body>
  <?php
  require 'include/header.php';
  require 'include/sql-connect.php';
  ?>
  <br /><br />
  <div class="w3-container">
    <table class="w3-table-all w3-hoverable">
      <?php
      if($_SESSION['role'] == 3) { ?>
        <tr><th colspan="3"></th><th class="w3-center w3-blue" colspan="3">Editable Fields</th><th colspan="3"></th></tr>
      <?php } ?>
      <tr>
        <th>Callsign</th>
        <th>Vehicle Type</th>
        <th>Registration</th>
        <th>MOT Due</th>
        <th>Service Due</th>
        <th>Vehicle Status</th>
        <th>ISSI HH1</th>
        <th>ISSI HH2</th>
        <th>ISSI VEH</th>
        <th>Delete</th>
      </tr>
      <?php
      //put vehicle types into x-editable array
      $sql = "SELECT * FROM vehicle_types";
      $result = $conn->query($sql);
      while($row = $result->fetch_assoc()) {
        if (!isset($types)) {
          $types = "{value: ".$row['id'].", text: '".$row['vehicle_type']."'}";
        } else {
          $types .= ",{value: ".$row['id'].", text: '".$row['vehicle_type']."'}";
        }
      }
      $types = "[".$types."]";

      //put status' into x-editable array
      $sql = "SELECT * FROM vehicle_status";
      $result = $conn->query($sql);
      while($row = $result->fetch_assoc()) {
        if (!isset($veh_status)) {
          $veh_status = "{value: ".$row['id'].", text: '".$row['vehicle_status']."'}";
        } else {
          $veh_status .= ",{value: ".$row['id'].", text: '".$row['vehicle_status']."'}";
        }
      }
      $veh_status = "[".$veh_status."]";

      if($_SESSION['role'] == 3) { //section for DLOs
        //select all vehicles
        $sqla = "SELECT vehicle_list.id, vehicle_list.callsign, vehicle_types.vehicle_type, vehicle_list.registration, IF(vehicle_list.mot = 0,NULL,FROM_UNIXTIME(vehicle_list.mot, '%d/%m/%Y')) AS mot, IF(vehicle_list.service = 0,NULL,FROM_UNIXTIME(vehicle_list.service, '%d/%m/%Y')) AS service,";
        $sqlb = "vehicle_status.vehicle_status, vehicle_list.issi_hh1, vehicle_list.issi_hh2, vehicle_list.issi_veh FROM vehicle_list";
        $sqlc = "LEFT JOIN vehicle_types ON vehicle_list.vehicle_type = vehicle_types.id LEFT JOIN vehicle_status ON vehicle_list.veh_status = vehicle_status.id WHERE hidden = 0 ORDER BY callsign ASC";
        $sql = "$sqla $sqlb $sqlc";
        $result = $conn->query($sql);

  			if ($result->num_rows > 0) {
  				while($row = $result->fetch_assoc()) {
            $hash = hash('sha256', $row['callsign']."-".$row['registration']."-".$row['id']."-".$salt); //create sha256 hash to remove a vehicle from display
  					echo'<tr>
                  <td>'.$row['callsign'].'</td>
    							<td>'.$row['vehicle_type'].'</td>
    							<td>'.$row['registration'].'</td>
                  <td class="xedit" data-type="date" data-clear="1" data-pk="'.$row['id'].'" data-name="mot" data-format="dd-mm-yyyy" data-viewformat="dd/mm/yyyy">'.$row['mot'].'</td>
                  <td class="xedit" data-type="date" data-clear="1" data-pk="'.$row['id'].'" data-name="service" data-format="dd-mm-yyyy" data-viewformat="dd/mm/yyyy">'.$row['service'].'</td>
                  <td class="xedit" data-type="select" data-source="'.$veh_status.'" data-pk="'.$row['id'].'" data-name="veh_status">'.$row['vehicle_status'].'</td>
                  <td>'.$row['issi_hh1'].'</td>
                  <td>'.$row['issi_hh2'].'</td>
                  <td>'.$row['issi_veh'].'</td>
                  <td><button class="w3-button" onclick="deleteRecord(`'.$hash.'`,`'.$row['callsign'].'`)"><i class="fas fa-minus-circle"></i></button></td>
                </tr>';
  			    }
          }
        } elseif($_SESSION['role'] == 4) { //section for admin
          //select all vehicles
          $sqla = "SELECT vehicle_list.id, vehicle_list.callsign, vehicle_types.vehicle_type, vehicle_list.registration, IF(vehicle_list.mot = 0,NULL,FROM_UNIXTIME(vehicle_list.mot, '%d/%m/%Y')) AS mot, IF(vehicle_list.service = 0,NULL,FROM_UNIXTIME(vehicle_list.service, '%d/%m/%Y')) AS service,";
          $sqlb = "vehicle_status.vehicle_status, vehicle_list.issi_hh1, vehicle_list.issi_hh2, vehicle_list.issi_veh, vehicle_list.hidden FROM vehicle_list";
          $sqlc = "LEFT JOIN vehicle_types ON vehicle_list.vehicle_type = vehicle_types.id LEFT JOIN vehicle_status ON vehicle_list.veh_status = vehicle_status.id ORDER BY hidden ASC, callsign ASC";
          $sql = "$sqla $sqlb $sqlc";
          $result = $conn->query($sql);

    			if ($result->num_rows > 0) {
    				while($row = $result->fetch_assoc()) {
              $hash = hash('sha256', $row['callsign']."-".$row['registration']."-".$row['id']."-".$salt); //create sha256 hash to remove a vehicle from display
              if ($row['hidden'] != 0) {
                $col = "class='w3-orange'";
              } else {
                $col = NULL;
              }
    					echo'<tr '.$col.'>
                    <td class="xedit" data-type="text" data-clear="1" data-pk="'.$row['id'].'" data-name="callsign">'.$row['callsign'].'</td>
      							<td class="xedit" data-type="select" data-source="'.$types.'" data-pk="'.$row['id'].'" data-name="vehicle_type">'.$row['vehicle_type'].'</td>
      							<td class="xedit" data-type="text" data-clear="1" data-pk="'.$row['id'].'" data-name="registration">'.$row['registration'].'</td>
                    <td class="xedit" data-type="date" data-clear="1" data-pk="'.$row['id'].'" data-name="mot" data-format="dd-mm-yyyy" data-viewformat="dd/mm/yyyy">'.$row['mot'].'</td>
                    <td class="xedit" data-type="date" data-clear="1" data-pk="'.$row['id'].'" data-name="service" data-format="dd-mm-yyyy" data-viewformat="dd/mm/yyyy">'.$row['service'].'</td>
                    <td class="xedit" data-type="select" data-source="'.$veh_status.'" data-pk="'.$row['id'].'" data-name="veh_status">'.$row['vehicle_status'].'</td>
                    <td class="xedit" data-type="text" data-clear="1" data-pk="'.$row['id'].'" data-name="issi_hh1">'.$row['issi_hh1'].'</td>
                    <td class="xedit" data-type="text" data-clear="1" data-pk="'.$row['id'].'" data-name="issi_hh2">'.$row['issi_hh2'].'</td>
                    <td class="xedit" data-type="text" data-clear="1" data-pk="'.$row['id'].'" data-name="issi_veh">'.$row['issi_veh'].'</td>';

                if ($row['hidden'] == 1) { //if hidden allow to add
                  echo '<td><button class="w3-button" onclick="restoreRecord(`'.$hash.'`,`'.$row['callsign'].'`)"><i class="fas fa-plus-circle"></i></button></td>';
                } elseif ($row['hidden'] == 0) { //if showing allow to remove
                  echo '<td><button class="w3-button" onclick="deleteRecord(`'.$hash.'`,`'.$row['callsign'].'`)"><i class="fas fa-minus-circle"></i></button></td>';
                }
                echo "</tr>";
              }
  			    }
          }
			?>
    </table>
  </div>
  <script type="text/javascript">
    jQuery(document).ready(function() {
      //toggle popup or inline mode
      $.fn.editable.defaults.mode = 'popup';

      //make any element with class=xedit editable
      $('.xedit').editable({
        url: 'live-update/live-update-db.php?db=vehicle_list',
        placement: 'right'
      });

      //automatically show next editable
      $('.xedit').on('save.newuser', function(){
          var that = this;
          setTimeout(function() {
              $(that).closest('td').next().find('.xedit').editable('show');
          }, 200);
      });

      //save button click
      $('#save-btn').click(function() {
        $('.xedit').editable('submit', {
        url: '/newuser',
        ajaxOptions: {
          dataType: 'json' //assuming json response
        },
        success: function(data, config) {
          if(data && data.id) {  //record created, response like {"id": 2}
            //set pk
            $(this).editable('option', 'pk', data.id);
            //remove unsaved class
            $(this).removeClass('editable-unsaved');
            //show messages
            var msg = 'New user created! Now editables submit individually.';
            $('#msg').addClass('alert-success').removeClass('alert-error').html(msg).show();
            $('#save-btn').hide();
            $(this).off('save.newuser');
          } else if(data && data.errors){
            //server-side validation error, response like {"errors": {"username": "username already exist"} }
            config.error.call(this, data.errors);
          }
        },
        error: function(errors) {
          var msg = '';
          if(errors && errors.responseText) { //ajax error, errors = xhr object
            msg = errors.responseText;
          } else { //validation error (client-side or server-side)
            $.each(errors, function(k, v) { msg += k+": "+v+"<br>"; });
          }
          $('#msg').removeClass('alert-success').addClass('alert-error').html(msg).show();
        }
        });
      });
    });

    //delete confirmation
    function deleteRecord(hash,cs) {
      if (confirm('Are you sure you want to delete '+cs+'?')) {
        location.href='include/edit-hide-vehicle.php?f=' + hash;
      }
    }
    function restoreRecord(hash,cs) {
      if (confirm('Are you sure you want to restore '+cs+'?')) {
        location.href='include/edit-hide-vehicle.php?r&f=' + hash;
      }
    }
</script>
</body>
</html>
