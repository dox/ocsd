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

function autoPluralise ($singular, $plural, $count = 1) {
	// fantasticly clever function to return the correct plural of a word/count combo
	// Usage:	$singular	= single version of the word (e.g. 'Bus')
	//       	$plural 	= plural version of the word (e.g. 'Busses')
	//			$count		= the number you wish to work out the plural from (e.g. 2)
	// Return:	the singular or plural word, based on the count (e.g. 'Jobs')
	// Example:	autoPluralise("Bus", "Busses", 3)  -  would return "Busses"
	//			autoPluralise("Bus", "Busses", 1)  -  would return "Bus"

	return ($count == 1)? $singular : $plural;
} // END function autoPluralise
?>