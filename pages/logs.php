<?php
$log->purge();
$logsAll = $log->getAll();

$data = array(
		'icon'		=> 'clock-history',
		'title'		=> 'Logs',
		'subtitle'	=> 'System logs'
);
echo pageTitle($data);

echo $log->table($logsAll);
?>