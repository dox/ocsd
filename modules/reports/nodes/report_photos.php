<?php
$studentClass = new Students;
$students = $studentClass->find_by_sql("SELECT * FROM students WHERE yr_cohort = '" . $_GET['cohort'] . "' ORDER BY surname ASC");


$pdf->SetAutoPageBreak(false);

$pdf->SetFont("Times", 'B', 10);
$studentArray = array();

foreach ($students AS $student) {
	$degree = Grads::find_by_studentkey($student->studentid);
	$subject = QualSubjects::find_by_qsid($degree->qskey);
	
	if (isset($student->photo)) {
		$photo = "uploads/userphoto/" . $student->photo;
	} else {
		$photo = null;
	}
	
	if (strlen($subject->name) > 45) {
		$subjetName = $subject->abbrv;
	} else {
		$subjetName = $subject->name;
	}
	
	$studentArray[] = array('studentPhoto' => $photo, 'studentName' => ($student->surname . ", " . $student->forenames), 'studentDegree' => $degree->abbrv, 'studentSubject' => $subjetName);
	
	
}

$rowCounter = 0;
$sets = array_chunk($studentArray, 3);
foreach ($sets as $set) {
	if (file_exists($set[0]['studentPhoto'])) {
		$pdf->Cell(65, 50, $pdf->Image($set[0]['studentPhoto'], $pdf->GetX()+ 15, $pdf->GetY(), 38), 0, 0, 'C', false );
	} else {
		$pdf->Cell(65, 50, "", 1, 0, 'C', false);
	}
	if (file_exists($set[1]['studentPhoto'])) {
		$pdf->Cell(65, 50, $pdf->Image($set[1]['studentPhoto'], $pdf->GetX()+ 15, $pdf->GetY(), 38), 0, 0, 'C', false );
	} else {
		$pdf->Cell(65, 50, "", 1, 0, 'C', false);
	}
	if (file_exists($set[2]['studentPhoto'])) {
		$pdf->Cell(65, 50, $pdf->Image($set[2]['studentPhoto'], $pdf->GetX()+ 15, $pdf->GetY(), 38), 0, 1, 'C', false );
	} else {
		$pdf->Cell(65, 50, "", 1, 1, 'C', false);
	}
	$pdf->Cell(65, 5, $set[0]['studentName'], 0, 0, 'C', false);
	$pdf->Cell(65, 5, $set[1]['studentName'], 0, 0, 'C', false);
	$pdf->Cell(65, 5, $set[2]['studentName'], 0, 1, 'C', false);
	$pdf->Cell(65, 5, $set[0]['studentDegree'], 0, 0, 'C', false);
	$pdf->Cell(65, 5, $set[1]['studentDegree'], 0, 0, 'C', false);
	$pdf->Cell(65, 5, $set[2]['studentDegree'], 0, 1, 'C', false);
	$pdf->Cell(65, 5, $set[0]['studentSubject'], 0, 0, 'C', false);
	$pdf->Cell(65, 5, $set[1]['studentSubject'], 0, 0, 'C', false);
	$pdf->Cell(65, 5, $set[2]['studentSubject'], 0, 1, 'C', false);
	$pdf->Ln();
	
	$rowCounter = $rowCounter + 1;
	
	if ($rowCounter >= '4') {
		$pdf->AddPage();
		$rowCounter = 0;
	}
}

$log = new Logs;
$log->notes			= "Report 'Photo' Generated for " . $_GET['cohort'] . " Cohort";
$log->prev_value	= $_GET['n'];
$log->type			= "report";
$log->create();
?>