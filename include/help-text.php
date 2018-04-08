<?php
require 'sql-connect.php';
$b64 = base64_encode($_SERVER['SCRIPT_URL']);
$sql = "SELECT title, help_text FROM help_text WHERE base64_code = '$b64' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
// output data of each row
  while($row = $result->fetch_assoc()) {
    ?>
    <div class="modal fade" id="helpMe" tabindex="-1" role="dialog" aria-labelledby="helpTitle" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header" style="background-color: #AE2573">
            <h5 class="modal-title text-light" id="helpTitle">Help: <?php echo $row['title']; ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <?php echo $row['help_text']; ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <?php
  }
} else {
  ?>
  <div class="alert alert-warning" role="alert">
    <?php echo "Page ID: $b64<br />No results for query: $sql<br />" . $conn->error; ?>
  </div>
  <?php
}
?>
