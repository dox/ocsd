<?php
include_once("../includes/autoload.php");

header('Content-Type: application/json');

if ($_SESSION['authenticated'] == true) {
	$personSearchArray = array();
	
	$personsClass = new Persons();
	$persons = $personsClass->search($_GET['search'], 5);
	
	$data = array();
	
	foreach ($persons AS $person) {
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