<?php include_once("views/html_head.php"); ?>

<?php
$dir    = 'cron';
$files = scandir($dir);
?>
<body>
  <div class="page">
    <div class="content">
      <div class="container-xl d-flex flex-column justify-content-center">
        <div class="content">
          <!-- Page title -->
          <div class="page-header">
            <div class="row align-items-center">
              <div class="col-auto">
                <div class="page-pretitle">Filter: Unknown</div>
                <h2 class="page-title">CRON Tasks</h2>
              </div>
              <!-- Page title actions -->
              <div class="col-auto ml-auto d-print-none">
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
              </div>
            </div>
          </div>
          <?php
          if (isset($_GET['action'])) {
            if ($_GET['action'] == "run_all") {
              foreach ($files AS $file) {
                $extension = end(explode('.', $file));

                if ($extension == "php") {
                  echo "<h3><strong>Executing " . $file . "</strong></h3>";
                  include_once($dir . "/" . $file);
                }
              }
            } elseif ($_GET['action'] == "run_one" && isset($_GET['file'])) {
              echo "<h3><strong>Executing " . $_GET['file'] . "</strong></h3>";
              include_once($dir . "/" . $_GET['file']);
            }
          }
          ?>
          <div class="row">
            <h2>Files Directory:</h2>
            <table class="table card-table table-vcenter">
              <thead>
                <tr>
                  <th>Extension</th>
                  <th>File Name</th>
                  <th>Scheduled</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                foreach ($files AS $file) {
                  $extension = end(explode('.', $file));

                  if (!empty($extension)) {
                    if ($extension == "php") {
                      $scheduled = "<a href=\"#\" class=\"btn btn-sm btn-success\" role=\"button\">YES</a>";
                    } else {
                      $scheduled = "<a href=\"#\" class=\"btn btn-sm btn-secondary\" role=\"button\">NO</a>";
                    }
                    $output  = "<tr>";
                    $output .= "<td class=\"w-1\">" . $extension . "</td>";
                    $output .= "<td class=\"td-truncate\">" . $file . "</td>";
                    $output .= "<td class=\"text-nowrap text-muted\">" . $scheduled . "</td>";
                    $output .= "<td class=\"text-nowrap text-muted\"><a href=\"#\" id=\"" . $file . "\" class=\"btn btn-sm btn-secondary cron_run_task\" role=\"button\">Run</a></td>";
                    $output .= "</tr>";

                    echo $output;
                  }

                }
                ?>
              </tbody>
            </table>
          </div>
          <div class="row">
            <?php
            foreach ($files AS $file) {
              $extension = end(explode('.', $file));

              if ($extension == "php") {
                //echo "<h3><strong>Executing " . $file . "</strong></h3>";
                //include_once($dir . "/" . $file);
              }
            }
            ?>
            <div id="cron_results"></div>
          </div>
        </div>
      </div>
    </div>
  </body>
  </html>
