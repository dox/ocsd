<?php
$filename = basename(__FILE__, '.php');

$sql  = "SELECT * FROM " . $filename;
$sql .= " WHERE cudid = '" . $person->cudid . "'";

$dbOutput = $db->query($sql, 'test', 'test')->fetchAll();
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
