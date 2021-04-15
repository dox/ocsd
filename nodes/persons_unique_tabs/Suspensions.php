<?php

$filename = basename(__FILE__, '.php');

$sql  = "SELECT * FROM " . $filename;
$sql .= " WHERE cudid = '" . $person->cudid . "'";
$sql .= " ORDER BY SuspendSeq DESC";

$dbOutput = $db->query($sql)->fetchAll();

$sqlCurrent  = "SELECT * FROM " . $filename;
$sqlCurrent .= " WHERE cudid = '" . $person->cudid . "'";
$sqlCurrent .= " AND (DATE(SuspendStrDt) < '" . date('Y-m-d') . "'";
$sqlCurrent .= " AND DATE(SuspendExpEndDt) > '" . date('Y-m-d') . "' AND SuspendEndDt IS null) OR (cudid = '" . $person->cudid . "' AND SuspendEndDt IS null)";

$currentSuspension = $db->query($sqlCurrent)->fetchArray();
?>

<?php if ($dbOutput) { ?>

<div class="card">
  <div class="card-header">
    <h3 class="card-title"><?php echo $filename; ?></h3>
  </div>
  <div class="card-body">
    <?php
    if ($currentSuspension) {
      if (!empty($currentSuspension['SuspendEndDt'])) {
        $suspensionEndDate = date('Y-m-d', strtotime($currentSuspension['SuspendEndDt']));
      } else {
        $suspensionEndDate = date('Y-m-d', strtotime($currentSuspension['SuspendExpEndDt']));
      }
      echo "<div class=\"alert alert-danger text-center\" role=\"alert\">CURRENTLY SUSPENDED UNTIL " . $suspensionEndDate . "</div>";
    }
    ?>
    <ul>
    <?php
    foreach ($dbOutput AS $output) {
      if (!isset($output['SuspendReason'])) {
        $output['SuspendReason'] = "Reason Unknown";
      }
      echo "<li>" . date('Y-m-d', strtotime($output['SuspendStrDt'])) . " - " . date('Y-m-d', strtotime($output['SuspendExpEndDt'])) . " " . $output['SuspendReason'] . "</li>";
    }
    ?>
    </ul>
  </div>
</div>

<?php } ?>
