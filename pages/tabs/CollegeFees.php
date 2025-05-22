<?php
include __DIR__ . '/../../inc/autoload.php';

$sql  = "SELECT * FROM CollegeFees WHERE cudid = :cudid";

$results = $db->get($sql, ['cudid' => $_GET['cudid']]);

if (!empty($results)) {
      foreach ($results AS $result) {
              printArray($result);
      }  
} else {
        echo "No data";
}

?>