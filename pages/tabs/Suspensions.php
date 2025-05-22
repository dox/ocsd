<?php
include __DIR__ . '/../../inc/autoload.php';

$person = new Person(filter_var($_GET['cudid'], FILTER_SANITIZE_STRING));

if ($person->suspensions()->isCurrentlySuspended()) {
    echo "<div class=\"alert alert-danger text-center\" role=\"alert\">CURRENTLY SUSPENDED</div>";
}

$today = date('Ymd');

foreach ($person->suspensions()->all() AS $result) {
  printArray($result);
}
?>