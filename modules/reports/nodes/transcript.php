<?php
$user = Students::find_by_uid($_GET['studentid']);
$degree = Grads::find_by_studentkey($user->id());
$subject = QualSubjects::find_by_qsid($degree->qskey);
$exams = Exams::find_by_studentkey($user->id());

$pdf->Cell(0, 20, "", 0, 1);
$pdf->SetFont("Times", '', 12);
$pdf->Cell(0, 10, convertToDateString(), 0, 1);

$pdf->SetFont("Times", 'U', 12);
$pdf->Cell(0, 10, "To Whom It May Concern", 0, 1);

$pdf->SetFont("Times", '', 12);
$pdf->Cell(0, 10, "Oxford University does not issue official transcripts, but this is to certify that", 0, 1);

$pdf->SetFont("Times", 'B', 14);
$pdf->Cell(0, 5, $user->fullDisplayName() . "  (DOB: " . convertToDateString($user->dt_birth) . ")", 0, 1, 'C');

$pdf->SetFont("Times", '', 12);
$pdf->Write(5, "is a full-time member of this College, in the University of Oxford, studying for the Degree Course detailed below:");
$pdf->Ln();

$pdf->Cell(20, 10, "Degree: ", 0, 0);
$pdf->SetFont("Times", 'B', 12);
$pdf->Cell(70, 10, $degree->abbrv, 0, 0);
$pdf->SetFont("Times", '', 12);
$pdf->Cell(90, 10, "Start: " . date('Y M', strtotime($user->dt_start)) . "           End: " . date('Y M', strtotime($user->dt_end)), 0, 1, 'R');

$pdf->Cell(20, 10, "Subject: ", 0, 0);
$pdf->SetFont("Times", 'B', 12);
$pdf->Cell(40, 10, $subject->name, 0, 0);
$pdf->Ln();

if ($_GET['exams'] != 'false') {
	if (count($exams) > 0) {
		$pdf->SetFont("Times", 'B', 14);
		$pdf->Cell(40, 10, "Course Exams", 0, 1);
		
		$pdf->SetFont("Times", 'B', 10);
		$pdf->Cell(30, 7, "Date", 0, 0, 'C');
		$pdf->Cell(130, 7, "Name", 0, 0, 'L');
		$pdf->Cell(20, 7, "Result", 0, 0, 'C');
		$pdf->Ln();
		
		foreach ($exams AS $exam) {
		    $papers = $exam->find_papers_by_examkey($exam->examid);
		    $pdf->SetFont("Times", '', 10);
		    $pdf->Cell(30, 7, date("Y M", strtotime($exam->dt_exam)), 'T', 0, 'C');
		    $pdf->Cell(130, 7, $exam->name, 'T', 0, 'L');
		    $pdf->Cell(20, 7, $exam->qualname, 'T', 0, 'C');
		    $pdf->Ln();
		    
		    $pdf->SetFont("Times", 'B', 10);
		    $pdf->Cell(30, 7, "", 0, 0, 'C');
		    $pdf->Cell(20, 7, "Paper No", 'B', 0, 'C');
		    $pdf->Cell(90, 7, "Title", 'B', 0, 'L');
		    $pdf->Cell(20, 7, "Grade", 'B', 0, 'C');
		    $pdf->Cell(20, 7, "", 0, 0, 'C');
		    $pdf->Ln();
		    
		    $pdf->SetFont("Times", '', 10);
		    foreach ($papers AS $paper) {
		    	$pdf->Cell(30, 7, "", 0, 0, 'C');
		    	$pdf->Cell(20, 7, $paper->paperno, 0, 0, 'C');
		    	$pdf->Cell(90, 7, $paper->title, 0, 0, 'L');
		    	$pdf->Cell(20, 7, $paper->grade, 0, 0, 'C');
		    	$pdf->Cell(20, 7, "", 0, 0, 'C');
		    	$pdf->Ln();
		    }
		}
	} else {
		$pdf->Cell(40, 10, "No exams recorded", 0, 1);
	}
}
$pdf->Ln(20);
$pdf->Cell(0, 10, "Signed:", 0, 1);
$pdf->Ln(10);
$pdf->Cell(0, 60, "ACADEMIC ADMINISTRATOR", 0, 1);
?>
