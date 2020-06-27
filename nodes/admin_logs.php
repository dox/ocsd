<?php
$filter = array('api_token' => api_token, 'filter' => all);
$logsJSON = api_decode("log", "read", $filter);
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
		foreach ($logsJSON->body AS $log) {
			$logDate = date('Y-m-d H:i:s', strtotime($log->date_created));

			if ($log->result == "success") {
				$class = "table-success";
			} else if ($log->result == "warning") {
				$class = "table-warning";
			} else if ($log->result == "error") {
				$class = "table-danger";
			} else if ($log->result == "info") {
				$class = "table-primary";
			} else if ($log->result == "debug") {
				$class = "table-info";
			} else {
				$class = "";
			}

			if (in_array($_SESSION['username'], admin_usernames)) {
				$output  = "<tr class=\"" . $class . "\">";
				$output .= "<td>" . $logDate . " </td>";
				$output .= "<td>" . $log->description . " <span class=\"badge badge-info float-right\">" . $log->type . "</span></td>";
				$output .= "<td>" . "<a href=\"index.php?n=persons_unique&cudid=" . $log->cudid . "\">" . $log->cudid . "</a></td>";
				$output .= "<td>" . "<a href=\"index.php?n=ldap_unique&samaccountname=" . $log->username . "\">" . $log->username . "</a></td>";
				$output .= "<td>" . $log->ip . "</td>";
				$output .= "</tr>";
			} else {
				$output  = "<tr class=\"blurry\">";
				$output .= "<td>" . generateRandomString($logDate) . " </td>";
				$output .= "<td>" . generateRandomString($log->description) . " <span class=\"badge blurry badge-info float-right\">" . generateRandomString($log->type) . "</span></td>";
				$output .= "<td>" . generateRandomString($log->cudid) . "</td>";
				$output .= "<td>" . generateRandomString($log->username) . "</td>";
				$output .= "<td>" . generateRandomString($log->ip) . "</td>";
				$output .= "</tr>";
			}

			echo $output;
		}
		?>
	</tbody>
</table>
