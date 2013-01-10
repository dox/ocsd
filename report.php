<?php
include_once("engine/initialise.php");
require_once('engine/fpdf/fpdf.php');

$pdf = new FPDF();
$pdf->AddPage();

if (isset($_GET['header'])) {
	if ($_GET['header'] == 'true') {
		include_once("modules/reports/views/header.php");
	}
}

if (isset($_GET['n'])) {
	$fileInclude = "modules/reports/nodes/" . $_GET['n'];
	
	include_once($fileInclude);
}



// Output the PDF
$pdf->Output();
?>