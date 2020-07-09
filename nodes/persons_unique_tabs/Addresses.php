<?php
$c_address = $person->address("C");
$t_address = $person->address("T");
$h_address = $person->address("H");

if ($c_address || $c_address || $c_address) {
?>
<div class="card-tabs">
  <!-- Cards navigation -->
  <ul class="nav nav-tabs">
    <li class="nav-item"><a href="#tab-addresses-contact" class="nav-link active" data-toggle="tab">Contact</a></li>
    <li class="nav-item"><a href="#tab-addresses-termtime" class="nav-link" data-toggle="tab">Term-Time</a></li>
    <li class="nav-item"><a href="#tab-addresses-home" class="nav-link" data-toggle="tab">Home</a></li>
  </ul>
  <div class="tab-content">
    <!-- Content of card #1 -->
    <div id="tab-addresses-contact" class="card tab-pane show active">
      <div class="card-body">
        <p>
          <?php
          echo makeAddress($c_address);
          foreach ($person->contactDetails() AS $contact) {
            //$contact['SubType']
            echo "<p><i class=\"fe fe-phone\"></i> " .  $contact['Value'] . "</p>";
          }

          if (isset($personJSON->internal_tel)) {
            echo "<p><i class=\"fe fe-phone\"></i> " . $personJSON->internal_tel . "</p>";
          }
          ?>
        </p>
      </div>
      <div class="card-footer">
        <div class="row align-items-center">
          <div class="col-auto">
            Updated: <?php echo date('Y-m-d', strtotime($c_address['LastUpdateDt'])); ?>
          </div>
          <div class="col-auto ml-auto">
            <a href="">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="11" r="3"></circle><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 0 1 -2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z"></path></svg>
            </a>
          </div>
        </div>
      </div>
    </div>
    <!-- Content of card #2 -->
    <div id="tab-addresses-termtime" class="card tab-pane">
      <div class="card-body">
        <p><?php echo makeAddress($person->address("T")); ?></p>
      </div>
      <div class="card-footer">
        <div class="row align-items-center">
          <div class="col-auto">
            Updated: <?php echo date('Y-m-d', strtotime($t_address['LastUpdateDt'])); ?>
          </div>
          <div class="col-auto ml-auto">
            <a href="">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="11" r="3"></circle><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 0 1 -2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z"></path></svg>
            </a>
          </div>
        </div>
      </div>
    </div>
    <!-- Content of card #3 -->
    <div id="tab-addresses-home" class="card tab-pane">
      <div class="card-body">
        <p><?php echo makeAddress($person->address("H")); ?></p>
      </div>
      <div class="card-footer">
        <div class="row align-items-center">
          <div class="col-auto">
            Updated: <?php echo date('Y-m-d', strtotime($h_address['LastUpdateDt'])); ?>
          </div>
          <div class="col-auto ml-auto">
            <a href="">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="11" r="3"></circle><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 0 1 -2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z"></path></svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
}
?>


<?php
function makeAddress($address) {


  $output  = "<a href=\"" . $url . "\">" . $icon . "</a>";
    if ($address["Line1"]) { $output .= $address["Line1"] . "<br />"; }
    if ($address["Line2"]) { $output .= $address["Line2"] . "<br />"; }
    if ($address["Line3"]) { $output .= $address["Line3"] . "<br />"; }
    if ($address["Line4"]) { $output .= $address["Line4"] . "<br />"; }
    if ($address["Line5"]) { $output .= $address["Line5"] . "<br />"; }
    if ($address["City"]) { $output .= $address["City"] . "<br />"; }
    if ($address["PostCode"]) { $output .= $address["PostCode"] . "<br />"; }
    if ($address["State"]) { $output .= $address["State"] . "<br />"; }
    if ($address["County"]) { $output .= $address["County"] . "<br />"; }
    if ($address["AddressCtryDesc"]) { $output .= $address["AddressCtryDesc"] . "<br />"; }
  $output .= "<ul >";
    if ($address["AddressEmail"]) { $output .= "<li>" . makeEmail($address["AddressEmail"]) . "</li>"; }
    if ($address["TelNo"]) { $output .= "<li>" . $address["TelNo"] . "</li>"; }
    if ($address["MobileNo"]) { $output .= "<li >" . $address["MobileNo"] . "</li>"; }
  $output .= "</ul>";

  return $output;
}
?>
