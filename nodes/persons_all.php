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
			<th scope="col">Card Type</th>
		</tr>
	</thead>
	
	<tbody>
	<?php
	foreach ($persons AS $personUnique) {
		$ldapUser = new LDAPPerson($personUnique['sso_username'], $personUnique['oxford_email']);
		
		//$person = new Person($personUnique['cudid']);
		//printArray($personUnique);
		$output  = "<tr>";
		$output .= "<td><a href=\"index.php?n=person_unique&cudid=" . $personUnique['cudid'] . "\">" . $personUnique['sso_username'] . "</a></td>";
		$output .= "<td><a href=\"index.php?n=ldap_unique&samaccountname=" . $ldapUser->samaccountname . "\">" . $ldapUser->samaccountname . "</a></td>";
		$output .= "<td>" . $personUnique['lastname'] . "</td>";
		$output .= "<td>" . $personUnique['firstname'] . "</td>";
		$output .= "<td>" . $personUnique['university_card_type'] . "</td>";
		$output .= "";
		$output .= "";
		$output .= "";
		$output .= "</tr>";
		
		echo $output;
	}
	?>
	</tbody>
</table>