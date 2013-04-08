<?php
header('content-type: application/json; charset=utf-8');
include_once("../engine/initialise.php");

if (isset($_SESSION['username'])) {
	$allStudents = Students::find_all();
	
	foreach ($allStudents AS $student) {
		$studArray['name'] = $student->fullDisplayName();
		//$studArray['description'] = "Description";
		$studArray['archive'] = "false";
		$studArray['value'] = $student->studentid;
		$studArray['tokens'] = array($student->forenames, $student->surname, $student->prefname, $student->bodcard(false), $student->oucs_id, $student->oss_pn);
		
		$masterArray[] = $studArray;
		unset($studArray);
	}
	
	echo json_encode($masterArray);
}
?>
