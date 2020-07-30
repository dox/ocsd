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

    <?php
    echo $logsClass->makeTable($logs);
    ?>
    
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
