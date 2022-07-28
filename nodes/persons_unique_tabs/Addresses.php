<?php
include_once("../../includes/autoload.php");

$personObject = new Person($_GET['cudid']);

$sql  = "SELECT * FROM Addresses";
$sql .= " WHERE cudid = '" . $personObject->cudid . "'";

$dbOutput = $db->query($sql)->fetchAll();

foreach ($dbOutput AS $address) {
  //printArray($address);
  echo makeAddress($address);
}
?>


<?php
function makeAddress($address) {
  $output  = "<div class=\"card\" style=\"width: 18rem;\">";
  $output .= "<div class=\"card-body\">";
  $output .= "<h5 class=\"card-title\">Address Type: " . $address['AddressTyp'] . "</h5>";
  $output .= "<p class=\"card-text\">Last updated: " . date('Y-m-d', strtotime($address['LastUpdateDt'])) . " by " . $address['AddressEntity'] . "</p>";
  $output .= "</div>";
  
  $output .= "<ul class=\"list-group list-group-flush\">";
  $output .= "<li class=\"list-group-item\">";
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
  $output .= "</li>";
  
    if ($address["AddressEmail"]) { $output .= "<li class=\"list-group-item\">" . makeEmail($address["AddressEmail"]) . "</li>"; }
    if ($address["TelNo"]) { $output .= "<li class=\"list-group-item\">" . $address["TelNo"] . "</li>"; }
    if ($address["MobileNo"]) { $output .= "<li class=\"list-group-item\">" . $address["MobileNo"] . "</li>"; }
  $output .= "</ul>";
  $output .= "</div>";
  
  return $output;
}
?>