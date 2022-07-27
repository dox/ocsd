<?php
include_once("../includes/autoload.php");

if ($_SESSION['authenticated'] == true) {
	$clean_samaccountname = filter_var($_POST['samaccountname'], FILTER_SANITIZE_STRING);
	
	$ldapPerson = new LDAPPerson($clean_samaccountname);
	$ldapPerson->deleteUser();
} else {
	$logInsert = (new Logs)->insert("ldap","error",null,"Attempt to delete <code>" . $clean_samaccountname . "</code> without being authenticated!");
}
?>