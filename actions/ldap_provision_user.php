<?php
include_once("../includes/autoload.php");

//$ldapUsername = $_POST['samaccountname'];

$ou = "DC=SEH,DC=ox,DC=ac,DC=uk";
$ldapClass = new LDAP();
$person = new Person($_POST['samaccountname']);

if ($ldapClass) {
	// BIND
	$admin_bind = $ldapClass->ldap_bind();

	// prepare data
	$userDN = "CN=" . $person->fullName() . ",OU=Other,OU=SEH Students,DC=SEH,DC=ox,DC=ac,DC=uk";
	$userdata["sAMAccountName"] = $person->sso_username;
	$userdata["userprincipalname"] = $person->sso_username . "@seh.ox.ac.uk";
	$userdata["pager"] = $person->MiFareID;
	$userdata["cn"] = $person->fullName();
	$userdata["givenname"] = $person->firstname();
	$userdata["displayname"] = $person->firstname();
	$userdata["sn"] = $person->lastname();
	$userdata["objectclass"] = "User";
	//$userdata["useraccountcontrol"] = "512";  // stops script from running?!
	//$userdata["homeDrive"] = 'S:';
	//$userdata["homeDirectory"] = '\\\\helium\students$\Other\%username%';

	if (isset($person->oxford_email)) {
		$userdata["mail"] = $person->oxford_email;
	}
/*
	$adduserAD["instancetype"][0] =
	$adduserAD["name"][0] =
	$adduserAD["company"][0] =
	$adduserAD["department"][0] =
	$adduserAD["title"][0] =
	$adduserAD["description"][0] =
	$adduserAD["initials"][0] =
*/

	// add data to directoryCN=Aisi Li,OU=Other,OU=SEH Students,DC=SEH,DC=ox,DC=ac,DC=uk
	$admin_add = $ldapClass->ldap_add($userDN, $userdata);

	if ($admin_add) {
		$logInsert = (new Logs)->insert("ldap","success",null,"Created user account <code>" . $userdata["cn"] . "</code>");
	} else {
		$logInsert = (new Logs)->insert("ldap","error",null,"Failed to create user account <code>" . $userdata["cn"] . "</code>");
	}
}
?>
