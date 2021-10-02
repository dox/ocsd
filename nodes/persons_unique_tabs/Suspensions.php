<?php if (!empty($person->getSuspensions())) { ?>

<div class="col-6">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Suspensions</h5>
      <p class="card-text">
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
      </p>
    </div>
  </div>
</div>

<?php } ?>
