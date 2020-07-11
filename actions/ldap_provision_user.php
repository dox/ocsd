<?php
include_once("../includes/autoload.php");

$ldapClass = new LDAP();
$person = new Person($_POST['cudid']);

if ($ldapClass && $person) {
	// BIND
	//printArray($person);
	$admin_bind = $ldapClass->ldap_bind();

	$randomPassword = $ldapClass->randomPassword(5);

	// prepare data
	$userDN = "CN=" . $person->FullName . "," . LDAP_BASE_DN;
	$userdata["sAMAccountName"] = strtolower($person->sso_username);
	$userdata["userprincipalname"] = $person->sso_username . LDAP_ACCOUNT_SUFFIX;
	$userdata["pager"] = $person->MiFareID;
	$userdata["cn"] = $person->FullName;
	$userdata["givenname"] = $person->firstname;
	$userdata["sn"] = $person->lastname;
	$userdata["displayname"] = $person->FullName;
	$userdata["objectclass"] = "User";
	//$userdata["description"][0] = "test;
	//$userdata["instancetype"][0] = "";
	//$userdata["name"][0] = "";
	//$userdata["company"][0] = "";
	//$userdata["department"][0] = "";
	//$userdata["title"][0] = "";
	//$userdata["initials"][0] = "";

	$userdata["useraccountcontrol"] = "544";  // stops script from running?!
	$userdata["unicodepwd"] = iconv("UTF-8", "UTF-16LE", "\"". $randomPassword ."\"");

	//$userdata["homeDrive"] = 'S:';
	//$userdata["homeDirectory"] = '\\\\helium\students$\Other\%username%';

	if (isset($person->oxford_email)) {
		$userdata["mail"] = $person->oxford_email;
	}

	if ($_POST['email'] == 'true') {
		// SEND WELCOMING EMAIL
		$emailMessageBody = file_get_contents("../cron/email_user_provision.template");
		$emailMessageBody = str_replace("{{firstname}}", $person->firstname, $emailMessageBody);
		$emailMessageBody = str_replace("{{username}}", $person->sso_username, $emailMessageBody);
		$emailMessageBody = str_replace("{{password}}", $randomPassword, $emailMessageBody);

		$sendMailSubject = "Your SEH IT account has been provisioned";

		sendMail($sendMailSubject, array($person->firstname->oxford_email), $emailMessageBody, "noreply@seh.ox.ac.uk", "SEH IT Office");
	}

//CREATE LDAP Account
//$admin_add = $ldapClass->ldap_add($userDN, $userdata);

	if ($admin_add) {
		$logInsert = (new Logs)->insert("ldap","success",null,"Created user account <code>" . $userdata["sAMAccountName"] . "</code>");
	} else {
		$logInsert = (new Logs)->insert("ldap","error",null,"Failed to create user account for CUDID <code>" . $_POST["cudid"] . "</code>");
	}
}
?>
