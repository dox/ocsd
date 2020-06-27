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

		<div class="dropdown">
			<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-stream"></i> API</button>
			<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
				<form action="/api/log/read.php" method="post"><button type="submit" name="api_token" value="<?php echo api_token; ?>" class="dropdown-item">Read</button></form>
			</div>
		</div>
	</div>
</div>
<p>Logs older than <?php echo logs_retention . autoPluralise(" day", " days", logs_retention);?> are automatically deleted.</p>
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
