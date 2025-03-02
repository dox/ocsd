<?php
include_once("../includes/autoload.php");

if ($_SESSION['authenticated'] == true) {
	$clean_cudid = filter_var($_POST['cudid'], FILTER_SANITIZE_STRING);
	$clean_email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
	
	$person = new Person($clean_cudid);
	
	$user = (new LdapRecord\Models\ActiveDirectory\User)->inside(LDAP_BASE_DN);
	
	$ldapClass = new LDAP();
	$templatesClass = new Templates();
	
	$randomPassword = $ldapClass->randomPassword(8);
	
	$user->cn = $person->FullName;
	$user->samaccountname = strtolower($person->sso_username);
	$user->userPrincipalName = $person->sso_username . LDAP_ACCOUNT_SUFFIX;
	$user->pager = $person->MiFareID;
	$user->givenname = $person->firstname;
	$user->sn = $person->lastname;
	$user->displayname = $person->FullName;
	$user->objectclass = "User";
	$user->description = "\\\\helium\users\%username%";
	$user->unicodePwd = $randomPassword;
	$user->mail = $person->oxford_email;
	//$user->pwdlastset = '-1';
	
	$user->save();
	sleep(1);
	$user->refresh();
	
	$user->userAccountControl = 512;
	
	try {
		$user->save();
	
		// SEND WELCOMING EMAIL
		if ($clean_email == 'enable') {
			$replaceFields = array(
				"username" => strtolower($person->sso_username),
				"firstname" => $person->firstname,
				"password" => $randomPassword
			);
			
			// send student or staff template, depending on card_type
			if ($person->isStudent()) {
				$emailMessageBody = $templatesClass->oneBodyWithReplaceFields('user_ldap_provision-student', $replaceFields);
			} else {
				$emailMessageBody = $templatesClass->oneBodyWithReplaceFields('user_ldap_provision-staff', $replaceFields);
			}
			$sendMailSubject = "Your SEH IT account has been provisioned";
			
			// build an array of the new user, and ldap_provision_recipients...
			$sendMailRecipients = array();
			$sendMailRecipients[] = $person->oxford_email;
			
			// add personal email in CC if we have it
			if (!empty($person->alt_email)) {
				$sendMailRecipients[] = $person->alt_email;
			}
			
			$sendMailRecipients = array_merge($sendMailRecipients, explode(",", $settingsClass->value('ldap_provision_recipients')));
			
			sendMail($sendMailSubject, $sendMailRecipients, $emailMessageBody, "noreply@seh.ox.ac.uk", "SEH IT Office");
		}
		
		$logInsert = (new Logs)->insert("ldap","success",null,"Created user account <code>" . $person->sso_username . "</code>");
	} catch (\LdapRecord\LdapRecordException $e) {
		// Failed saving user.
		echo "Error";
		printArray($e);
		$logInsert = (new Logs)->insert("ldap","error",null,"Failed to create user account for CUDID <code>" . $clean_cudid . "</code>");
	}
} else {
	$logInsert = (new Logs)->insert("ldap","error",null,"Attempt to provision <code>" . $clean_cudid . "</code> without being authenticated!");
}
?>
