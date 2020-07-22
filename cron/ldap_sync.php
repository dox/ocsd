<?php
include_once("../includes/autoload.php");

// this checks each LDAP record against a matched CUD record
// if pager, or mail isn't correct in LDAP (based on the data in CUD)
// it will update the LDAP record
$ldapClass = new LDAP;

$users = $ldapClass->all_users_enabled();

foreach ($users AS $ldapUser) {
  $userdata = null;

  $personsClass = new Persons;
  $CUDPerson = $personsClass->search($ldapUser['samaccountname'][0], 2);
  if (!count($CUDperson) == 1) {
    $CUDPerson = $personsClass->search($ldapUser['mail'][0], 2);
  }

  if (isset($CUDPerson[0]['cudid']) == 1) {
    //echo "<p>Checking CUD/LDAP details match on " . $ldapUser['samaccountname'][0] . " with mail: " . $CUDPerson[0]['oxford_email'] . "</p>";

    if ($ldapUser['pager'][0] != $CUDPerson[0]['MiFareID']) {
      $userdata["pager"] = $CUDPerson[0]['MiFareID'];
    }
    if ($ldapUser['mail'][0] != $CUDPerson[0]['oxford_email']) {
      $userdata["mail"] = $CUDPerson[0]['oxford_email'];
    }
    # update names?

    if (count($userdata) > 0) {
      echo "<p>Actioning on " . $ldapUser['samaccountname'][0] . " (" . $ldapUser['cn'][0]  . ")</p>";
      printArray($userdata);
      $ldapClass->ldap_mod_replace($ldapUser['dn'], $userdata);
      echo "<hr />";
    }
  }
}
?>
