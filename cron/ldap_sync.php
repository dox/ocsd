<?php
$ldapClass = new LDAP();

if ($ldapClass) {
  $admin_bind = $ldapClass->ldap_bind();
  $allLDAPUsers = $ldapClass->all_users();
}
$dateNow = date('Y-m-d');

foreach ($allLDAPUsers AS $ldapUser) {
  $userdata = null;
  $ldapPerson = new LDAPPerson($ldapUser['samaccountname'][0]);

  $person = new Person($ldapPerson->samaccountname);
  if (!isset($person->cudid)) {
    $person = new Person($ldapPerson->mail);
  }

  if (isset($person->cudid)) {
    if ($ldapPerson->pager != $person->MiFareID) {
      $userdata["pager"] = $person->MiFareID;
    }
    if ($ldapPerson->mail != $person->oxford_email) {
      $userdata["mail"] = $person->oxford_email;
    }
    # update names?

    if (count($userdata) > 0){
      echo $ldapPerson->samaccountname;
      printArray($userdata);
      $ldapClass->ldap_mod_replace($ldapPerson->dn, $userdata);
      echo "<hr />";
    }
  }
}
?>
