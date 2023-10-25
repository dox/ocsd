<?php
include_once("../includes/autoload.php");

// this checks each LDAP record against a matched CUD record
// if pager, or mail isn't correct in LDAP (based on the data in CUD)
// it will update the LDAP record
$ldapClass = new LDAP;

$users = $ldapClass->all_users_enabled();
$personsClass = new Persons;

echo "\n\033[39m ldap_sync running for " . count($users) . " users" . "\n";

$i = 0;
$j = 0;
foreach ($users AS $ldapUser) {
  $userdata = array();
  
  $CUDPerson = $personsClass->searchStrict($ldapUser['samaccountname'][0]);
  if (!isset($CUDPerson[0]['cudid'])) {
    $CUDPerson = $personsClass->searchStrict($ldapUser['mail'][0]);
  }

  if (isset($CUDPerson[0]['cudid'])) {
    $i++;
    //echo "\033[39m Checking CUD/LDAP details match on " . $ldapUser['samaccountname'][0] . " with mail: " . $CUDPerson[0]['oxford_email'] . "\n";

    if ($ldapUser['pager'][0] != $CUDPerson[0]['MiFareID']) {
      $userdata["pager"] = $CUDPerson[0]['MiFareID'];
    }
    if ($ldapUser['mail'][0] != $CUDPerson[0]['oxford_email']) {
      $userdata["mail"] = $CUDPerson[0]['oxford_email'];
    }
    # update names?
    if ($ldapUser['givenname'][0] != $CUDPerson[0]['firstname']) {
      $userdata["givenname"] = $CUDPerson[0]['firstname'];
    }
    if ($ldapUser['sn'][0] != $CUDPerson[0]['lastname']) {
      $userdata["sn"] = $CUDPerson[0]['lastname'];
    }
    if ($ldapUser['cn'][0] != $CUDPerson[0]['FullName']) {
      //$userdata["cn"] = $CUDPerson[0]['FullName'];
    }
    
    //printArray($ldapUser);
    
    if (count($userdata) > 0) {
      $j ++;
      echo "\033[32m Updating " . implode(', ', array_keys($userdata)) . " on " . $ldapUser['samaccountname'][0] . " to " . implode(', ', $userdata) . "\n";
      $ldapClass->ldap_mod_replace($ldapUser['dn'], $userdata);
    }
  }
}

echo "\033[39m ldap_sync complete.  Matched on " . $i . " users out of a total of " . count($users) . " ldap users.  " . $j . " users updated" . "\n";
?>
