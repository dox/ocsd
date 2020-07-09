<?php
$filename = basename(__FILE__, '.php');

$sql  = "SELECT * FROM " . $filename;
$sql .= " WHERE cudid = '" . $person->cudid . "'";

$dbOutput = $db->query($sql, 'test', 'test')->fetchAll();
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
      //printArray($output);
      echo "<li>" . $output['QualDesc'] . " (" . $output['QualCode'] . ") " . $output['ApprovedResult'] . "</li>";
      //     [ClaimedResult] =>
      //     [PredictedResult] =>
      //     [QualYear] =>
      //     [QualSitting] =>
      //     [AwdBody] =>
      //     [AwdBodyDesc] =>
      //     [AwdBodyDescUCAS] =>
    }
    ?>
    </ul>
  </div>
</div>

<?php } ?>
