<?php
$ldapPerson = new LDAPPerson($personJSON->sso_username, $personJSON->oxford_email);

if (isset($ldapPerson->samaccountname)) {
		echo "<div class=\"alert alert-dark\" role=\"alert\">" . $ldapPerson->dn . "</div>";
    echo "<h1 class=\"float-right\">" . $ldapPerson->useraccountcontrolbadge() . "</h1>";
		echo "<p>Username: <kbd>" . $ldapPerson->samaccountname . "</kbd></p>";
		echo "<p>Given Name: <kbd>" . $ldapPerson->givenname . "</kbd></p>";
    echo "<p>Description: " . $ldapPerson->description . "</p>";
    echo "<p>CN: " . $ldapPerson->cn . " <i>(" . $ldapPerson->givenname . " " . $ldapPerson->sn . ")</i></p>";
    echo "<p>pwdlastset: " . $ldapPerson->pwdlastsetbadge() . "</p>";
    echo "<p>mail: " . makeEmail($ldapPerson->mail) . "</p>";

    echo "<h2>Member Of:</h2>";
    echo "<ul>";
    foreach ($ldapPerson->memberof AS $memberOf) {
      echo "<li>" . "<div class=\"alert alert-dark\" role=\"alert\">" . $memberOf . "</div>" . "</li>";
    }
    echo "</ul>";
} else {
	if (debug == true) {
		echo "<div class=\"alert alert-warning\" role=\"alert\">";
		echo "<kbd>ldap_count_entries</kbd> as user <code>" . $user_dn . "</code> is <code>" . $ldapPerson['count'] . "</code>";
		echo "</div>";
	}

	echo "<div class=\"alert alert-warning\" role=\"alert\"><strong>Warning!</strong> More than 1 user found!</div>";
  echo "<pre>";
  print_r($ldapPerson);
  echo "</pre>";
}

$includeFile = true;
?>
