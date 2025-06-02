<?php
include __DIR__ . '/../../inc/autoload.php';

$person = new Person(filter_var($_GET['cudid'], FILTER_SANITIZE_STRING));

if ($person->suspensions()->isCurrentlySuspended()) {
  echo "<div class=\"alert alert-danger text-center\" role=\"alert\">CURRENTLY SUSPENDED UNTIL " . $person->suspensions()->currentSuspensionEndDate() . "</div>";
}

foreach ($person->suspensions()->all() AS $result) {
  $start = $result['SuspendStrDt'] ?? null;
  $end = $result['SuspendEndDt'] ?? $result['SuspendExpEndDt'] ?? null;
  $reason = $result['reason'] ?? "Unknown";
  
  echo "<div class=\"alert alert-dark text-center\" role=\"alert\"><span class=\"badge float-start text-bg-secondary\">Expired</span> Suspended " . date('Y-m-d', strtotime($start)) . " to " . date('Y-m-d', strtotime($end)) . " Reason: " . $reason . "</div>";
}
?>