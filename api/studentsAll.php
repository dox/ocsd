<?php
header('content-type: application/json; charset=utf-8');
include_once("../engine/initialise.php");

$allStudents = Students::find_all();

foreach ($allStudents AS $student) {
	$studArray['name'] = $student->fullDisplayName();
	$studArray['description'] = "Description";
	$studArray['language'] = "language";
	$studArray['value'] = $student->studentid;
	$studArray['tokens'] = array($student->forenames, $student->surname, $student->bodcard(false));
	
	$masterArray[] = $studArray;
	unset($studArray);
}

echo json_encode($masterArray);
?>
