<?php
include_once("../includes/autoload.php");

$personsClass = new Persons();

$suspendedPersons = $personsClass->suspendedPersons();

foreach ($personsClass->suspendedPersons() AS $suspendedPerson) {
  $ldapPerson = new LDAPPerson($suspendedPerson['sso_username'], $suspendedPerson['oxford_email']);

  if ($ldapPerson->useraccountcontrol != '514') {
    $ldapPerson->disableUser();
  }
}
?>
