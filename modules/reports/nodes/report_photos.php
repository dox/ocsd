<?php
$students = Students::find_by_sql("SELECT * FROM students WHERE photo IS NOT NULL ORDER BY surname ASC");

$pdf->SetFont("Times", 'BU', 12);
$pdf->Cell(0, 10, "St Edmund Hall", 0, 1);

$pdf->SetFont("Times", 'BU', 12);
$pdf->Cell(0, 10, "Photo List 2012-2013", 0, 1);

$pdf->SetFont("Times", 'B', 10);
$pdf->Cell(65, 7, "Name", 0, 0, 'L');
$pdf->Cell(75, 7, "Room/Address", 0, 0, 'L');
$pdf->Cell(40, 7, "Telephone", 0, 0, 'L');
$pdf->Ln();

foreach ($students AS $student) {
	$addresses = ResidenceAddresses::find_all_by_student($student->studentid);
	foreach ($addresses AS $address) {
		$resAddress = ResAddress::find_by_uid($address->radkey);
		 
		$addOutput  = "";
		$phoneOutput  = "";
		
		if ($address->roomno) {
			$addOutput .= $address->roomno . " ";
		}
		
		if ($resAddress->line1) {
			$addOutput .= $resAddress->line1 . " ";
		}
		if ($resAddress->line2) {
			$addOutput .= $resAddress->line2 . " ";
		}
		
		// phone numbers
		if ($address->phone) {
			$phoneOutput .= $address->phone . "  ";
		}
		if ($resAddress->phone) {
			$phoneOutput .= $resAddress->phone . " ";
		}
		if ($student->mobile) {
			$phoneOutput .= $student->mobile . " ";
		}
		
	}
	
	$name = $student->surname . ", " . $student->forenames;
	
	$photo = "uploads/" . $student->photo;
	$pdf->SetFont("Times", '', 10);
	$pdf->Cell(65, 7, $name, 'T', 0, 'L');
	$pdf->Cell(75, 7, $addOutput, 'T', 0, 'L');
	$pdf->Image($photo,null,null,30);
	$pdf->Ln();
}


?>
