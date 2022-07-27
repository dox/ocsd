<?php
include_once("../includes/autoload.php");

if ($_SESSION['authenticated'] == true) {
	$clean_samaccountname = filter_var($_POST['samaccountname'], FILTER_SANITIZE_STRING);
	$clean_toggle = filter_var($_POST['toggle'], FILTER_SANITIZE_STRING);
	
	$ldapPerson = new LDAPPerson($clean_samaccountname);
	
	if ($clean_toggle == "enable") {
		$ldapPerson->enableUser();
	} elseif ($clean_toggle == "disable") {
		$ldapPerson->disableUser();
	}
} else {
	$logInsert = (new Logs)->insert("ldap","error",null,"Attempt to disable <code>" . $clean_samaccountname . "</code> without being authenticated!");
}
?>