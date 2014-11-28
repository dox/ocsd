<?php
$studentClass = new Students();
$students = $studentClass->find_by_sql("SELECT * FROM students ORDER BY surname ASC");

if ($_GET['type'] == "csv") {
	// build header rows for CSV
	
	// build header rows for CSV
	$classVars1 = get_class_vars(get_class($studentClass));
	
	$n = 0;
	
	$outputArray[$n][] = "Title";
	$outputArray[$n][] = "Forenames";
	$outputArray[$n][] = "Surname";
	$outputArray[$n][] = "Preferred Name";
	$outputArray[$n][] = "Res. Status";
	$outputArray[$n][] = "Residence Address";
	$outputArray[$n][] = "Mobile";
	$outputArray[$n][] = "Oxford E-Mail";
	$outputArray[$n][] = "Personal E-Mail";
	$outputArray[$n][] = "College Status";
	$outputArray[$n][] = "Degree";
	$outputArray[$n][] = "Subject";
	$outputArray[$n][] = "Year";
	
	$n++;
	
	foreach ($students AS $student) {
		$addresses = ResidenceAddresses::find_all_by_student($student->studentid);
		
		$resStatusClass = new resStatus;
		$resStatuses = $resStatusClass->find_all();
		$resStatus = $resStatusClass->find_by_uid($student->rskey);
					
		$degree = Grads::find_by_studentkey($student->studentid);
		$subject = QualSubjects::find_by_qsid($degree->qskey);
	
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
				
				
				
		$outputArray[$n][] = $student->title();
		$outputArray[$n][] = $student->forenames;
		$outputArray[$n][] = $student->surname;
		$outputArray[$n][] = $student->prefname;
		$outputArray[$n][] = $resStatus->status;
		$outputArray[$n][] = $addOutput;
		$outputArray[$n][] = $student->mobile;
		$outputArray[$n][] = $student->email1;
		$outputArray[$n][] = $student->email2;
		$outputArray[$n][] = $student->st_type;
		$outputArray[$n][] = $degree->abbrv;
		$outputArray[$n][] = $subject->name;
		$outputArray[$n][] = $student->course_yr;
			
		$n++;
	}
} else {
	$pdf->SetFont("Times", 'BU', 12);
	$pdf->Cell(0, 10, "St Edmund Hall", 0, 1);
	
	$pdf->SetFont("Times", 'BU', 12);
	$pdf->Cell(0, 10, "Dean's List (Created: " . date('Y-m-d') . ")", 0, 1);
	
	$pdf->SetFont("Times", 'B', 8);
	$pdf->Cell(45, 7, "Name", 0, 0, 'L');
	$pdf->Cell(35, 7, "Residence Status", 0, 0, 'L');
	$pdf->Cell(75, 7, "Residence Address", 0, 0, 'L');
	$pdf->Cell(40, 7, "Mobile Number", 0, 0, 'L');
	$pdf->Ln();
	
	foreach ($students AS $student) {
		$resStatusClass = new resStatus;
		$resStatuses = $resStatusClass->find_all();
		$resStatus = $resStatusClass->find_by_uid($student->rskey);
		
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
		if (isset($student->prefname)) {
			$name = $name . " (" . $student->prefname . ")";
		}
		
		$pdf->SetFont("Times", '', 8);
		$pdf->Cell(45, 7, $name, 'T', 0, 'L');
		$pdf->Cell(35, 7, $resStatus->status, 'T', 0, 'L');
		$pdf->Cell(75, 7, $addOutput, 'T', 0, 'L');
		$pdf->Cell(40, 7, $student->mobile, 'T', 0, 'L');
		$pdf->Ln();
	}
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





/*


} else {
	$pdf->SetFont("Times", 'BU', 12);
	$pdf->Cell(0, 10, "St Edmund Hall", 0, 1);
	
	$pdf->SetFont("Times", 'BU', 12);
	$pdf->Cell(0, 10, "Dean's List", 0, 1);
	
	$pdf->SetFont("Times", 'B', 10);
	$pdf->Cell(15, 7, "Title", 0, 0, 'L');
	$pdf->Cell(35, 7, "Forenames", 0, 0, 'L');
	$pdf->Cell(35, 7, "Surname", 0, 0, 'L');
	$pdf->Cell(35, 7, "Res Status", 0, 0, 'L');
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
*/
$log = new Logs;
$log->notes			= "Report 'Deans List' Generated";
$log->prev_value	= $_GET['n'];
$log->type			= "report";
$log->create();
?>
