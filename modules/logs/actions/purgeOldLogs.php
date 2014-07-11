<?php
include_once("../../../engine/initialise.php");

$log = new Logs;

$log->purge_old_logs();

$log->notes			= "Logs purged";
$log->type			= "delete";
$log->create();
?>