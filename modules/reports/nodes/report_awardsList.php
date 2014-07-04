<?php

if ($_GET['studenttype'] == "ug") {
}

//$awardTypes = Awards::find_all($sql);
$studentAwards = student_awardsClass::find_all();

// build header rows for CSV
$outputArray[0]["student_title"] = "student_title";
$outputArray[0]["student_initials"] = "student_initials";
$outputArray[0]["student_forenames"] = "student_forenames";
$outputArray[0]["student_surname"] = "student_surname";
$outputArray[0]["student_email"] = "student_email";
$outputArray[0]["student_course"] = "student_course";
$outputArray[0]["student_courseyear"] = "student_courseyear";
$outputArray[0]["award_name"] = "award_name";
$outputArray[0]["award_awardeddate"] = "award_awardeddate";
$outputArray[0]["award_startdate"] = "award_startdate";
$outputArray[0]["award_enddate"] = "award_enddate";

$i = 1;

foreach ($studentAwards AS $studentAward) {
	$student = Students::find_by_uid($studentAward->studentkey);
	$award = Awards::find_by_uid($studentAward->awdkey);
	$degree = Grads::find_by_studentkey($student->studentid);
	$subject = QualSubjects::find_by_qsid($degree->qskey);
	//$outputArray[$i["test"] = $studentAward->sawid;
	
	$outputArray[$i]["student_title"] = $student->title();
	$outputArray[$i]["student_initials"] = $student->initials;
	$outputArray[$i]["student_forenames"] = $student->forenames;
	$outputArray[$i]["student_surname"] = $student->surname;
	$outputArray[$i]["student_email"] = $student->email1;
	$outputArray[$i]["student_course"] = "TEST";
	$outputArray[$i]["student_courseyear"] = $student->course_yr;
	$outputArray[$i]["award_name"] = $award->name;
	$outputArray[$i]["award_awardeddate"] = $studentAward->dt_awarded;
	$outputArray[$i]["award_startdate"] = $studentAward->dt_from;
	$outputArray[$i]["award_enddate"] = $studentAward->dt_to;
	
	$i = $i + 1;
}

function outputCSV($data) {
   $outstream = fopen("php://output", "w");
   
   function __outputCSV(&$vals, $key, $filehandler) {
   	fputcsv($filehandler, $vals, ',', '"');
   }
   
   array_walk($data, '__outputCSV', $outstream);
   
   fclose($outstream);
}

outputCSV($outputArray);

$log = new Logs;
$log->notes			= "Report 'Address List' Generated for Student Type " . $_GET['studenttype'];
$log->prev_value	= $_GET['n'];
$log->type			= "report";
$log->create();
?>
