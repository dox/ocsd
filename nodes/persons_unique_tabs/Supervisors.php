<?php
$filename = basename(__FILE__, '.php');

$sql  = "SELECT * FROM " . $filename;
$sql .= " WHERE cudid = '" . $person->cudid . "'";

$dbOutput = $db->query($sql, 'test', 'test')->fetchAll();
?>

<?php if ($dbOutput) { ?>

<div class="card">
  <div class="card-header">
    <h3 class="card-title"><?php echo $filename; ?></h3>
  </div>
  <div class="card-body">
    <?php
    foreach ($dbOutput AS $output) {
      $outputCard  = "<div class=\"card\">";
      $outputCard .= "<div class=\"card-body\">";
      $outputCard .= "<div class=\"row row-sm align-items-center\">";
      $outputCard .= "<div class=\"col-auto\">";
      $outputCard .= "<span class=\"avatar avatar-lg\" style=\"background-image: url(./images/blank_avatar.png)\"></span>";
      $outputCard .= "</div>";
      $outputCard .= "<div class=\"col\">";
      $outputCard .= "<h4 class=\"card-title m-0\">";
      $outputCard .= "<a href=\"#\">" . $output['SuperFullName'] . "</a>";
      $outputCard .= "</h4>";
      $outputCard .= "<div class=\"text-muted\">";

      if (isset($output['SuperSrtDt'])) {
        $startDate = date('Y-m-d', strtotime($output['SuperSrtDt']));
      } else {
        $startDate = "";
      }

      if (isset($output['SuperEndDt'])) {
        $endDate = date('Y-m-d', strtotime($output['SuperEndDt']));
      } else {
        $endDate = "";
      }


      $outputCard .= $output['SuperTypeName'] . " <i>(" . $startDate . " - " . $endDate . ")</i>";
      $outputCard .= "</div>";
      $outputCard .= "<div class=\"small mt-1\">";
      $outputCard .= "<span class=\"text-success\">●</span> " . $output['SuperDeptName'];
      $outputCard .= "</div>";
      $outputCard .= "</div>";
      $outputCard .= "<div class=\"col-auto\">";
      $outputCard .= "<a href=\"mailto:" . $output['SuperEmail'] . "\" class=\"btn btn-sm btn-white d-none d-md-inline-block\">Email</a>";
      $outputCard .= "</div>";
      $outputCard .= "</div>";
      $outputCard .= "</div>";
      $outputCard .= "</div>";

      echo $outputCard;
    }
    ?>
  </div>
</div>

<?php } ?>
