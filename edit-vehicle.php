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
  <div class="w3-container">
    <table class="w3-table-all w3-hoverable">
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

      //select all vehicles
			$sql = "SELECT * FROM vehicle_list ORDER BY callsign";
      $result = $conn->query($sql);

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
          if ($row['mot'] != 0 ) { $date_mot = date('d/m/Y', $row['mot']); } else { $date_mot = NULL; } //format MOT date correctly
          if ($row['service'] != 0 ) { $date_svc = date('d/m/Y', $row['service']); } else { $date_svc = NULL; } //format service date correctly

          //get vehicle type from db
          $veh_sql = "SELECT vehicle_type FROM vehicle_types WHERE id = " . $row['vehicle_type'];
          $veh_result = $conn->query($veh_sql);
          $veh_type = $veh_result->fetch_assoc();

          //get status from db
          $status_sql = "SELECT vehicle_status FROM vehicle_status WHERE id = " . $row['veh_status'];
          $status_result = $conn->query($status_sql);
          $status_type = $status_result->fetch_assoc();
					echo'<tr>
                <td class="xedit" data-type="text" data-clear="1" data-pk="'.$row['id'].'" data-name="callsign">'.$row['callsign'].'</td>
  							<td class="xedit" data-type="select" data-source="'.$types.'" data-pk="'.$row['id'].'" data-name="vehicle_type">'.$veh_type['vehicle_type'].'</td>
  							<td class="xedit" data-type="text" data-clear="1" data-pk="'.$row['id'].'" data-name="registration">'.$row['registration'].'</td>
                <td class="xedit" data-type="date" data-clear="1" data-pk="'.$row['id'].'" data-name="mot" data-format="dd-mm-yyyy" data-viewformat="dd/mm/yyyy">'.$date_mot.'</td>
                <td class="xedit" data-type="date" data-clear="1" data-pk="'.$row['id'].'" data-name="service" data-format="dd-mm-yyyy" data-viewformat="dd/mm/yyyy">'.$date_svc.'</td>
                <td class="xedit" data-type="select" data-source="'.$veh_status.'" data-pk="'.$row['id'].'" data-name="veh_status">'.$status_type['vehicle_status'].'</td>
                <td class="xedit" data-type="text" data-clear="1" data-pk="'.$row['id'].'" data-name="issi_hh1">'.$row['issi_hh1'].'</td>
                <td class="xedit" data-type="text" data-clear="1" data-pk="'.$row['id'].'" data-name="issi_hh2">'.$row['issi_hh2'].'</td>
                <td class="xedit" data-type="text" data-clear="1" data-pk="'.$row['id'].'" data-name="issi_veh">'.$row['issi_veh'].'</td>
              </tr>';
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

      /*$(document).on('click','.editable-submit',function(){
      var key = $(this).closest('.editable-container').prev().attr('key');
      var x = $(this).closest('.editable-container').prev().attr('id');
      var y = $('.input-sm').val();
      var z = $(this).closest('.editable-container').prev().text(y);

      $.ajax({
        url: "process.php?id="+x+"&data="+y+'&key='+key,
        type: 'GET',
        success: function(s){
        	if(s == 'status'){
          	$(z).html(y);}
        	if(s == 'error') {
          	alert('Error Processing your Request!');}
        },
        error: function(e){
        	alert('Error Processing your Request!!');
        }
      });
    });*/
    });
</script>
</body>
</html>
