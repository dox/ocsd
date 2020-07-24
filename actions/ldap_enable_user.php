<?php
include_once("../includes/autoload.php");

$ldapUsername = $_POST['samaccountname'];
$ldapPerson = new LDAPPerson($ldapUsername);

if (isset($_POST['reset'])) {
  $ldapPerson->enableUser(true);
} else {
  $ldapPerson->enableUser(false);
}
?>
