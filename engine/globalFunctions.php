<?php
function printArray($array) {
	echo ("<pre>");
	print_r ($array);
	echo ("</pre>");
}

function convertToDateString($dateString) {
	$dateFormat = "Y-m-d";
	$date = strtotime($dateString);
	
	$returnDate = date($dateFormat, $date);
	
	return $returnDate;
}

function convertToDateTimeString($dateTimeString) {
	$dateFormat = "Y-m-d H:i:s";
	$date = strtotime($dateTimeString);
	
	$returnDate = date($dateFormat, strtotime($date));
	
	return $returnDate;
}
?>