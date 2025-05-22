<?php
include __DIR__ . '/../../inc/autoload.php';

$sql  = "SELECT * FROM Supervisors WHERE cudid = :cudid";

$results = $db->get($sql, ['cudid' => $_GET['cudid']]);

if (!empty($results)) {
      foreach ($results AS $output) {
        $outputCard  = "<div class=\"card mb-3\">";
        $outputCard .= "<div class=\"card-body\">";
        $outputCard .= "<div class=\"row row-sm align-items-center\">";
        $outputCard .= "<div class=\"col-auto\">";
        $outputCard .= "<span class=\"avatar avatar-lg\" style=\"background-image: url(/images/blank_avatar.png)\"></span>";
        $outputCard .= "</div>";
        $outputCard .= "<div class=\"col\">";
        $outputCard .= "<h4 class=\"card-title m-0\">";
        $outputCard .= "" . $output['SuperFullName'] . "";
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
        $outputCard .= "<a href=\"mailto:" . $output['SuperEmail'] . "\" class=\"btn btn-sm btn-info d-none d-md-inline-block\">Email</a>";
        $outputCard .= "</div>";
        $outputCard .= "</div>";
        $outputCard .= "</div>";
        $outputCard .= "</div>";
      
        echo $outputCard;
      } 
} else {
        echo "No data";
}

?>