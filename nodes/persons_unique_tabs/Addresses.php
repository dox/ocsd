<?php
$addressTypesToDisplay = array('C' => 'Contact', 'T' => 'Term-Time', 'H' => 'Home');

foreach ($addressTypesToDisplay AS $addressType => $addressTypeName) {
  $address = $person->address($addressType);

  if (!empty($address)) {
    $navTabs[$addressTypeName] = $address;
  }
}
?>
<div class="card mb-3">
  <!-- Cards navigation -->
  <ul class="nav nav-pills">
    <?php
    foreach ($navTabs AS $addressTypeName => $address) {
      if ($addressTypeName == 'Contact') {
        $active = "active";
      } else {
        $active = "";
      }
      echo "<li class=\"nav-item\"><a href=\"#tab-addresses-" . $addressTypeName . "\" class=\"nav-link " . $active . "\" data-bs-toggle=\"tab\">" . $addressTypeName . "</a></li>";
    }
    ?>
    <!--<li class="nav-item"><a href="#tab-addresses-contact" class="nav-link active" data-bs-toggle="tab">Contact</a></li>
    <li class="nav-item"><a href="#tab-addresses-termtime" class="nav-link" data-bs-toggle="tab">Term-Time</a></li>
    <li class="nav-item"><a href="#tab-addresses-home" class="nav-link" data-bs-toggle="tab">Home</a></li>-->
  </ul>
  <div class="tab-content">
    <?php
    foreach ($navTabs AS $addressTypeName => $address) {
      if ($addressTypeName == 'Contact') {
        $active = "show active";
      } else {
        $active = "";
      }

      $output  = "<div id=\"tab-addresses-" . $addressTypeName . "\" class=\"tab-pane " . $active . "\">";
      $output .= "<div class=\"card-body\">";
      $output .= makeAddress($address);

      if ($addressTypeName == 'Contact') {
        foreach ($person->contactDetails() AS $contact) {
          //$contact['SubType']
          $output .= "<p><i class=\"fe fe-phone\"></i> " .  $contact['Value'] . "</p>";
        }

        if (isset($personJSON->internal_tel)) {
          $output .= "<p><i class=\"fe fe-phone\"></i> " . $personJSON->internal_tel . "</p>";
        }
      }
      $output .= "</div>";

      $output .= "<div class=\"card-footer\">";
      $mapsURL = "https://www.google.co.uk/maps/place/" . $address['Line1'] . "+" . $address['AddressCtryDesc'] . "+" . $address['PostCode'];
      $output .= "Updated: " . date('Y-m-d', strtotime($address['LastUpdateDt']));
      $output .= "<a href=\"" . $mapsURL . "\"><svg width=\"1em\" height=\"1em\" class=\"float-end\"><use xlink:href=\"images/icons.svg#geo\"/></svg></a>";
      $output .= "</div>";

      $output .= "</div>";

      echo $output;
    }
    ?>
  </div>
</div>




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
