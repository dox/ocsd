<?php
if (isset($_GET['studenttype'])) {
	if ($_GET['studenttype'] == "ug") {
		$sql = "SELECT * FROM students WHERE st_type = 'ug' ORDER BY surname ASC";
		$reportTitle = "Nationalities (Undergraduates Only)";
	} elseif ($_GET['studenttype'] == "pg") {
		$sql = "SELECT * FROM students WHERE st_type = 'pg' ORDER BY surname ASC";
		$reportTitle = "Nationalities (Postgraduates Only)";
	} elseif ($_GET['studenttype'] == "vx") {
		$sql = "SELECT * FROM students WHERE st_type = 'vx' ORDER BY surname ASC";
		$reportTitle = "Nationalities (Visiting Students Only)";
	}
} else {
	$sql = "SELECT * FROM students ORDER BY surname ASC";
	$reportTitle = "Nationalities";
}
$students = Students::find_by_sql($sql);

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
	$pdf->Cell(0, 10, $reportTitle, 0, 1);
	
	foreach ($students AS $student) {
		$friendlyNatationName = str_replace("/", "", $student->nationality);
		$friendlyNatationName = str_replace(" ", "", $friendlyNatationName);
		//$friendlyNatationName = "test";
		$nationalityTotals[$friendlyNatationName] = $nationalityTotals[$friendlyNatationName] + 1;
	}
	
	$url  = "http://chart.googleapis.com/chart";
	$url .= "?chxt=y";
	$url .= "&chxr=0,0," . max(array_values($nationalityTotals));
	$url .= "&chbh=a";
	$url .= "&chds=0," . max(array_values($nationalityTotals));
	$url .= "&chxl=0:|" . implode("|", array_keys($nationalityTotals));
	$url .= "&chs=680x425";
	$url .= "&cht=bhg";
	$url .= "&chco=A2C180";
	$url .= "&chd=t:" . implode(",", array_values($nationalityTotals));
	//$url .= "&chtt=Vertical+bar+chart";
	
	$pdf->Image($url,10,10,0,0,'PNG');
	$pdf->Cell(0, 90, "", 0, 1);
	
	$pdf->SetFont("Times", 'B', 10);
	$pdf->Cell(65, 7, "Name", 0, 0, 'L');
	$pdf->Cell(75, 7, "Nationality", 0, 0, 'L');
	$pdf->Cell(40, 7, "Country Of Birth", 0, 0, 'L');
	$pdf->Ln();
	
	foreach ($students AS $student) {
		$addresses = ResidenceAddresses::find_all_by_student($student->studentid);
		$nationality = $student->nationality;
		$birth_nation = Countries::find_by_uid($student->birth_cykey);

		/*
	public $resid_cykey;
	public $citiz_cykey;
	
		public $eng_lang;
	public $ethkey;
	public $rskey;
	public $cskey;
		public $fee_status;
*/
		$name = $student->surname . ", " . $student->forenames;
		
		$pdf->SetFont("Times", '', 10);
		$pdf->Cell(65, 7, $name, 'T', 0, 'L');
		$pdf->Cell(75, 7, $nationality, 'T', 0, 'L');
		$pdf->Cell(40, 7, $birth_nation->short, 'T', 0, 'L');
		$pdf->Ln();
	}
}
?>
