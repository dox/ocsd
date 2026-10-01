<?php
$log->purge();
$logsPerPage = 100;
$logsAll = $log->getAll($logsPerPage + 1);
$hasMoreLogs = count($logsAll) > $logsPerPage;
if ($hasMoreLogs) array_pop($logsAll);

$data = array(
		'icon'		=> 'clock-history',
		'title'		=> 'Logs',
		'subtitle'	=> 'System logs'
);
echo pageTitle($data);

echo $log->table($logsAll);

if ($hasMoreLogs) {
	echo '<p class="text-center my-4"><a href="index.php?page=logs&amp;load_more=1&amp;offset=' . count($logsAll) . '" id="load-more-logs">Load more logs</a></p>';
}
?>
