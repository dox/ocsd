<?php
$ldapClass = new LDAPPerson();
$ldapTypes = $ldapClass->userAccountControlFlags();

$personClass = new Person();
$bodcardTypes = $personClass->bodcardTypes();
?>
<h2>LDAP Account Types</h2>
<?php
foreach ($ldapTypes AS $key => $value) {
  echo $ldapClass->useraccountcontrolbadge($key) . " " . $value . "<br />";
}
?>
<hr />
<h2>Bodcard Card Types</h2>
<?php
foreach ($bodcardTypes AS $key => $value) {
  echo $personClass->cardTypeBadge($key) . " " . $value . "<br />";
}
?>
