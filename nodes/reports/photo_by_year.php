<?php
$personsClass = new Persons();

if (isset($_GET['cohort'])) {
	//$persons = $db->where("unit_set_cd", $_GET['cohort']);
	$persons = $personsClass->allStudentsByCohort($_GET['cohort']);
} else {
	$prsons = $personsClass->allStudents();

}

$pdf->SetAutoPageBreak(false);

$pdf->SetFont("Times", 'B', 10);
$studentArray = array();

foreach ($persons AS $personUnique) {
	$person = new Person($personUnique['cudid']);

	$photo = $person->photo();

	$subjectName = str_replace("Visiting Non-Matriculated - ", "", $person->rout_name);
	$subjectName = str_replace("Visiting Non-Matriculated ", "", $subjectName);
	$subjectName = str_replace("Visiting Matriculated ", "", $subjectName);
	if (strlen($subjectName) > 33) {
		//$subjetNameShort = substr($subjectName, 1, 33);
		$subjetNameShort = $subjectName;
	} else {
		$subjetNameShort = $subjectName;
	}

	$studentArray[] = array('studentPhoto' => $photo, 'studentName' => $person->FullName, 'studentDegree' => $subjetNameShort, 'studentSubject' => "");
}

function ordinalSuffix( $n ) {
	return date('S',mktime(1,1,1,1,( (($n>=10)+($n>=20)+($n==0))*10 + $n%10) ));
}
$heading = "Photo Report: " . $_GET['cohort'] . ordinalSuffix($_GET['cohort']) . " Year";

$pdf->WriteHTML('<h1>' . $heading . '</h1>');

$rowCounter = 0;
$sets = array_chunk($studentArray, 3);


$pdf->WriteHTML('<table width="100%" border="1">');
foreach ($sets as $set) {

	$pdf->WriteHTML('<tr>');
	$pdf->WriteHTML('<td align="center">');
	if (file_exists($set[0]['studentPhoto'])) {
		$photo = $set[0]['studentPhoto'];
	} else {
		$photo = "images/photo_blank.jpg";
	}
	$pdf->WriteHTML('<img src="' . $photo . '" width="140" height="182"><br />');
	$pdf->WriteHTML(str_replace("'", "'", $set[0]['studentName']) . "<br />");
	$pdf->WriteHTML(str_replace("'", "'", $set[0]['studentDegree']) . "<br />");
	$pdf->WriteHTML(str_replace("'", "'", $set[0]['studentSubject']));
	$pdf->WriteHTML('</td>');

	$pdf->WriteHTML('<td align="center">');
	if (file_exists($set[1]['studentPhoto'])) {
		$photo = $set[1]['studentPhoto'];
	} else {
		$photo = "images/photo_blank.jpg";
	}
	$pdf->WriteHTML('<img src="' . $photo . '" width="140" height="182"><br />');
	$pdf->WriteHTML(str_replace("'", "'", $set[1]['studentName']) . "<br />");
	$pdf->WriteHTML(str_replace("'", "'", $set[1]['studentDegree']) . "<br />");
	$pdf->WriteHTML(str_replace("'", "'", $set[1]['studentSubject']));
	$pdf->WriteHTML('</td>');

	$pdf->WriteHTML('<td align="center">');
	if (file_exists($set[2]['studentPhoto'])) {
		$photo = $set[2]['studentPhoto'];
	} else {
		$photo = "images/photo_blank.jpg";
	}
	$pdf->WriteHTML('<img src="' . $photo . '" width="140" height="182"><br />');
	$pdf->WriteHTML(str_replace("'", "'", $set[2]['studentName']) . "<br />");
	$pdf->WriteHTML(str_replace("'", "'", $set[2]['studentDegree']) . "<br />");
	$pdf->WriteHTML(str_replace("'", "'", $set[2]['studentSubject']));
	$pdf->WriteHTML('</td>');
	$pdf->WriteHTML('</tr>');

	$rowCounter = $rowCounter + 1;

	if ($rowCounter >= '4') {
		$pdf->WriteHTML('</table>');
		$pdf->WriteHTML('<table table width="100%" border="1">');
		$rowCounter = 0;
	}
}
$pdf->WriteHTML('</table>');

?>
