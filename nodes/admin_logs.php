<?php
$resultsPerPage = 100;
if (isset($_GET['offset'])) {
  $from = $_GET['offset'];
  $to = $from + $resultsPerPage;
} else {
  $from = 0;
  $to = $from + $resultsPerPage;
}
$logsClass = new Logs();
$logsAll = $logsClass->all();
$logs = $logsClass->paginatedAll($from, $resultsPerPage);
?>

<div class="content">
  <!-- Page title -->
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col-auto">
        <div class="page-pretitle">Filter: <?php echo $from . " - " . $to; ?></div>
        <h2 class="page-title">Logs</h2>
      </div>
    </div>
  </div>

  <div class="row">
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

          if ($_SESSION['user_type'] == "Administrator") {
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
    <div>
      <?php
      $logsCounts = count($logsAll);
      $logsCountBlocks = ceil($logsCounts / $resultsPerPage);
      ?>
      <nav aria-label="...">
        <ul class="pagination justify-content-center">

          <?php
          if ($from < $resultsPerPage) {
            echo "<li class=\"page-item disabled\"><a class=\"page-link\" href=\"#\" tabindex=\"-1\" aria-disabled=\"true\">Previous</a></li>";
          } else {
            echo "<li class=\"page-item\"><a class=\"page-link\" href=\"./index.php?n=admin_logs&offset=" . ($from - 100) . "\" tabindex=\"-1\">Previous</a></li>";
          }

          $i = 1;
          do {
            $offset = ($i * $resultsPerPage) - $resultsPerPage;
            $url = "./index.php?n=admin_logs&offset=" . $offset;

            if ($_GET['offset'] == $offset) {
              echo "<li class=\"page-item active\"><a class=\"page-link\" href=\"" . $url . "\">" . $i . " <span class=\"sr-only\">(current)</span></a></li>";
            } else {
              echo "<li class=\"page-item\"><a class=\"page-link\" href=\"" . $url . "\">" . $i . "</a></li>";
            }

            $i++;
          } while ($i <= $logsCountBlocks);

          if ($from > (count($logsAll)-$resultsPerPage)) {
            echo "<li class=\"page-item disabled\"><a class=\"page-link\" href=\"#\" tabindex=\"-1\" aria-disabled=\"true\">Next</a></li>";
          } else {
            echo "<li class=\"page-item\"><a class=\"page-link\" href=\"./index.php?n=admin_logs&offset=" . ($from + $resultsPerPage) . "\" tabindex=\"-1\">Next</a></li>";
          }
          ?>

        </ul>
      </nav>
    </div>
</div>
