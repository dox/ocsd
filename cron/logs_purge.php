<?php
include_once("../includes/autoload.php");

$logsClass = new Logs();
$logsPurge = $logsClass->purge();
?>
