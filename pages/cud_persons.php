<?php
$personsClass = new Persons();

$allowedFilters = [
	'staff' => "university_card_type = '" . implode("', '", $personsClass->staffTypes) . "'",
	'students' => "university_card_type IN ('" . implode("', '", $personsClass->studentTypes) . "')",
	'underage' => "(dob IS NOT NULL AND dob >= '" . date('Ymd', strtotime('-18 years')) . "')",
	'cud-no-ldap' => "1=1",
	'suspended' => " cudid IN (
		SELECT cudid
		FROM Suspensions
		WHERE STR_TO_DATE(SuspendStrDt, '%Y%m%d') <= CURDATE()
		  AND STR_TO_DATE(COALESCE(SuspendEndDt, SuspendExpEndDt), '%Y%m%d') >= CURDATE()
	)",
	'unsuspended' => " cudid IN (
		SELECT cudid
		FROM Suspensions
		WHERE
		  -- Calculate effective end date (either real or expected)
		  STR_TO_DATE(COALESCE(SuspendEndDt, SuspendExpEndDt), '%Y%m%d')
			BETWEEN DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND DATE_SUB(CURDATE(), INTERVAL 1 DAY)
	)",
	'test' => 'cudid = \'9357283B-B4CF-4DC0-A2A4-4E01310846FE\''
];

$filter = $_GET['filter'] ?? null;
$whereClause = isset($allowedFilters[$filter]) ? "WHERE {$allowedFilters[$filter]}" : "";

$sql = "SELECT cudid FROM Person $whereClause";

$personsAll = $db->get($sql);

if ($filter == "cud-no-ldap") {
	foreach ($personsAll AS $arrayPosition => $person) {
		$lookups = array_filter([
			'cudid'		=> $person['cudid'] ?? null
		]);
		
		$person = new Person($lookups);
		
		if (!empty($person->getLdapRecord())) {
			unset($personsAll[$arrayPosition]);
		}
	}
}

if ($filter == "unsuspended") {
	foreach ($personsAll AS $arrayPosition => $person) {
		$lookups = array_filter([
			'cudid'		=> $person['cudid'] ?? null
		]);
		
		$person = new Person($lookups);
		
		if ($person->suspensions()->isCurrentlySuspended()) {
			unset($personsAll[$arrayPosition]);
		}
	}
}

// table or card view?
$view = 'table'; // default
if (isset($_GET['view']) && in_array($_GET['view'], ['card', 'table'])) {
	$view = $_GET['view'];
}

if ($view === "card") {
	$viewTitle = "Table View";
	$viewURL = "index.php?page=cud_persons&filter=" . $filter . "&view=table";
} else {
	$viewTitle = "Card View";
	$viewURL = "index.php?page=cud_persons&filter=" . $filter . "&view=card";
}

$data = array(
		'icon'		=> 'person',
		'title'		=> count($personsAll) . autoPluralise(" CUD Person", " CUD Persons", count($personsAll)),
		'subtitle'	=> 'Filter: ' . $filter,
		'actions'	=> array(
			"<a class=\"btn btn-success\" href=\"" . $viewURL . "\" role=\"button\">" . icon('person-circle') . " " . $viewTitle . "</a>"
		)
);
echo pageTitle($data);

if ($view === "card") {
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
			$ldapButton = '<span class="text-muted">Not found</span>';
		}
		
		if ($person->ssoButton()) {
			$ssoButton = $person->ssoButton();
		} else {
			$ssoButton = '<span class="text-muted">Not found</span>';
		}
		
		$output .= "<th scope=\"row\">" . $ssoButton . "</th>";
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

