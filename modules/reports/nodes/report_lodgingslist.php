<?php
$students = Students::find_by_sql("SELECT * FROM students ORDER BY surname ASC");

if ($_GET['type'] == "csv") {
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

} else {
	$pdf->SetFont("Times", 'BU', 12);
	$pdf->Cell(0, 10, "St Edmund Hall", 0, 1);
	
	$pdf->SetFont("Times", 'BU', 12);
	$pdf->Cell(0, 10, "Lodgings Address List 2012-2013", 0, 1);
	
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
		
		$pdf->SetFont("Times", '', 10);
		$pdf->Cell(65, 7, $name, 'T', 0, 'L');
		$pdf->Cell(75, 7, $addOutput, 'T', 0, 'L');
		$pdf->Cell(40, 7, $phoneOutput, 'T', 0, 'L');
		$pdf->Ln();
	}
}
?>
