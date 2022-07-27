<?php
include_once("../../includes/autoload.php");

$personObject = new Person($_GET['cudid']);

$sql  = "SELECT * FROM EnrolAwdProg";
$sql .= " WHERE cudid = '" . $personObject->cudid . "'";

$dbOutput = $db->query($sql)->fetchAll();
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
