<?php
include_once("engine/initialise.php");
require_once('engine/fpdf/fpdf.php');

$pdf = new FPDF();
$pdf->AddPage();

// only exclude the header if explicitly asked to do so
if ($_GET['header'] != 'false') {
	include_once("modules/reports/views/header.php");
}

// include the node for the current report
if (isset($_GET['n'])) {
	$fileInclude = "modules/reports/nodes/" . $_GET['n'];
	
	include_once($fileInclude);
}

// Output the PDF
$pdf->Output();
?>