<div class="content">
  <div class="container-xl">
    <!-- Page title -->
    <div class="page-header">
      <div class="row align-items-center">
        <div class="col-auto">
          <!-- Page pre-title -->
          <div class="page-pretitle">
            Overview
          </div>
          <h2 class="page-title">
            Logs
          </h2>
        </div>
        <!-- Page title actions -->
        <!--<div class="col-auto ml-auto d-print-none">
          <span class="d-none d-sm-inline">
            <a href="#" class="btn btn-white">
              New view
            </a>
          </span>
          <a href="#" class="btn btn-primary ml-3 d-none d-sm-inline-block" data-toggle="modal" data-target="#modal-report">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
            Create new report
          </a>
          <a href="#" class="btn btn-primary ml-3 d-sm-none btn-icon" data-toggle="modal" data-target="#modal-report" aria-label="Create new report">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
          </a>
        </div>-->
      </div>
    </div>
  </div>
</div>





<!--
          <div class="card">
  <div class="card-header">
    <h3 class="card-title">Timeline</h3>
  </div>
  <div class="card-body">
    <ul class="list list-timeline">
      <li>
        <div class="list-timeline-icon bg-twitter">SVG icon code
        </div>
        <div class="list-timeline-content">
          <div class="list-timeline-time">10 hrs ago</div>
          <p class="list-timeline-title">+1150 Followers</p>
          <p class="text-muted">You’re getting more and more followers, keep it up!</p>
        </div>
      </li>
      <li>
        <div class="list-timeline-icon bg-red">SVG icon code
        </div>
        <div class="list-timeline-content">
          <div class="list-timeline-time">2 hrs ago</div>
          <p class="list-timeline-title">+3 New Products were added!</p>
          <p class="text-muted">Congratulations!</p>
        </div>
      </li>
      <li>
        <div class="list-timeline-icon bg-success">SVG icon code
        </div>
        <div class="list-timeline-content">
          <div class="list-timeline-time">1 day ago</div>
          <p class="list-timeline-title">Database backup completed!</p>
          <p class="text-muted">Download the <a href="#">latest backup</a>.</p>
        </div>
      </li>
      <li>
        <div class="list-timeline-icon bg-facebook">SVG icon code
        </div>
        <div class="list-timeline-content">
          <div class="list-timeline-time">1 day ago</div>
          <p class="list-timeline-title">+290 Page Likes</p>
          <p class="text-muted">This is great, keep it up!</p>
        </div>
      </li>
      <li>
        <div class="list-timeline-icon bg-teal">SVG icon code
        </div>
        <div class="list-timeline-content">
          <div class="list-timeline-time">2 days ago</div>
          <p class="list-timeline-title">+3 Friend Requests</p>
          <div class="avatar-list mt-3">
            <span class="avatar" style="background-image: url(...)">
              <span class="badge bg-success"></span></span>
            <span class="avatar">
              <span class="badge bg-success"></span>JL</span>
            <span class="avatar" style="background-image: url(...)">
              <span class="badge bg-success"></span></span>
          </div>
        </div>
      </li>
      <li>
        <div class="list-timeline-icon bg-yellow">SVG icon code
        </div>
        <div class="list-timeline-content">
          <div class="list-timeline-time">3 days ago</div>
          <p class="list-timeline-title">+2 New photos</p>
          <div class="mt-3">
            <div class="row row-sm">
              <div class="col-6">
                <div class="media media-2x1 rounded">
                  <a class="media-content" style="background-image: url(...)"></a>
                </div>
              </div>
              <div class="col-6">
                <div class="media media-2x1 rounded">
                  <a class="media-content" style="background-image: url(...)"></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </li>
      <li>
        <div class="list-timeline-icon">SVG icon code
        </div>
        <div class="list-timeline-content">
          <div class="list-timeline-time">2 weeks ago</div>
          <p class="list-timeline-title">System updated to v2.02</p>
          <p class="text-muted">Check the complete changelog at the <a href="#">activity
              page</a>.</p>
        </div>
      </li>
    </ul>
  </div>
</div>
-->
<?php
$logsClass = new Logs();
$logs = $logsClass->all();
?>

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
		foreach ($logs AS $log) {
			$logDate = date('Y-m-d H:i:s', strtotime($log['date_created']));

      if ($log['result'] == "success") {
				$class = "table-success";
			} else if ($log['result'] == "warning") {
				$class = "table-warning";
			} else if ($log['result'] == "error") {
				$class = "table-danger";
			} else if ($log['result'] == "info") {
				$class = "table-primary";
			} else if ($log['result'] == "debug") {
				$class = "table-info";
			} else {
				$class = "";
			}

      if ($log['type'] == "ldap") {
				$badgeClass = "bg-indigo";
			} else if ($log['type'] == "logon" || $log['type'] == "logoff") {
				$badgeClass = "bg-green";
			} else if ($log['type'] == "view") {
				$badgeClass = "bg-lime";
			} else if ($log['type'] == "cron") {
				$badgeClass = "bg-blue";
			} else if ($log['type'] == "purge") {
				$badgeClass = "bg-pink";
			} else if ($log['type'] == "email") {
				$badgeClass = "bg-yellow";
			} else {
				$badgeClass = "";
			}

			if (in_array($_SESSION['username'], admin_usernames)) {
				$output  = "<tr class=\"" . $class . "\">";
				$output .= "<td>" . $logDate . " </td>";
				$output .= "<td>" . $log['description'] . " <span class=\"badge float-right " . $badgeClass . "\">" . $log['type'] . "</span></td>";

        if (!empty($log['cudid'])){
          $cudLink = "<a href=\"index.php?n=persons_unique&cudid=" . $log['cudid'] . "\">" . $log['cudid'] . "</a>";
        } else {
          $cudLink = "";
        }
        $output .= "<td>" . $cudLink . "</td>";

        if (!empty($log['username'])){
          $ldapLink = "<a href=\"index.php?n=ldap_unique&samaccountname=" . $log['username'] . "\">" . $log['username'] . "</a>";
        } else {
          $ldapLink = "";
        }
        $output .= "<td>" . $ldapLink . "</td>";
				$output .= "<td>" . $log['ip'] . "</td>";
				$output .= "</tr>";
			} else {
				$output  = "<tr class=\"blurry\">";
				$output .= "<td>" . generateRandomString($logDate) . " </td>";
				$output .= "<td>" . generateRandomString($log['description']) . " <span class=\"badge blurry badge-info float-right\">" . generateRandomString($log['type']) . "</span></td>";
				$output .= "<td>" . generateRandomString($log['cudid']) . "</td>";
				$output .= "<td>" . generateRandomString($log['username']) . "</td>";
				$output .= "<td>" . generateRandomString($log['ip']) . "</td>";
				$output .= "</tr>";
			}

			echo $output;
		}
		?>
	</tbody>
</table>
