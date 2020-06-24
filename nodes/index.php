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
			$statsPersonsTotals = $db->orderBy('date_created', "ASC");
			$statsPersonsTotals = $db->get('_stats', '7');

			foreach ($statsPersonsTotals AS $personTotal) {
				$personTotalArray[] = $personTotal['value'];
			}
			$personTotalArray = array_reverse($personTotalArray);
		?>
		<img src="../images/blank.svg" class="card-img-top" alt="...">
		<div class="card-body">
			<h5 class="card-title"><?php echo $personsCount; ?> Persons</h5>
			<p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
			<a href="#" class="btn btn-primary">Go somewhere</a>
		</div>
	</div>

	<div class="card" style="width: 18rem;">
		<?php
			$statsStudentTotals = $db->where('name', "student_rows_total");
			$statsStudentTotals = $db->orderBy('date_created', "ASC");
			$statsStudentTotals = $db->get('_stats', '7');


			foreach ($statsStudentTotals AS $studentTotal) {
				$studentTotalArray[] = $studentTotal['value'];
			}
			$studentTotalArray = array_reverse($studentTotalArray);
		?>
		<img src="../images/blank.svg" class="card-img-top" alt="...">
		<div class="card-body">
			<h5 class="card-title"><?php echo $studentsCount; ?> Students</h5>
			<p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
			<a href="#" class="btn btn-primary">Go somewhere</a>
		</div>
	</div>

	<div class="card" style="width: 18rem;">
		<?php
			$logonsAll = $db->where('type', "LOGON");
			$logonsAll = $db->get('_logs', '7');
			$logonsAllCount = $db->count;

			$logonsByDay = $db->rawQuery("SELECT DATE(date_created) AS date_created, COUNT(*) AS cnt FROM _logs WHERE type = 'LOGON' GROUP BY DATE(date_created) ORDER BY date_created ASC");
			foreach ($logonsByDay AS $day) {
				$logonsCountArray[] = $day['cnt'];
			}
			$logonsCountArray = array_reverse($logonsCountArray);
		?>
		<img src="../images/blank.svg" class="card-img-top" alt="...">
		<div class="card-body">
			<h5 class="card-title"><?php echo $logonsAllCount; ?> Logons</h5>
			<p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
			<a href="#" class="btn btn-primary">Go somewhere</a>
		</div>
	</div>
	<div class="card" style="width: 18rem;">
		<?php
			$logViewsAll = $db->where('type', "VIEW");
			$logViewsAll = $db->get('_logs');
			$logViewsAllCount = $db->count;

			$logViewsByDay = $db->rawQuery("SELECT DATE(date_created) AS date_created, COUNT(*) AS cnt FROM _logs WHERE type = 'VIEW' GROUP BY DATE(date_created) ORDER BY date_created DESC");
			foreach ($logViewsByDay AS $day) {
				$logViewsCountArray[] = $day['cnt'];
			}
			$logViewsCountArray = array_reverse(array_slice($logViewsCountArray, 0, 7));
		?>
		<img src="../images/blank.svg" class="card-img-top" alt="...">
		<div class="card-body">
			<h5 class="card-title"><?php echo $logViewsAllCount; ?> Views</h5>
			<p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
			<a href="#" class="btn btn-primary">Go somewhere</a>
		</div>
	</div>
</div>
