<?php
include_once("../../includes/autoload.php");

$personObject = new Person($_GET['cudid']);

$sql  = "SELECT * FROM CoOwningDepartments";
$sql .= " WHERE cudid = '" . $personObject->cudid . "'";

$dbOutput = $db->query($sql)->fetchAll();
?>

<?php if ($dbOutput) { ?>

<div class="card">
  <div class="card-body">
    <h5 class="card-title">Co-owning Departments</h5>
    <p class="card-text">
      <ul>
        <?php
        foreach ($dbOutput AS $output) {
          //printArray($output);
          echo "<li>" . $output['CoOwnDptDesc'] . " (" . $output['Code'] . " - " . $output['SCESequence'] . ")</li>";
        }
    
        ?>
        </ul>
    </p>
  </div>
</div>

<?php } ?>