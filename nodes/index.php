<?php
$students = $db->get ("Student");
$studentsCount = $db->count;
$persons = $db->get ("Person");
$personsCount = $db->count;
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2"><i class="fas fa-home"></i> Dashboard</h1>
	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="btn-group mr-2">
			<button type="button" class="btn btn-sm btn-outline-secondary">void</button>
			<button type="button" class="btn btn-sm btn-outline-secondary">void</button>
		</div>

		<button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"><span data-feather="calendar"></span>void</button>
	</div>
</div>

<h2>Quick stats</h2>

<div class="row">
	<div class="card" style="width: 18rem;">
		<?php
			$statsPersonsTotals = $db->where('name', "person_rows_total");
			$statsPersonsTotals = $db->orderBy('date_created', "DESC");
			$statsPersonsTotals = $db->get('_stats', '7');

			foreach ($statsPersonsTotals AS $personTotal) {
				$personTotalArray["'" . date('Y-m-d', strtotime($personTotal['date_created'])) . "'"] = $personTotal['value'];
			}

			$personTotalArray = array_reverse($personTotalArray);
		?>
		<canvas id="personsChart" width="200" height="100"></canvas>
		<div class="card-body">
			<h5 class="card-title"><?php echo $personsCount; ?> Persons</h5>
		</div>
	</div>

	<div class="card" style="width: 18rem;">
		<?php
			$statsStudentTotals = $db->where('name', "student_rows_total");
			$statsStudentTotals = $db->orderBy('date_created', "DESC");
			$statsStudentTotals = $db->get('_stats', '7');

			foreach ($statsStudentTotals AS $studentTotal) {
				$studentTotalArray["'" . date('Y-m-d', strtotime($studentTotal['date_created'])) . "'"] = $studentTotal['value'];
			}

			$studentTotalArray = array_reverse($studentTotalArray);
		?>
		<canvas id="studentsChart" width="200" height="100"></canvas>
		<div class="card-body">
			<h5 class="card-title"><?php echo $studentsCount; ?> Students</h5>
		</div>
	</div>

	<div class="card" style="width: 18rem;">
		<?php
			$logonsAll = $db->where('type', "LOGON");
			$logonsAll = $db->get('_logs', '7');
			$logonsAllCount = $db->count;

			$logonsByDay = $db->rawQuery("SELECT DATE(date_created) AS date_created, COUNT(*) AS cnt FROM _logs WHERE type = 'LOGON' GROUP BY DATE(date_created) ORDER BY date_created DESC");
			foreach ($logonsByDay AS $day) {
				$logonsCountArray["'" . date('Y-m-d', strtotime($day['date_created'])) . "'"] = $day['cnt'];
			}
			$logonsCountArray = array_reverse($logonsCountArray);
		?>
		<canvas id="logonsChart" width="200" height="100"></canvas>
		<div class="card-body">
			<h5 class="card-title"><?php echo $logonsAllCount; ?> Logons</h5>
		</div>
	</div>
	<div class="card" style="width: 18rem;">
		<?php
			$logViewsAll = $db->where('type', "VIEW");
			$logViewsAll = $db->get('_logs');
			$logViewsAllCount = $db->count;

			$logViewsByDay = $db->rawQuery("SELECT DATE(date_created) AS date_created, COUNT(*) AS cnt FROM _logs WHERE type = 'VIEW' GROUP BY DATE(date_created) ORDER BY date_created DESC");
			foreach ($logViewsByDay AS $day) {
				$logViewsCountArray["'" . date('Y-m-d', strtotime($day['date_created'])) . "'"] = $day['cnt'];
			}
			$logViewsCountArray = array_reverse(array_slice($logViewsCountArray, 0, 7));
		?>
		<canvas id="viewsChart" width="200" height="100"></canvas>
		<div class="card-body">
			<h5 class="card-title"><?php echo $logViewsAllCount; ?> Views</h5>
		</div>
	</div>
</div>

<script>
var ctx_persons = document.getElementById('personsChart').getContext('2d');
var ctx_students = document.getElementById('studentsChart').getContext('2d');
var ctx_logons = document.getElementById('logonsChart').getContext('2d');
var ctx_views = document.getElementById('viewsChart').getContext('2d');

var personsChart = new Chart(ctx_persons, {
	type: 'bar',
	data: {
		labels: [<?php echo implode(array_keys($personTotalArray), ", "); ?>],
		datasets: [{
			label: 'Total',
			data: [<?php echo implode($personTotalArray, ", "); ?>],
			backgroundColor: 'rgba(54, 162, 235, 1)',
			borderColor: 'rgba(54, 162, 235, 1)',
			borderWidth: 1
		}]
	},
	options: {
		legend: {
			display: false
		},
		scales: {
			xAxes: [{
				display: false
			}],
			yAxes: [{
				display: false,
				ticks: {
					min: <?php echo min($personTotalArray)*0.999; ?>
				}
			}]
		}
	}
});

var studentsChart = new Chart(ctx_students, {
	type: 'bar',
	data: {
		labels: [<?php echo implode(array_keys($studentTotalArray), ", "); ?>],
		datasets: [{
			label: 'Total',
			data: [<?php echo implode($studentTotalArray, ", "); ?>],
			backgroundColor: 'rgba(54, 162, 235, 1)',
			borderColor: 'rgba(54, 162, 235, 1)',
			borderWidth: 1
		}]
	},
	options: {
		legend: {
			display: false
		},
		scales: {
			xAxes: [{
				display: false
			}],
			yAxes: [{
				display: false,
				ticks: {
					min: <?php echo min($studentTotalArray)*0.999; ?>
				}
			}]
		}
	}
});

var logonsChart = new Chart(ctx_logons, {
	type: 'bar',
	data: {
		labels: [<?php echo implode(array_keys($logonsCountArray), ", "); ?>],
		datasets: [{
			label: 'Total',
			data: [<?php echo implode($logonsCountArray, ", "); ?>],
			backgroundColor: 'rgba(54, 162, 235, 1)',
			borderColor: 'rgba(54, 162, 235, 1)',
			borderWidth: 1
		}]
	},
	options: {
		legend: {
			display: false
		},
		scales: {
			xAxes: [{
				display: false
			}],
			yAxes: [{
				display: false,
				ticks: {
					min: <?php echo min($logonsCountArray)*0.999; ?>
				}
			}]
		}
	}
});

var viewsChart = new Chart(ctx_views, {
	type: 'bar',
	data: {
		labels: [<?php echo implode(array_keys($logViewsCountArray), ", "); ?>],
		datasets: [{
			label: 'Total',
			data: [<?php echo implode($logViewsCountArray, ", "); ?>],
			backgroundColor: 'rgba(54, 162, 235, 1)',
			borderColor: 'rgba(54, 162, 235, 1)',
			borderWidth: 1
		}]
	},
	options: {
		legend: {
			display: false
		},
		scales: {
			xAxes: [{
				display: false
			}],
			yAxes: [{
				display: false,
				ticks: {
					min: <?php echo min($logViewsCountArray)*0.999; ?>
				}
			}]
		}
	}
});
</script>
