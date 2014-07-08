<?php

if ($_GET['studenttype'] == "ug") {
	$sql = "SELECT students.st_type, students.surname, students.forenames, qual_types.abbrv, qual_subjects.name, students.course_yr, student_resaddress.roomno, resaddress.line1, resaddress.line2, resaddress.town, resaddress.postcode, address_types.type, address_types.res_type
FROM students, undergrads, qual_subjects, qual_types, student_resaddress, resaddress, address_types
WHERE students.studentid = undergrads.studentkey
AND undergrads.qskey = qual_subjects.qsid
AND undergrads.qtkey = qual_types.qtid
AND student_resaddress.studentkey = students.studentid
AND student_resaddress.radkey = resaddress.radid
AND resaddress.atkey = address_types.atid";
} elseif ($_GET['studenttype'] == "pg") {
	$sql = "SELECT students.st_type, students.surname, students.forenames, qual_types.abbrv, qual_subjects.name, students.course_yr, student_resaddress.roomno, resaddress.line1, resaddress.line2, resaddress.town, resaddress.postcode, address_types.type, address_types.res_type
FROM students, postgrads, qual_subjects, qual_types, student_resaddress, resaddress, address_types
WHERE students.studentid = postgrads.studentkey
AND postgrads.qskey = qual_subjects.qsid
AND postgrads.qtkey = qual_types.qtid
AND student_resaddress.studentkey = students.studentid
AND student_resaddress.radkey = resaddress.radid
AND resaddress.atkey = address_types.atid";
} elseif ($_GET['studenttype'] == "vx") {
	$sql = "SELECT students.st_type, students.surname, students.forenames, students.course_yr, student_resaddress.roomno, resaddress.line1, resaddress.line2, resaddress.town, resaddress.postcode, address_types.type, address_types.res_type
FROM students, visitexchs, student_resaddress, resaddress, address_types
WHERE students.studentid = visitexchs.studentkey
AND student_resaddress.studentkey = students.studentid
AND student_resaddress.radkey = resaddress.radid
AND resaddress.atkey = address_types.atid";
} else {
	
}

$students = Report::find_by_sql($sql);

// build header rows for CSV
$classVars1 = get_class_vars(get_class($students));

$n = 0;

foreach ($classVars1 as $name => $value) {
   $outputArray[1][$n] = $name;
   $n++;
}

// start itterating through the database
$i = 2;

foreach ($students AS $student) {
   $n = 0;
   $classVars2 = get_object_vars($student);

   foreach ($classVars2 as $name => $value) {
   	if ($value == "" || $value == null) {
   		$value = "";
   	}
   
   	$outputArray[$i][$n] = $value;
   	
   	$n++;
   }
   
   $i++;
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
