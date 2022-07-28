<?php
$ldapClass = new LDAP();

if ($_GET['filter'] == "ldap-no-cud") {
	$ldapUsers = $ldapClass->usersWithoutCUD();
} elseif ($_GET['filter'] == "cud-no-ldap") {
	$noldapUsers = $ldapClass->usersWithoutLDAP();
} elseif ($_GET['filter'] == "expiring") {
	$ldapUsers = $ldapClass->expiring_users();
} elseif ($_GET['filter'] == "stale") {
	$ldapUsers = $ldapClass->stale_users();
} elseif ($_GET['filter'] == "stale-workstations") {
	$ldapUsers = $ldapClass->stale_workstations();
} elseif ($_GET['filter'] == "search") {
	$ldapUsers = $ldapClass->search($_POST['ldap_search']);
} elseif ($_GET['filter'] == "group") {
	$ldapUsers = $ldapClass->all_users_in_group($_GET['ldap_search']);
} else {
	
}
/*

if (isset($_GET['filter'])) {
  if ($_GET['filter'] == "ldap-no-cud") {
	$personsClass = new Persons;
	$preCheckedUsers = $ldapClass->all_users_enabled();
	$filterDescription = "These are enabled records that exist in the local LDAP, but are not matched against a valid CUD record.";
	foreach ($preCheckedUsers AS $user) {
	  $person = $personsClass->search($user['mail'][0], 1);
	  if (!isset($person[0]['cudid'])) {
		$person = $personsClass->search($user['samaccountname'][0], 1);
		if (!isset($person[0]['cudid'])) {
		  $users[] = $user;
		}
	  }
	}
  } elseif ($_GET['filter'] == "cud-no-ldap") {
	$filterDescription = "These are enabled records that exist in CUD, but are not matched against a valid local LDAP record.";
	$personsClass = new Persons();
	$persons = $personsClass->all();

	foreach ($persons AS $person) {
	  $ldap_entries = $ldap_connection->query()
			  ->where('samaccountname', '=', $person['sso_username'])
			  ->orWhere('mail', '=', $person['oxford_email'])
			  ->get();

	  if (count($ldap_entries) != 1) {
		$users[] = $person;
	  }
	}
  } elseif ($_GET['filter'] == "expiring") {
	$users = $ldapClass->expiring_users();
	$filterDescription = "These are enabled records that exist in the local LDAP, but are due to expire as they have not reset their password in over " . pwd_warn_age . " days.";
  } elseif ($_GET['filter'] == "stale") {
	$users = $ldapClass->stale_users(LDAP_BASE_DN, true);
	$filterDescription = "These are all records that exist in the local LDAP, but have not had their password reset in " . (pwd_warn_age*3) . " days.";
  } elseif ($_GET['filter'] == "stale-workstations") {
	$users = $ldapClass->stale_workstations(LDAP_BASE_DN, true);
	$filterDescription = "These are all records for workstations that exist in the local LDAP, but have not had any activity in " . (pwd_warn_age*3) . " days.";
  } elseif ($_GET['filter'] == "all") {
	$filterDescription = "These are all records that exist in the local LDAP.";
	$users = $ldapClass->all_users_enabled();
  } elseif ($_GET['filter'] == "search") {
	$filterDescription = "These are all records that exist in the local LDAP that match the search term '" . $_POST['ldap_search'] . "'.";
	$users = $ldapClass->search_users($_POST['ldap_search']);
	$logInsert = (new Logs)->insert("ldap","success",null,"LDAP Search for <code>" . $_POST['ldap_search'] . "</code> returned " . count($users) . " results (not all of the results were displayable users)");
  } elseif ($_GET['filter'] == "group") {
	$filterDescription = "These are all records that exist in the local LDAP that are in the group '" . $_GET['cn'] . "'.";
	$group = LdapRecord\Models\ActiveDirectory\Group::find($_GET['cn']);
	$users = $group->members()->get();
	//$logInsert = (new Logs)->insert("ldap","success",null,"LDAP Search for <code>" . $_POST['ldap_search'] . "</code> returned " . count($users) . " results (not all of the results were displayable users)");


  //$members = $group->members()->get();
	//printArray($usersForOutput);
  }
}
*/

?>
<table class="table table-striped">
	<thead>
		<tr>
			<th scope="col">LDAP</th>
			<th scope="col">SSO</th>
			<th scope="col">Full Name</th>
			<th scope="col">Account Status</th>
			<th scope="col">Password Age</th>
			<th scope="col">Email</th>
			<th scope="col">Actions</th>
		</tr>
	</thead>
	
	<tbody>
	<?php
	$personsClass = new Persons();
	
	if ($_GET['filter'] == "cud-no-ldap") {
		foreach ($noldapUsers AS $noldapUser) {
			$ldapUser = new LDAPPerson($noldapUser);
			$CUDPerson = $personsClass->search($noldapUser, 2);
			$CUDPerson = new Person($CUDPerson[0]['cudid']);
			
			$output  = "<tr>";
			$output .= "<td>" . "</td>";
			$output .= "<td>" . $CUDPerson->ssoButton() . "</td>";
			$output .= "<td>" . $CUDPerson->FullName . "</td>";
			$output .= "<td>" . "</td>";
			$output .= "<td>" . "</td>";
			$output .= "<td>" . makeEmail($CUDPerson->oxford_email) . "</td>";
			$output .= "<td>" . $ldapUser->actionsButton($CUDPerson->cudid) . "</td>";
			$output .= "</tr>";
			
			echo $output;
		}
	} else {
		foreach ($ldapUsers AS $ldapUser) {
			
			$ldapUser = new LDAPPerson($ldapUser['samaccountname'][0]);
			
			$CUDPerson = $personsClass->search($ldapUser->samaccountname, 2);
			$CUDPerson = new Person($CUDPerson[0]['cudid']);			
			
			$output  = "<tr>";
			$output .= "<td>" . $ldapUser->ldapButton() . "</td>";
			$output .= "<td>" . $CUDPerson->ssoButton() . "</td>";
			$output .= "<td>" . $ldapUser->cn . "</td>";
			$output .= "<td>" . $ldapUser->useraccountcontrolbadge() . "</td>";
			$output .= "<td>" . $ldapUser->pwdlastsetbadge() . "</td>";
			$output .= "<td>" . makeEmail($ldapUser->mail) . "</td>";
			$output .= "<td>" . $ldapUser->actionsButton($CUDPerson->cudid) . "</td>";
			$output .= "</tr>";
			
			echo $output;
		}
	}
	
	?>
	</tbody>
</table>