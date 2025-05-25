<?php
$allowedFilters = [
	'staff' => "university_card_type = 'CS'",
	'students' => "university_card_type IN ('UG', 'PG')",
	'underage' => "(dob IS NOT NULL AND dob >= '" . date('Ymd', strtotime('-18 years')) . "')",
	'cud-no-ldap' => "1=1",
	'suspended' => " cudid IN (
		SELECT cudid
		FROM Suspensions
		WHERE STR_TO_DATE(SuspendStrDt, '%Y%m%d') <= CURDATE()
		  AND STR_TO_DATE(COALESCE(SuspendEndDt, SuspendExpEndDt), '%Y%m%d') >= CURDATE()
	)",
	'test' => 'cudid = \'7EBED3BE-233F-4376-99AC-AAE649686FA8\''
];

$filter = $_GET['filter'] ?? null;
$whereClause = isset($allowedFilters[$filter]) ? "WHERE {$allowedFilters[$filter]}" : "";

$sql = "SELECT cudid FROM Person $whereClause";

$personsAll = $db->get($sql);

if ($filter == "cud-no-ldap") {
	$i = 0;
	foreach ($personsAll AS $person) {
		$lookups = array_filter([
			'cudid'		=> $person['cudid'] ?? null
		]);
		
		$person = new Person($lookups);
		
		if (!empty($person->getLdapRecord())) {
			unset($personsAll[$i]);
		}
		
		$i++;
	}
}

$data = array(
		'icon'		=> 'person',
		'title'		=> count($personsAll) . " CUD Persons",
		'subtitle'	=> 'Filter: ' . $filter
);
echo pageTitle($data);

if (isset($_GET['view']) && $_GET['view'] == "card") {
	$viewTitle = "Table View";
	$viewURL = "index.php?page=cud_persons&filter=" . $filter . "&view=table";
} elseif (isset($_GET['view']) && $_GET['view'] == "table") {
	$viewTitle = "Card View";
	$viewURL = "index.php?page=cud_persons&filter=" . $filter . "&view=card";
} else {
	$viewTitle = "Card View";
	$viewURL = "index.php?page=cud_persons&filter=" . $filter . "&view=card";
}
?>

<div class="pb-3 text-end">
	<a class="btn btn-success" href="<?php echo $viewURL; ?>" role="button"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-add" viewBox="0 0 16 16">
  <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4"></path>
  <path d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z"></path>
</svg> <?php echo $viewTitle; ?></a>
</div>
<?php
if (isset($_GET['view']) && $_GET['view'] == "card") {
	$output  = "<div class=\"row row-cols-1 row-cols-md-4 g-4\">";
	foreach ($personsAll as $person) {
		$person = new Person($person['cudid']);
		$output .= $person->card();
	}
	
	$output .= "</div>";
	
	echo $output;
} else {
	$output  = "<table class=\"table\">";
	$output .= "<thead>";
	$output .= "<tr>";
	$output .= "<th scope=\"col\">SSO</th>";
	$output .= "<th scope=\"col\">LDAP</th>";
	$output .= "<th scope=\"col\">Last Name</th>";
	$output .= "<th scope=\"col\">First Name</th>";
	$output .= "<th scope=\"col\">Email</th>";
	$output .= "</tr>";
	$output .= "</thead>";
	$output .= "<tbody>";
	
	foreach ($personsAll as $person) {
		$person = new Person($person['cudid']);
		
		if ($person->getLdapRecord()) {
			$ldapUser = new LdapUserWrapper($person->getLdapRecord());
			$ldapButton = $ldapUser->getLDAPButton();
		} else {
			$ldapUser = null;
			$ldapButton = '<span class="text-muted">Not found</span>'; // or whatever fallback you like
		}
		
		$output .= "<th scope=\"row\">" . $person->ssoButton() . "</th>";
		$output .= "<td>" . $ldapButton . "</td>";
		$output .= "<td>" . $person->lastname . "</td>";
		$output .= "<td>" . $person->firstname . "</td>";
		$output .= "<td>" . $person->oxford_email . "<span class=\"float-end\">" . $person->actionsButton() . "</span></td>";
		$output .= "</tr>";
	}
	
	$output .= "</tbody>";
	$output .= "</table>";
	
	echo $output;
}
?>

