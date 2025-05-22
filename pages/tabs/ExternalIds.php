<?php
include __DIR__ . '/../../inc/autoload.php';

$sql  = "SELECT * FROM ExternalIds WHERE cudid = :cudid";

$results = $db->get($sql, ['cudid' => $_GET['cudid']]);

if (!empty($results)) {
      $output  = "<ul class=\"list-group\">";

      foreach ($results AS $result) {
            $output .= "<li class=\"list-group-item\">";
            $output .= "<strong>" . $result['ExtIdType'] . "</strong> ";
            $output .= $result['ExtId'];
            
            if (!empty($result['ExtIdEndDt'])) {
                  $output .= " <i>(End date: " . date('Y-m-d' ,strtotime($result['ExtIdEndDt'])) . ")</i>";
            }
            $output .= "</li>";
      }
      
      $output .= "</ul>";
      
      echo $output;
} else {
        echo "No data";
}

?>
