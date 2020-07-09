<?php
$filename = basename(__FILE__, '.php');

$sql  = "SELECT * FROM " . $filename;
$sql .= " WHERE cudid = '" . $person->cudid . "'";
$sql .= " ORDER BY SuspendSeq DESC";

$dbOutput = $db->query($sql)->fetchAll();


$sqlCurrent  = "SELECT * FROM " . $filename;
$sqlCurrent .= " WHERE cudid = '" . $person->cudid . "'";
$sqlCurrent .= " AND DATE(SuspendStrDt) < '" . date('Y-m-d') . "'";
$sqlCurrent .= " AND DATE(SuspendExpEndDt) > '" . date('Y-m-d') . "'";

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
      echo "<div class=\"alert alert-danger text-center\" role=\"alert\">CURRENTLY SUSPENDED UNTIL " . date('Y-m-d', strtotime($currentSuspension['SuspendExpEndDt'])) . "</div>";
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
