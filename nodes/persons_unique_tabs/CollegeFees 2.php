<?php
include_once("../../includes/autoload.php");

$personObject = new Person($_GET['cudid']);

$sql  = "SELECT * FROM CollegeFees";
$sql .= " WHERE cudid = '" . $personObject->cudid . "'";

$dbOutput = $db->query($sql)->fetchAll();
?>

<?php if ($dbOutput) { ?>

<div class="card">
  <div class="card-header">
    <h3 class="card-title"><?php echo $filename; ?></h3>
  </div>
  <div class="card-body">
    <ul>
    <?php
    foreach ($dbOutput AS $output) {
      printArray($output);
      //echo "<li>" . $output['CoOwnDptDesc'] . " (" . $output['Code'] . ")</li>";
    }
    ?>
    </ul>
  </div>
</div>

<?php } ?>
