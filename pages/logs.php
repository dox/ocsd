<?php
$logsAll = $log->getAll();
?>

<h1><?php echo icon('clock-history', '1em'); ?> Logs</h1>
<?php
echo $log->table($logsAll);
?>