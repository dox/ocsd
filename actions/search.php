<?php
include_once("../includes/autoload.php");

$personSearchArray = array();

$personsClass = new Persons();

$persons = $personsClass->search($_GET['search']);

foreach ($persons AS $person) {
	$personArray = array();
	$personArray['cudid'] = $person['cudid'];
	$personArray['sso'] = $person['sso_username'];
	$personArray['bodcard'] = $person['barcode7'];
	$personArray['name'] = $person['FullName'];
	
	$personSearchArray[] = $personArray;
}

echo json_encode($personSearchArray);
?>