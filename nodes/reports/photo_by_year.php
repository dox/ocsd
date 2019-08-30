<?php
//$studentClass = new Students;
//$students = $studentClass->find_by_sql("SELECT * FROM students WHERE yr_cohort = '" . $_GET['cohort'] . "' ORDER BY surname ASC");
if (isset($_GET['cohort'])) {
	$persons = $db->where("unit_set_cd", $_GET['cohort']);
}
$persons = $db->orderBy ("lastname", "Asc");
$persons = $db->get ("Person");

$pdf->SetAutoPageBreak(false);

$pdf->SetFont("Times", 'B', 10);
$studentArray = array();

foreach ($persons AS $person) {
	$photo = "photos/UAS_UniversityCard-" . $person['university_card_sysis'] . ".jpg";
	
	$subjectName = str_replace("Visiting Non-Matriculated - ", "", $person['rout_name']);
	$subjectName = str_replace("Visiting Non-Matriculated ", "", $subjectName);
	$subjectName = str_replace("Visiting Matriculated ", "", $subjectName);
	if (strlen($subjectName) > 33) {
		$subjetNameShort = substr($subjectName, 1, 33);
	} else {
		$subjetNameShort = $subjectName;
	}
	
	$studentArray[] = array('studentPhoto' => $photo, 'studentName' => $person['FullName'], 'studentDegree' => $subjetNameShort, 'studentSubject' => "");
	
	
}

$rowCounter = 0;
$sets = array_chunk($studentArray, 3);
foreach ($sets as $set) {
	if (file_exists($set[0]['studentPhoto'])) {
		$pdf->Cell(65, 50, $pdf->Image($set[0]['studentPhoto'], $pdf->GetX()+ 15, $pdf->GetY(), 38), 0, 0, 'C', false );
	} else {
		$pdf->Cell(65, 50, "", 0, 0, 'C', false);
	}
	if (file_exists($set[1]['studentPhoto'])) {
		$pdf->Cell(65, 50, $pdf->Image($set[1]['studentPhoto'], $pdf->GetX()+ 15, $pdf->GetY(), 38), 0, 0, 'C', false );
	} else {
		$pdf->Cell(65, 50, "", 0, 0, 'C', false);
	}
	if (file_exists($set[2]['studentPhoto'])) {
		$pdf->Cell(65, 50, $pdf->Image($set[2]['studentPhoto'], $pdf->GetX()+ 15, $pdf->GetY(), 38), 0, 1, 'C', false );
	} else {
		$pdf->Cell(65, 50, "", 0, 1, 'C', false);
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

?>