<?php
$ldapClass = new LDAP();

if ($ldapClass) {
  $admin_bind = $ldapClass->ldap_bind();
  $allLDAPUsers = $ldapClass->all_users();
}
$dateNow = date('Y-m-d');

foreach ($allLDAPUsers AS $ldapUser) {
  # update pager (miFare)

  # update email address

  # update names?

  # disable accounts that don't exist in CUD
  //$output  = "\$found= Get-ADUser -Filter \"SamAccountName -eq '" . $person['sso_username'] . "' -or mail -eq '" . $person['oxford_email'] . "'\" <br />";
  //$output .= "if(\$found){<br />";
  //$output .= "\$found|Set-ADUser -Replace @{pager = '" . $person['MiFareID'] . "'; mail = '" . $person['oxford_email'] . "'} -Verbose <br />";
  //$output .= "}<br/><br/>";

  //$userdata["useraccountcontrol"] = "514";
  //$userdata["description"] = $ldapUser['description'][0] . " (NO CUD RECORD " . $dateNow . ")";
  //$ldapClass->ldap_mod_replace($ldapUser['dn'], $userdata);
}
?>
