<?php
$logsClass = new Logs();
//$logs = $logsClass->paginatedAll($from, $resultsPerPage);
$logs = $logsClass->all();

echo displayTitle("Logs", "Filter: All");
?>

<p>Logs older than <?php echo logs_retention . autoPluralise(" day", " days", logs_retention);?> are automatically deleted.</p>

<?php
  echo $logsClass->makeTable($logs);
?>
