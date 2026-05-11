<?php
include __DIR__ . '/../../inc/autoload.php';
requireLogin();

$person = new Person(trim((string)($_GET['cudid'] ?? '')));

$results = $person->EnrolAwdProg()->all();

if (!empty($results)) {
      foreach ($results AS $result) {
            printArray($result);
      }  
} else {
        echo "No data";
}
?>
