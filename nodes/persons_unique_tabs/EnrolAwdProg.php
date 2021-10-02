<?php
$filename = basename(__FILE__, '.php');

$sql  = "SELECT * FROM " . $filename;
$sql .= " WHERE cudid = '" . $person->cudid . "'";

$dbOutput = $db->query($sql, 'test', 'test')->fetchAll();
?>

<?php if ($dbOutput) { ?>

<div class="col-6">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Enrollment Award Program</h5>
      <p class="card-text">
        <ul>
         <?php
         foreach ($dbOutput AS $output) {
           printArray($output);
           //echo "<li>" . $output['CoOwnDptDesc'] . " (" . $output['Code'] . ")</li>";
         }
         ?>
        </ul>
      </p>
    </div>
  </div>
</div>

<?php } ?>
