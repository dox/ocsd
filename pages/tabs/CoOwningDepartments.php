<?php
include __DIR__ . '/../../inc/autoload.php';

$sql  = "SELECT * FROM CoOwningDepartments WHERE cudid = :cudid";

$results = $db->get($sql, ['cudid' => $_GET['cudid']]);

if (!empty($results)) {
      $output  = "<ul class=\"list-group\">";
      
      foreach ($results AS $result) {
            $output .= "<li class=\"list-group-item d-flex justify-content-between align-items-start\">";
            $output .= "<div class=\"ms-2 me-auto\"><strong>" . $result['CoOwnDptCd'] . "</strong> ";
            $output .= $result['CoOwnDptDesc'];
            $output .= "</div><span class=\"badge text-bg-primary rounded-pill\">Seq: " . $result['SCESequence'] . "</span>";
            $output .= "</li>";
      }
      
      $output .= "</ul>";
      
      echo $output;
} else {
      echo "No data";
}

?>