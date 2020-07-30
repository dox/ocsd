<?php
$filename = basename(__FILE__, '.php');

$sql  = "SELECT * FROM " . $filename;
$sql .= " WHERE cudid = '" . $person->cudid . "'";

$dbOutput = $db->query($sql)->fetchAll();
?>

<?php if ($dbOutput) { ?>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Applications</h3>
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
