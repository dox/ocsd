<?php
include_once("../inc/autoload.php");

sleep(3);

$logData = [
	'category' => 'cron',
	'result'   => 'success',
	'cudid'   => 'test',
	'ldap'   => 'test',
	'description' => 'Test cron task completed sucessfully'
];
$log->create($logData);
?>