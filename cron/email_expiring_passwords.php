<?php
$ldapClass = new LDAP();

if ($ldapClass) {
  $admin_bind = $ldapClass->ldap_bind();
  $allLDAPUsers = $ldapClass->all_users();
}
$dateNow = date('Y-m-d');

foreach ($allLDAPUsers AS $ldapUser) {
  $ldapPerson = new LDAPPerson($ldapUser['samaccountname'][0]);
  if (isset($ldapPerson->mail)) {
    $pwdlastsetInDays = $ldapPerson->pwdlastsetage();
    $pwdLastSetDate = date('Y-m-d', strtotime("-" . $pwdlastsetInDays . " days"));

    if ($pwdlastsetInDays <= pwd_warn_age) {
  		//password ok
  	} elseif ($pwdlastsetInDays > pwd_warn_age && $pwdlastsetInDays) {
      //password expiring within 30 - 60 days
      $password_expiry_in_days = (pwd_max_age - $pwdlastsetInDays);

      $emailMessageBody = file_get_contents("cron/email_expiring_password.template");
      $emailMessageBody = str_replace("{{firstname}}", $ldapPerson->givenname, $emailMessageBody);
      $emailMessageBody = str_replace("{{username}}", strtolower($ldapPerson->samaccountname), $emailMessageBody);
      $emailMessageBody = str_replace("{{password_expiry_duration}}", autoPluralise($password_expiry_in_days . " day", $password_expiry_in_days . " days", $password_expiry_in_days), $emailMessageBody);

      $sendMail = false;
      $sendMailSubject = "Your SEH IT Password is due to expire in " . $password_expiry_in_days . autoPluralise(" day", " days", $password_expiry_in_days);

      if ($password_expiry_in_days == 30) {
        $sendMail = true;
      } elseif ($password_expiry_in_days == 7) {
        $sendMail = true;
      } elseif ($password_expiry_in_days == 1) {
        $sendMail = true;
      }

      if ($sendMail == true) {
        echo "Emailing " . $ldapPerson->samaccountname . "<br />";
        sendMail($sendMailSubject, array($ldapUser['mail'][0]), $emailMessageBody, "noreply@seh.ox.ac.uk", "SEH IT Office");
        $logInsert = (new Logs)->insert("cron","success",null,"Sending password expiry email (" . $password_expiry_in_days . autoPluralise(" day", " days", $password_expiry_in_days) . " warning) to <code>" . $ldapPerson->mail . "</code>", $ldapPerson->samaccountname);
      }
  	} elseif ($pwdlastsetInDays > pwd_max_age) {
  		//password expired
      echo "Expiring " . $ldapPerson->samaccountname . "<br />";
      $userdata["useraccountcontrol"] = "514";
      $userdata["description"] = $ldapPerson->description . " PWD EXPIRED";
      $ldapClass->ldap_mod_replace($ldapUser['dn'], $userdata);
      $logInsert = (new Logs)->insert("cron","warning",null,"Auto disable user account <code>" . $ldapPerson->samaccountname . "</code>", $ldapPerson->samaccountname);
  	} else {
  		//password expiry unknown
  	}


  } else {
    // user doesn't have an email address!
  }
}
?>
