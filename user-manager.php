<?php
require 'include/login-check.php';
require 'functions/functions.php';
check_auth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include_once 'include/scripts.php'; ?>
  <!-- x-editable (bootstrap version) -->
  <link href="css/bootstrap-editable.css" rel="stylesheet"/>
  <script src="js/bootstrap-editable.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/locales/bootstrap-datepicker.en-GB.min.js" integrity="sha256-zWVLv9rjdSAUVWhtqJUdGV1O5ONXpXMEJsOkp7B2gZ4=" crossorigin="anonymous"></script>
</head>
<body>
  <?php
  require 'include/header.php';
  require 'include/sql-connect.php';
  ?>
  <div class="container-fluid">
    <table class="table table-striped">
      <tr>
        <th scope="col">Staff Number</th>
        <th scope="col">Forename</th>
        <th scope="col">Surname</th>
        <th scope="col">Email Address</th>
        <th scope="col">Access Rights</th>
        <th scope="col">Last Login</th>
        <th scope="col">Unlock Account</th>
        <th scope="col">Reset PIN</th>
        <th scope="col">Delete</th>
      </tr>
      <?php
      //select all users
      $sql = "SELECT * FROM users ORDER BY surname";
      $result = $conn->query($sql);

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
          $hash = hash('sha256', $row['staff_number']."-".$row['surname']."-".$row['id']."-".$salt); //create sha256 hash to delete a user
          if ($row['failed_login'] > 3) {
            $col = "class='table-warning'";
          } else {
            $col = NULL;
          }
					echo'<tr '.$col.'>
                <td class="xedit" data-type="text" data-clear="1" data-pk="'.$row['id'].'" data-name="staff_number">'.$row['staff_number'].'</td>
  							<td class="xedit" data-type="text" data-clear="1" data-pk="'.$row['id'].'" data-name="forename">'.$row['forename'].'</td>
                <td class="xedit" data-type="text" data-clear="1" data-pk="'.$row['id'].'" data-name="surname">'.$row['surname'].'</td>
                <td class="xedit" data-type="text" data-clear="1" data-pk="'.$row['id'].'" data-name="email">'.$row['email'].'</td>
                <td><button class="btn btn-outline-dark" data-toggle="modal" data-target="#roles'.$row['id'].'">Edit</button></td>
                <td>'.$row['last_login'].'</td>
                <td>Unlock</td>
                <td>Reset</td>
                <td>Delete</td></tr>';
          ?>
          <div id="roles<?php echo $row['id']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="roles_title_<?php echo $row['id']; ?>" aria-hidden="true">
  					<div class="modal-dialog" role="document">
  						<div class="modal-content">
      					<div class="modal-header">
  				        <h5 class="modal-title" id="roles_title_<?php echo $row['id']; ?>">Edit Permissions for <?php echo "{$row['forename']} {$row['surname']}"; ?></h5>
  				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
  				          <span aria-hidden="true">&times;</span>
  				        </button>
  				      </div>
  							<form method="post" name="form_roles<?php echo $row['id']; ?>" action="include/submit-users.php?id=<?php echo $row['id']; ?>">
  				      	<div class="modal-body">
  									<?php
                    $pages = "SELECT code, CONV(code,2,10) AS hrc, human_readable FROM page_permissions ORDER BY human_readable";
										$pagesResult = $conn->query($pages);

										if ($pagesResult->num_rows > 0) {
											while($pagesRow = $pagesResult->fetch_assoc()) {
												echo "<div class='row'><div class='col-sm'>{$pagesRow['human_readable']}</div>";
												$x = 0;
									      $return = 0;
									      $user = $row['page_access_level'];
									      $userbin = sprintf("%032b", $user);
									      $pagebin = sprintf("%032b", $pagesRow['code']);
									      $usersplit = str_split($userbin);
									      $pagesplit = str_split($pagebin);
									      foreach ($usersplit as $value) {
									        if ($value == $pagesplit[$x] AND $value == 1) {
									          $return++;
									        }
									        $x++;
									      }
												echo "<div class='col-sm'>";
									      if ($return == 1) {
									        echo "Y";
									      } elseif ($return > 1) {
									        echo "E#" . $return;
									      } else {
									        echo "N";
									      }
												echo "</div></div>";
											}
										}
                    ?>
  								</div>
  								<div class="modal-footer">
  									<button class="btn btn-primary" type="submit">Update</button>
  									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
  								</div>
  							</form>
  						</div>
  					</div>
  				</div>
          <?php
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
        url: 'live-update/live-update-db.php?db=users',
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
<?php include 'include/footer.php'; ?>
</body>
</html>
