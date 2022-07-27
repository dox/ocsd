<?php
include_once("../../includes/autoload.php");

$personObject = new Person($_GET['cudid']);

$sql  = "SELECT * FROM Addresses";
$sql .= " WHERE cudid = '" . $personObject->cudid . "'";

$dbOutput = $db->query($sql)->fetchAll();


$addressTypesToDisplay = array('C' => 'Contact', 'T' => 'Term-Time', 'H' => 'Home');

foreach ($dbOutput AS $address) {
  printArray($address);
  $output  = "<div class=\"card\">";
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
  
  echo $output;
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
