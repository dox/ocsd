<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php
$logsClass = new Logs();
$personTotalArray = $logsClass->totalPersons();
?>


<?php
$sql = "SELECT * FROM _stats WHERE name = 'person_rows_total' ORDER BY date_created DESC LIMIT 7";
$statsPersonsTotals = $db->query($sql)->fetchAll();
foreach ($statsPersonsTotals AS $personTotal) {
	$personTotalArray["'" . date('Y-m-d', strtotime($personTotal['date_created'])) . "'"] = $personTotal['value'];
}
$personTotalArray = array_reverse($personTotalArray);


$sql = "SELECT * FROM _stats WHERE name = 'student_rows_total' ORDER BY date_created DESC LIMIT 7";
$statsStudentTotals = $db->query($sql)->fetchAll();
foreach ($statsStudentTotals AS $studentTotal) {
	$studentTotalArray["'" . date('Y-m-d', strtotime($studentTotal['date_created'])) . "'"] = $studentTotal['value'];
}
$studentTotalArray = array_reverse($studentTotalArray);


$sql = "SELECT * FROM _logs WHERE type = 'LOGON' ORDER BY date_created DESC LIMIT 7";
$logonsAll = $db->query($sql)->fetchAll();
$logonsAllCount = count($logonsAll);

$sql = "SELECT DATE(date_created) AS date_created, COUNT(*) AS cnt FROM _logs WHERE type = 'LOGON' GROUP BY DATE(date_created) ORDER BY date_created DESC LIMIT 7";
$logonsByDay = $db->query($sql)->fetchAll();

$logonsCountArray = array();
foreach ($logonsByDay AS $day) {
	$logonsCountArray["'" . date('Y-m-d', strtotime($day['date_created'])) . "'"] = $day['cnt'];
}

$sql = "SELECT * FROM _logs WHERE type = 'VIEW' ORDER BY date_created DESC";
$logViewsAll = $db->query($sql)->fetchAll();
$logViewsAllCount = count($logViewsAll);

$sql = "SELECT DATE(date_created) AS date_created, COUNT(*) AS cnt FROM _logs GROUP BY DATE(date_created) ORDER BY date_created DESC";
$logsByDay = $db->query($sql)->fetchAll();
foreach ($logsByDay AS $day) {
	$logsCountArray["'" . date('Y-m-d', strtotime($day['date_created'])) . "'"] = $day['cnt'];
}
$logsCountArray = array_reverse(array_slice($logsCountArray, 0, 7));

$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"images/icons.svg#bell\"/></svg> Test Button", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteMealModal\"");
$icons[] = array("class" => "btn-warning", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"images/icons.svg#email\"/></svg> Test Button", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteMealModal\"");

echo displayTitle("Dashboard", "Overview", $icons);
?>

<canvas id="totalPersons" width="100%"></canvas>
<canvas id="totalLogs" width="100%" height="20"></canvas>
<script>
const ctx = document.getElementById('totalPersons').getContext('2d');
const myChart = new Chart(ctx, {
	type: 'bar',
	data: {
		labels: [<?php echo implode(", ", array_keys($personTotalArray)); ?>],
		datasets: [{
			label: 'Total CUD Persons',
			data: [<?php echo implode(", ", $personTotalArray); ?>],
			borderWidth: 1
		}]
	},
	options: {
		scales: {
			y: {
				min: <?php echo min($personTotalArray)-1; ?>,
				max: <?php echo max($personTotalArray)+1; ?>
			}
		}
	}
});
</script>

<?php
$totalLogs = $logsClass->allByDay();
foreach ($totalLogs AS $log) {
	$totalLogsArray["'" . date('Y-m-d', strtotime($log['dateGroup'])) . "'"] = $log['totalCount'];
}
?>

<script>
const ctx2 = document.getElementById('totalLogs').getContext('2d');
const myChart2 = new Chart(ctx2, {
	type: 'line',
	data: {
		labels: [<?php echo implode(", ", array_keys($totalLogsArray)); ?>],
		datasets: [{
			label: 'Total Logs By Day',
			data: [<?php echo implode(", ", $totalLogsArray); ?>],
			borderWidth: 1
		}]
	},
	options: {
		scales: {
			y: {
				min: <?php echo min($totalLogsArray)-1; ?>,
				max: <?php echo max($totalLogsArray)+1; ?>
			}
		}
	}
});
</script>
