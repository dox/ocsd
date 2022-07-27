<?php

?>

<p>Email logs within the last <?php echo $ageLimitDays . autoPluralise(" day", " days", $ageLimitDays);?>.  Please see <a href="index.php?n=admin_logs">Logs</a> for all logs.</p>
<?php
echo $logsClass->makeTable($logs);
?>
