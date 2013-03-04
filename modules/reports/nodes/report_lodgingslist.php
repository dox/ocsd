<?php
$students = Students::find_by_sql("SELECT * FROM students ORDER BY surname ASC");

$pdf->SetFont("Times", 'BU', 12);
$pdf->Cell(0, 10, "St Edmund Hall", 0, 1);

$pdf->SetFont("Times", 'BU', 12);
$pdf->Cell(0, 10, "Lodgings Address List 2012-2013", 0, 1);

$pdf->SetFont("Times", 'B', 10);
$pdf->Cell(70, 7, "Name", 0, 0, 'L');
$pdf->Cell(70, 7, "Room/Address", 0, 0, 'L');
$pdf->Cell(40, 7, "Telephone", 0, 0, 'L');
$pdf->Ln();

foreach ($students AS $student) {
	$addresses = ResidenceAddresses::find_all_by_student($student->studentid);
	foreach ($addresses AS $address) {
		$resAddress = ResAddress::find_by_uid($address->radkey);
		 
		$addOutput  = "";
		$phoneOutput  = "";
		
		if ($resAddress->line1) {
			$addOutput .= $resAddress->line1 . " ";
		}
		if ($resAddress->line2) {
			$addOutput .= $resAddress->line2 . " ";
		}
		if ($resAddress->town) {
			$addOutput .= $resAddress->town . " ";
		}
		if ($resAddress->postcode) {
			$addOutput .= $resAddress->postcode . " ";
		}
		
		
		if ($address->roomno) {
			$addOutput .= $address->roomno . " ";
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
	
	$pdf->SetFont("Times", '', 10);
	$pdf->Cell(70, 7, $name, 'T', 0, 'L');
	$pdf->Cell(70, 7, $addOutput, 'T', 0, 'L');
	$pdf->Cell(40, 7, $phoneOutput, 'T', 0, 'L');
	$pdf->Ln();
}


?>
