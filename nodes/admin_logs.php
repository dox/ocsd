<?php
$logsClass = new Logs();
//$logs = $logsClass->paginatedAll($from, $resultsPerPage);

if (isset($_POST['logs_search'])) {
  $search = filter_var($_POST['logs_search'], FILTER_SANITIZE_STRING);
  $logs = $logsClass->allSearch($search);
} else {
  $logs = $logsClass->all();
}

echo displayTitle("Logs", "Filter: All");
?>

<p>Logs older than <?php echo logs_retention . autoPluralise(" day", " days", logs_retention);?> are automatically deleted.</p>

<form method="post" id="search" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="needs-validation" novalidate>
<div class="input-group my-3">
  <input type="text" class="form-control" id="logs_search" name="logs_search" placeholder="e.g. '{cudid:123}'" aria-label="Search Logs" aria-describedby="button-addon2" value="<?php if (isset($search)) { echo $search; } ?>">
  <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Search</button>
</div>
</form>
<?php
  echo $logsClass->makeTable($logs);
?>
