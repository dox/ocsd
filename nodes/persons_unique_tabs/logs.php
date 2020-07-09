<?php
$logsClass = new Logs();
$logs = $logsClass->allByUser($person->cudid, $person->sso_username);
?>

<div class="box">
  <div class="card">
    <div class="table-responsive">
      <table class="table table-vcenter table-mobile-md card-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Description</th>
            <th>CUDID</th>
            <th>Username</th>
            <th class="w-1">IP</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($logs AS $log) {
            $logDate = date('Y-m-d H:i:s', strtotime($log['date_created']));

      			if ($log->result == "success") {
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

      			if (in_array($_SESSION['username'], admin_usernames)) {
      				$output  = "<tr class=\"" . $class . "\">";
      				$output .= "<td>" . $logDate . " </td>";
      				$output .= "<td>" . $log['description'] . " <span class=\"badge badge-info float-right\">" . $log['type'] . "</span></td>";
      				$output .= "<td>" . "<a href=\"index.php?n=persons_unique&cudid=" . $log['cudid'] . "\">" . $log['cudid'] . "</a></td>";
      				$output .= "<td>" . "<a href=\"index.php?n=ldap_unique&samaccountname=" . $log['username'] . "\">" . $log['username'] . "</a></td>";
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
    </div>
  </div>
</div>






  </tbody>
</table>
