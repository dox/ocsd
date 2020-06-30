<?php
$ldapPerson = new LDAPPerson($_GET['samaccountname']);

if (isset($ldapPerson->samaccountname)) {
    echo "<div class=\"alert alert-dark\" role=\"alert\">" . $ldapPerson->dn . "</div>";

    echo $ldapPerson->actionsButton();

    echo "<h1 class=\"float-right\">" . $ldapPerson->useraccountcontrolbadge() . "</h1>";
		echo "<p>Username: <kbd>" . $ldapPerson->samaccountname . "</kbd></p>";
		echo "<p>Given Name: " . $ldapPerson->givenname . "</p>";
    echo "<p>Description: " . $ldapPerson->description . "</p>";
    echo "<p>CN: " . $ldapPerson->cn . " <i>(" . $ldapPerson->givenname . " " . $ldapPerson->sn . ")</i></p>";
    echo "<p>pwdlastset: " . $ldapPerson->pwdlastsetbadge() . "</p>";
    echo "<p>mail: " . $ldapPerson->emailAddress() . "</p>";

		// Last logon
		$getLastLogon = $ldapPerson->lastlogon;
		$getLastLogon = win_time_to_unix_time($getLastLogon);
		echo "Lastlogon: " . date("d-m-Y H:i", $getLastLogon) . "<br />";

    echo "<h2>Member Of:</h2>";
    echo "<ul>";
    foreach ($ldapPerson->memberof AS $memberOf) {
      echo "<li>" . $memberOf . "</li>";
    }
    echo "</ul>";

    echo "<h2>Object Class:</h2>";
    echo "<ul>";
    foreach ($ldapPerson->objectclass AS $objectClass) {
      echo "<li>" . $objectClass . "</li>";
    }
    echo "</ul>";
	} else {
		if (debug == true) {
			echo "<div class=\"alert alert-warning\" role=\"alert\">";
			echo "<kbd>ldap_count_entries</kbd> as user <code>" . $user_dn . "</code> is <code>" . $ldapPerson->count . "</code>";
			echo "</div>";
		}
    echo "<div class=\"alert alert-warning\" role=\"alert\"><strong>Warning!</strong> More than 1 user found!</div>";
    echo "<pre>";
    print_r($ldapPerson);
    echo "</pre>";
	}
?>
