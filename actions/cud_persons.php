<?php
include_once("../inc/autoload.php");
requireLogin();

header('Content-Type: application/json');

if ($user->isLoggedIn() && isset($_GET['search'])) {
	$searchColumns = ['cudid', 'sits_student_code', 'firstname', 'lastname', 'sso_username', 'barcode'];
	$searchTerm = trim($_GET['search'] ?? '');
	$whereClause = '';
	$params = [];
	
	if (!empty($searchTerm)) {
		$searchConditions = array_map(function($col) {
			return "$col LIKE :search";
		}, $searchColumns);
	
		$whereClause = 'WHERE ' . implode(' OR ', $searchConditions);
		$params[':search'] = '%' . $searchTerm . '%';
	}
	
	$sql = "SELECT * FROM Person $whereClause LIMIT 50";
	$personsAll = $db->get($sql, $params);
	$data = [];
	
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
