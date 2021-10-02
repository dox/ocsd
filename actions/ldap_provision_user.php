<?php
include_once("../includes/autoload.php");

$person = new Person($_POST['cudid']);
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
$user->description = "\\\\helium\students$\\" . date('Y') . "\%username%";
$user->unicodePwd = $randomPassword;
$user->mail = $person->oxford_email;

try {
	$user->save();
	sleep(1);

	//$user->userAccountControl = 512;
	//$user->save();

	// SEND WELCOMING EMAIL
	if ($_POST['email'] == 'true') {
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

	echo "Success";
	$logInsert = (new Logs)->insert("ldap","success",null,"Created user account <code>" . $userdata["sAMAccountName"] . "</code>");
} catch (\LdapRecord\LdapRecordException $e) {
	// Failed saving user.
	echo "Error";
	printArray($e);
	$logInsert = (new Logs)->insert("ldap","error",null,"Failed to create user account for CUDID <code>" . $_POST["cudid"] . "</code>");
}
?>
