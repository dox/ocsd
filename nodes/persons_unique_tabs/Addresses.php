<?php
$contactDetails = $db->where ("cudid", $_GET['cudid']);
$contactDetails = $db->get("ContactDetails");
$addresses = $db->where ("cudid", $_GET['cudid']);
$addresses = $db->get("Addresses");

foreach ($contactDetails AS $contact) {
  $output  = "<div class=\"card\">";
  $output .= "<div class=\"card-body\">";
    $output .= "<strong>" . $contact['SubType'] . "</strong> " . $contact['Value'];
  $output .= "</div>";
  $output .= "</div>";

  echo $output;
}

foreach ($addresses AS $address) {
  if ($address["AddressTyp"] == "T") {
    $addressType = "Term-Time";
  } elseif ($address["AddressTyp"] == "C") {
    $addressType = "Contact";
  } elseif ($address["AddressTyp"] == "Z") {
    $addressType = "UNKNOWN";
  } elseif ($address["AddressTyp"] == "H") {
    $addressType = "Home";
  } else {
    $addressType = "Other";
  }
  $output  = "<div class=\"card\" style=\"width: 28rem;\">";
  $output .= "<div class=\"card-body\">";
  $output .= "<h5 class=\"card-title\">" . $addressType . "</h5>";
  $output .= "<h6 class=\"card-subtitle mb-2 text-muted\"><span class=\"badge badge-light\">Last updated: " . (date('Y-m-d', strtotime($address["LastUpdateDt"]))) . "</span></h6>";
  $output .= "<p class=\"card-text\">";
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
  $output .= "<ul class=\"list-group list-group-flush\">";
    if ($address["AddressEmail"]) { $output .= "<li class=\"list-group-item\"><strong>Email:</strong> " . $address['AddressEmail'] . "</li>"; }
    if ($address["TelNo"]) { $output .= "<li class=\"list-group-item\"><strong>Telephone:</strong> " . $address['TelNo'] . "</li>"; }
    if ($address["MobileNo"]) { $output .= "<li class=\"list-group-item\"><strong>Mobile:</strong> " . $address['MobileNo'] . "</li>"; }
  $output .= "</ul>";
  $output .= "<a href=\"https://www.google.co.uk/maps?q=" . $address["Line1"] . "," . $address["Line2"] . "," . $address["PostCode"] . "," . $address["County"] . "\" class=\"card-link\"><i class=\"fas fa-map-marker-alt\"></i> Google Maps</a>";
  $output .= "</div>";
  $output .= "</div>";

  echo $output;
}
?>

<?php
if (count($addresses) > 0) {
  $includeFile = true;
} else {
  $includeFile = false;
}
?>
