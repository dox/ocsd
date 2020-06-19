<?php
$ldapClass = new LDAP();
if ($ldapClass) {
  $admin_bind = $ldapClass->ldap_bind();
  $allLDAPUsers = $ldapClass->all_users();
}
$dateNow = date('Y-m-d');
//printArray($allLDAPUsers);
foreach ($allLDAPUsers AS $ldapUser) {
  if (isset($ldapUser['mail'][0])) {
    $pwdlastsetInDays = $ldapClass->pwdlastsetage($ldapUser['pwdlastset'][0]);
    $pwdLastSetDate = date('Y-m-d', strtotime("-" . $pwdlastsetInDays . " days"));

    if ($pwdlastsetInDays <= pwd_warn_age) {
  		//password ok
  	} elseif ($pwdlastsetInDays > pwd_warn_age && $pwdlastsetInDays) {
      //password expiring within 30 - 60 days
      $firstname = "andrew";
      $username = "sedm4218";
      $password_expiry_in_days = (pwd_max_age - $pwdlastsetInDays);

      $emailMessageBody = file_get_contents("cron/email_expiring_password.template");
      $emailMessageBody = str_replace("{{firstname}}", $ldapUser['givenname'][0], $emailMessageBody);
      $emailMessageBody = str_replace("{{username}}", strtolower($ldapUser['samaccountname'][0]), $emailMessageBody);
      $emailMessageBody = str_replace("{{password_expiry_in_days}}", $password_expiry_in_days, $emailMessageBody);

      if ($password_expiry_in_days == 30) {
        sendMail("Subject", array($ldapUser['mail'][0]), $emailMessageBody, "noreply@seh.ox.ac.uk", "SEH IT Office");
        $logInsert = (new Logs)->insert("email","success",null,"Sending password expiry email (" . $password_expiry_in_days . " days) to <code>" . $ldapUser['mail'][0] . "</code>", $ldapUser['samaccountname'][0]);
      } elseif ($password_expiry_in_days == 7) {
        sendMail("Subject", array($ldapUser['mail'][0]), $emailMessageBody, "noreply@seh.ox.ac.uk", "SEH IT Office");
        $logInsert = (new Logs)->insert("email","success",null,"Sending password expiry email (" . $password_expiry_in_days . " days) to <code>" . $ldapUser['mail'][0] . "</code>", $ldapUser['samaccountname'][0]);
      } elseif ($password_expiry_in_days == 2) {
        sendMail("Subject", array($ldapUser['mail'][0]), $emailMessageBody, "noreply@seh.ox.ac.uk", "SEH IT Office");
        $logInsert = (new Logs)->insert("email","success",null,"Sending password expiry email (" . $password_expiry_in_days . " day) to <code>" . $ldapUser['mail'][0] . "</code>", $ldapUser['samaccountname'][0]);
      }
  	} elseif ($pwdlastsetInDays > pwd_max_age) {
  		//password expired
      $userdata["useraccountcontrol"] = "514";
      $userdata["description"] = $ldapUser['description'][0] . " PWD EXPIRED";
      //$ldapClass->ldap_mod_replace($ldapUser['dn'], $userdata);
      $logInsert = (new Logs)->insert("ldap","warning",null,"Auto disable user account <code>" . $ldapUser['samaccountname'][0] . "</code>");
  	} else {
  		//password expiry unknown
  	}


  } else {
    // user doesn't have an email address!
  }
}
echo "email expiring passwords";

?>
