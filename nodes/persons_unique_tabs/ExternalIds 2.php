<?php
include_once("../../includes/autoload.php");

$personObject = new Person($_GET['cudid']);

$sql  = "SELECT * FROM ExternalIds";
$sql .= " WHERE cudid = '" . $personObject->cudid . "'";

$dbOutput = $db->query($sql)->fetchAll();
?>

<?php if ($dbOutput) { ?>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">External IDs</h3>
  </div>
  <div class="card-body">
    <ul>
    <?php
    foreach ($dbOutput AS $output) {
      echo "<li>" . $output['ExtIdType'] . " = " . $output['ExtId'] . "</li>";
    }
    ?>
    </ul>
  </div>
</div>

<?php } ?>
