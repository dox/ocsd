<?php
$ldapClass = new LDAPPerson();
$ldapTypes = $ldapClass->userAccountControlFlags();

$personClass = new Person();
$bodcardTypes = bodcardTypes();
?>
<h2>LDAP Account Types</h2>
<?php
foreach ($ldapTypes AS $key => $value) {
  echo "[" . $key . "] " . $value . "<br />";
}
?>
<hr />
<h2>Bodcard Card Types</h2>
<?php
foreach ($bodcardTypes AS $key => $value) {
  echo "[" . $key . "] " . $value . "<br />";
}
?>
