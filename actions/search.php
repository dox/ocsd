<?php
include_once("../includes/autoload.php");

if ($_SESSION['authenticated'] == true) {
	$personSearchArray = array();
	
	$personsClass = new Persons();
	
	$persons = $personsClass->search($_GET['search'], 10);
	
	foreach ($persons AS $person) {
		$personArray = array();
		$personArray['cudid'] = $person['cudid'];
		$personArray['sso'] = $person['sso_username'];
		$personArray['bodcard'] = $person['barcode7'];
		$personArray['name'] = $person['FullName'];
		$personArray['sits_student_code'] = $person['sits_student_code'];
		
		$personSearchArray[] = $personArray;
	}
	
	echo json_encode($personSearchArray);
}
?>