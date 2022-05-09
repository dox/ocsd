<?php if (!empty($person->getSuspensions())) { ?>

<div class="card">
  <h4 class="card-header">Suspensions</h4>
  
  <div class="card-body">
  <?php
    if ($person->isSuspended()) {
      echo "<div class=\"alert alert-danger text-center\" role=\"alert\">CURRENTLY SUSPENDED</div>";
    }
    ?>
    
   <ul>
     <?php
     foreach ($person->getSuspensions() AS $output) {
       if (!isset($output['SuspendReason'])) {
         $output['SuspendReason'] = "Reason Unknown";
       }
       echo "<li>" . date('Y-m-d', strtotime($output['SuspendStrDt'])) . " - " . date('Y-m-d', strtotime($output['SuspendExpEndDt'])) . " " . $output['SuspendReason'] . "</li>";
     }
     ?>
     </ul>
  </div>
</div>

<?php } ?>