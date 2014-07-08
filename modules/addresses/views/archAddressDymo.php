<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/ocsd/engine/initialise.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/ocsd/engine/fpdf/fpdf.php');
$address = ArchAddresses::find_by_uid($_GET['uid']);

$pdf = new FPDF('L','mm',array(89,28));
$pdf->SetMargins(0,0,0);
$pdf->AddPage();
$pdf->SetFont('Arial','B',11);
$pdf->SetAutoPageBreak(0);

if ($address->line1) {
	$pdf->Cell(0,5,$address->line1,0,1,'C');
}
if ($address->line2) {
	$pdf->Cell(0,5,$address->line2,0,1,'C');
}
if ($address->line3) {
	$pdf->Cell(0,5,$address->line3,0,1,'C');
}
if ($address->line4) {
	$pdf->Cell(0,5,$address->line4,0,1,'C');
}
if ($address->town || $address->county) {
	$output = $address->town . ", " . $address->county;
	$pdf->Cell(0,5,$output,0,1,'C');
}
if ($address->postcode) {
	$pdf->Cell(0,5,$address->postcode,0,1,'C');
}
$pdf->Output();
?>
