<?php
include_once("../inc/autoload.php");

header('Content-Type: application/json');

if ($user->isLoggedIn()) {
	$searchColumns = ['cudid', 'sits_student_code', 'firstname', 'lastname', 'sso_username', 'barcode'];
	$searchTerm = $_GET['search'] ?? null;
	$whereClause = '';
	
	if (!empty($searchTerm)) {
		$escapedTerm = addslashes($searchTerm); // or better: use prepared statements
		$searchConditions = array_map(function($col) use ($escapedTerm) {
			return "$col LIKE '%$escapedTerm%'";
		}, $searchColumns);
	
		$whereClause = 'WHERE ' . implode(' OR ', $searchConditions);
	}
	
	$sql = "SELECT * FROM Person $whereClause LIMIT 50";
	$personsAll = $db->get($sql);
	
	foreach ($personsAll AS $person) {
		$personArray = array();
		$personArray['cudid'] = $person['cudid'];
		$personArray['sso'] = $person['sso_username'];
		$personArray['bodcard'] = $person['barcode7'];
		$personArray['name'] = $person['FullName'];
		$personArray['sits_student_code'] = $person['sits_student_code'];
		
		$data[] = $personArray;
	}
	
	echo json_encode(['data' => $data]);
}
?>