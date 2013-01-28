<?php
function printArray($array) {
	echo ("<pre>");
	print_r ($array);
	echo ("</pre>");
}

function convertToDateString($dateString, $time = false) {
	if ($time == "true") {
		$dateFormat = "Y-m-d H:i:s";
	} else {
		$dateFormat = "Y-m-d";
	}
	
	if ($dateString == "") {
		$returnDate = "Unknown";
	} else {
		$date = strtotime($dateString);
		$returnDate = date($dateFormat, $date);
	}
	
	return $returnDate;
}


function age($date = NULL) {
	if ($birthDate == NULL) {
		$birthDate = date('Y-m-d');
	}
	
	return intval(substr(date('Ymd') - date('Ymd', strtotime($date)), 0, -4));
}
?>