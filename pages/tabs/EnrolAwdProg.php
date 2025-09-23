<?php
include __DIR__ . '/../../inc/autoload.php';

$person = new Person(filter_var($_GET['cudid'], FILTER_SANITIZE_STRING));

$results = $person->EnrolAwdProg()->all();

if (!empty($results)) {
      foreach ($results AS $result) {
            printArray($result);
      }  
} else {
        echo "No data";
}
?>