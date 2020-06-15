<?php
$logs = new Logs();
$logs->purge();
$logsAll = $logs->all();
$logsCount = $db->count;
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2"><i class="fas fa-cogs"></i> Logs</h1>
	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="btn-group mr-2">
			<button type="button" class="btn btn-sm btn-outline-secondary">void</button>
			<button type="button" class="btn btn-sm btn-outline-secondary">void</button>
		</div>
		
		<button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"><span data-feather="calendar"></span>void</button>
	</div>
</div>

<table class="table table-sm table-striped">
	<thead>
		<tr>
			<th scope="col" style="width: 180px">Date</th>
			<th scope="col">Description</th>
			<th scope="col" style="width: 330px">CUDID</th>
			<th scope="col" style="width: 140px">Username</th>
			<th scope="col" style="width: 140px">ip</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($logsAll AS $log) {
			$log = new Log($log['uid']);
			echo $log->tableRow();
		}
		?>
	</tbody>
</table>