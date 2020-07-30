<?php
$logsClass = new Logs();
$logs = $logsClass->allByUser($person->cudid, $person->sso_username);
?>

<div class="box">
  <div class="card">
    <?php
    echo $logsClass->makeTable($logs);
    ?>
  </div>
</div>
