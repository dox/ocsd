<?php
$_POST['samaccountname'] = "2017test";

include_once("../includes/autoload.php");

$ldapUsername = $_POST['samaccountname'];
$ldapPerson = new LDAPPerson($ldapUsername);
$ldapPerson->enableUser();
?>
