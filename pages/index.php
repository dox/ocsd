<div id="chart"></div>
<div id="chart-logs"></div>


<?php
$sql = "SELECT 
  s.date_created,
  s.value AS student_rows_total,
  p.value AS person_rows_total
FROM 
  _stats s
JOIN 
  _stats p ON s.date_created = p.date_created
WHERE 
  s.name = 'student_rows_total'
  AND p.name = 'person_rows_total'
ORDER BY 
  s.date_created DESC
LIMIT 30";
$statsPersonTotals = $db->query($sql);

$sqlLogs = "SELECT 
  DATE(date_created) AS log_date,
  COUNT(*) AS log_count
FROM 
  _logs
GROUP BY 
  DATE(date_created)
ORDER BY 
  log_date DESC
LIMIT 30";
$logsAll = $db->query($sqlLogs);

foreach ($statsPersonTotals AS $personTotal) {
	$studentTotalArray[date('Y-m-d', strtotime($personTotal['date_created']))] = $personTotal['student_rows_total'];
	$staffTotalArray[date('Y-m-d', strtotime($personTotal['date_created']))] = $personTotal['person_rows_total'] - $personTotal['student_rows_total'];
}
foreach ($logsAll AS $log) {
	$logsTotalArray[date('Y-m-d', strtotime($log['log_date']))] = $log['log_count'];
}
$studentTotalArray = array_reverse($studentTotalArray);
$staffTotalArray = array_reverse($staffTotalArray);
$logsTotalArray = array_reverse($logsTotalArray);
?>
<script>
var options = {
	series: [
		{
			name: 'Student Total',
			data: [<?php echo implode(", ", $studentTotalArray); ?>]
		},
		{
			name: 'Staff Total',
			data: [<?php echo implode(", ", $staffTotalArray); ?>],
			yAxisIndex: 1
		}
	],
	chart: {
		type: 'bar',
		height: 350,
		stacked: true
	},
	dataLabels: {
		enabled: false
	},
	xaxis: {
		categories: [<?php echo '"' . implode('", "', array_keys($studentTotalArray)) . '"'; ?>],
		labels: {
			formatter: function(value) {
				const date = new Date(value);
				const month = date.toLocaleString('default', { month: 'short' });
				const day = String(date.getDate()).padStart(2, '0');
				return `${month}-${day}`;
			}
		}
	},
	yaxis: [
		{
			title: {
				text: 'Totals'
			},
			min: <?php echo min($studentTotalArray) - 1; ?>,
			max: <?php echo max($studentTotalArray) + 1; ?>
		}
	]
};

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();

var options = {
	series: [
		{
			name: 'Logs',
			data: [<?php echo implode(", ", $logsTotalArray); ?>]
		}
	],
	chart: {
		type: 'line',
		height: 350,
		zoom: {
			enabled: false
		}
	},
	stroke: {
	  curve: 'smooth'
	},
	dataLabels: {
		enabled: false
	},
	xaxis: {
		categories: [<?php echo '"' . implode('", "', array_keys($logsTotalArray)) . '"'; ?>],
		labels: {
			formatter: function(value) {
				const date = new Date(value);
				const month = date.toLocaleString('default', { month: 'short' });
				const day = String(date.getDate()).padStart(2, '0');
				return `${month}-${day}`;
			}
		}
	},
	yaxis: [
		{
			title: {
				text: 'Logs'
			},
			min: <?php echo min($logsTotalArray) - 1; ?>,
			max: <?php echo max($logsTotalArray) + 1; ?>
		}
	]
};

var chart = new ApexCharts(document.querySelector("#chart-logs"), options);
chart.render();
</script>