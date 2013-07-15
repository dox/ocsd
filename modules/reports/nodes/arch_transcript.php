<?php
$user = ArchStudents::find_by_uid($_GET['arstudentid']);
$degree = ArchGrads::find_academic_record_by_studentkey($user->id());
$degreeType = $degree->qualtype($degree->qtkey);
$subject = QualSubjects::find_by_qsid($degree->qskey);
$exams = ArchExams::find_by_ar_sarkey($degree->ar_sarid);

$course = ugpgvxClass::find_by_ar_sarkey($degree->ar_sarid);

$pdf->SetFont("Times", '', 12);
$pdf->Cell(0, 20, "", 0, 1);
$pdf->Cell(0, 10, date('d F Y'), 0, 1);

$pdf->SetFont("Times", 'U', 12);
$pdf->Cell(0, 10, "To Whom It May Concern", 0, 1);

$pdf->SetFont("Times", '', 12);
$pdf->Cell(0, 10, "Oxford University does not issue official transcripts, but this is to certify that", 0, 1);

$pdf->SetFont("Times", 'B', 14);
$pdf->Cell(0, 5, $user->fullDisplayName(), 0, 1, 'C');
$pdf->SetFont("Times", '', 10);
$pdf->Cell(0, 10, "Date of Birth: " . convertToDateString($user->dt_birth), 0, 1, 'C');

$pdf->SetFont("Times", '', 12);
$pdf->Write(5, "was a full-time member of this College, in the University of Oxford, studying for the Degree Course detailed below:");
$pdf->Ln();

$pdf->Cell(20, 10, "Degree: ", 0, 0);
$pdf->SetFont("Times", 'B', 12);
$pdf->Cell(70, 10, $degreeType->abbrv, 0, 0);
$pdf->SetFont("Times", '', 12);
$pdf->Cell(90, 10, "Start: " . date('Y M', strtotime($degree->dt_start)) . "           End: " . date('Y M', strtotime($degree->dt_end)), 0, 1, 'R');

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
			$examName = $exam->examType($exam->etkey);
			$papers = $exam->find_papers_by_examkey($exam->ar_examid);
		    $pdf->SetFont("Times", '', 10);
		    $pdf->Cell(30, 7, date("Y M", strtotime($exam->dt_exam)), 'T', 0, 'C');
		    $pdf->Cell(130, 7, $examName->name, 'T', 0, 'L');
		    $pdf->Cell(20, 7, $exam->name, 'T', 0, 'C');
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

$pdf->Ln();
$pdf->SetFont("Times", '', 12);
$pdf->Cell(35, 10, "Final degree class: ", 0, 0);
$pdf->SetFont("Times", 'B', 12);
$pdf->Cell(30, 10, $degree->grade, 0, 0);

$pdf->SetFont("Times", '', 12);
$pdf->Cell(30, 10, "Date Conferred: ", 0, 0);
$pdf->SetFont("Times", 'B', 12);
$pdf->Cell(40, 10, date('d F Y', strtotime($course->dt_confer)), 0, 0);

if ($course->dt_MA > 0) {
	$pdf->SetFont("Times", '', 12);
	$pdf->Cell(20, 10, "Date MA: ", 0, 0);
	$pdf->SetFont("Times", 'B', 12);
	$pdf->Cell(30, 10, date('d F Y', strtotime($course->dt_MA)), 0, 0);
}

$pdf->Ln();



$pdf->Ln(20);
$pdf->SetFont("Times", '', 12);
$pdf->Cell(0, 10, "Signed:", 0, 1);
$pdf->Ln(10);
$pdf->Cell(0, 60, "ACADEMIC ADMINISTRATOR", 0, 1);
?>
