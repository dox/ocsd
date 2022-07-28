<?php
$personsClass = new Persons();

if (isset($_GET['filter'])) {
	if ($_GET['filter'] == "students") {
		$persons = $personsClass->allStudents();
	} elseif ($_GET['filter'] == "staff") {
		$persons = $personsClass->allStaff();
	} elseif ($_GET['filter'] == "search") {
		$persons = $personsClass->search($_POST['navbar_search']);
	} elseif ($_GET['filter'] == "suspended") {
		$persons = $personsClass->suspendedPersons();
	} else {
		$persons = $personsClass->all();
	}
} else {
	$_GET['filter'] = "all";
	$persons = $personsClass->all();
}

?>

<table class="table table-striped">
	<thead>
		<tr>
			<th scope="col">SSO</th>
			<th scope="col">LDAP</th>
			<th scope="col">Lastname</th>
			<th scope="col">Firstname</th>
			<th scope="col">Email</th>
		</tr>
	</thead>
	
	<tbody>
	<?php
	foreach ($persons AS $person) {
		$personObject = new Person($person['cudid']);
		$ldapUser = new LDAPPerson($personObject->sso_username, $personObject->oxford_email);
		
		$output  = "<tr>";
		$output .= "<td>" . $personObject->ssoButton() . "</td>";
		$output .= "<td>" . $ldapUser->ldapButton() . "</td>";
		//$output .= "<td><a href=\"index.php?n=ldap_unique&samaccountname=" . $ldapUser->samaccountname . "\">" . $ldapUser->samaccountname . "</a></td>";
		$output .= "<td>" . $personObject->lastname . "</td>";
		$output .= "<td>" . $personObject->firstname . "</td>";
		$output .= "<td>" . makeEmail($personObject->oxford_email) . "</td>";
		$output .= "</tr>";
		
		echo $output;
	}
	?>
	</tbody>
</table>