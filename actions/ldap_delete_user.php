<?php
include_once("../includes/autoload.php");

$ldapUsername = $_POST['samaccountname'];
$ldapPerson = new LDAPPerson($ldapUsername);
$ldapPerson->deleteUser();
?>
