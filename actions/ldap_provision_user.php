<?php
include_once("../includes/autoload.php");

if ($_SESSION['authenticated'] == true) {
	$clean_cudid = filter_var($_POST['cudid'], FILTER_SANITIZE_STRING);
	$clean_email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
	
	$person = new Person($clean_cudid);
	
	$user = (new LdapRecord\Models\ActiveDirectory\User)->inside(LDAP_BASE_DN);
	
	$ldapClass = new LDAP();
	$templatesClass = new Templates();
	
	$randomPassword = $ldapClass->randomPassword(5);
	
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
			$emailMessageBody = $templatesClass->oneBodyWithReplaceFields('user_ldap_provision', $replaceFields);
			$sendMailSubject = "Your SEH IT account has been provisioned";
			sendMail($sendMailSubject, array($person->oxford_email, "andrew.breakspear@seh.ox.ac.uk"), $emailMessageBody, "noreply@seh.ox.ac.uk", "SEH IT Office");
			//sendMail($sendMailSubject, array("andrew.breakspear@seh.ox.ac.uk"), $emailMessageBody, "noreply@seh.ox.ac.uk", "SEH IT Office");
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