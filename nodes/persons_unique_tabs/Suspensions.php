<?php
include_once("../../includes/autoload.php");

$personObject = new Person($_GET['cudid']);
?>

<div class="tab-pane fade show active" id="Suspensions-tab-pane" role="tabpanel" aria-labelledby="Suspensions-tab" tabindex="0">
  <?php
  if ($personObject->isSuspended()) {
    echo "<div class=\"alert alert-danger text-center\" role=\"alert\">CURRENTLY SUSPENDED</div>";
  }
  ?>
  
   <ul>
   <?php
   foreach ($personObject->getSuspensions() AS $output) {
     if (!isset($output['SuspendReason'])) {
     $output['SuspendReason'] = "Reason Unknown";
     }
     echo "<li>" . date('Y-m-d', strtotime($output['SuspendStrDt'])) . " - " . date('Y-m-d', strtotime($output['SuspendExpEndDt'])) . " " . $output['SuspendReason'] . "</li>";
   }
   ?>
   </ul>
</div>