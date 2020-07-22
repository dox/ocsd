<?php
include_once("../includes/autoload.php");

$ldapClass = new LDAP();
$templatesClass = new Templates();
$dateNow = date('Y-m-d');

$users = $ldapClass->all_users_enabled();

foreach ($users AS $ldapUser) {
  $ldapPerson = new LDAPPerson($ldapUser['samaccountname'][0]);
  if (isset($ldapPerson->mail)) {
    $pwdlastsetInDays = $ldapPerson->pwdlastsetage();
    $pwdLastSetDate = date('Y-m-d', strtotime("-" . $pwdlastsetInDays . " days"));

    if ($pwdlastsetInDays <= pwd_warn_age) {
  		//password ok
  	} elseif ($pwdlastsetInDays > pwd_warn_age && $pwdlastsetInDays) {
      //password expiring within 30 - 60 days
      $password_expiry_in_days = (pwd_max_age - $pwdlastsetInDays);

      $sendMail = false;
      $replaceFields = array(
        "username" => strtolower($ldapPerson->samaccountname),
        "firstname" => $ldapPerson->givenname,
        "password_expiry_duration" => autoPluralise($password_expiry_in_days . " day", $password_expiry_in_days . " days", $password_expiry_in_days)
      );
      $emailMessageBody = $templatesClass->oneBodyWithReplaceFields('user_password_expiring', $replaceFields);
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
  	}
  }

  if ($pwdlastsetInDays >= pwd_max_age) {
    //password expired
    echo "Expiring " . $ldapPerson->samaccountname . "<br />";
    $ldapPerson->disableUser();
    
    $logInsert = (new Logs)->insert("cron","warning",null,"Auto disable user account <code>" . $ldapPerson->samaccountname . "</code>", $ldapPerson->samaccountname);
  }
}
?>
