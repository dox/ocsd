<?php
$logsClass = new Logs();
//$logs = $logsClass->paginatedAll($from, $resultsPerPage);
$logs = $logsClass->all();
?>

<div class="content">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <div class="page-pretitle">Filter: All</div>
        <h2 class="page-title">Logs</h2>
      </div>
    </div>
  </div>

  <div class="row">
    <p>Logs older than <?php echo logs_retention . autoPluralise(" day", " days", logs_retention);?> are automatically deleted.</p>

    <?php
    echo $logsClass->makeTable($logs);
    ?>
  </div>
</div>
