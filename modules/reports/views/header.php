<?php
$pdf->SetFont("Times", '', 12);
$pdf->Cell(90, 5, "TELEPHONE: (01865) 279008", 0, 0);
$pdf->Cell(90, 5, "St Edmund Hall", 0, 1, 'R');

$pdf->Cell(90, 5, "FAX: (01865) 279002", 0, 0);
$pdf->Cell(90, 5, "Queens Lane", 0, 1, 'R');
$pdf->Cell(180, 5, "Oxford", 0, 2, 'R');
$pdf->Cell(180, 5, "OX1 4AR", 0, 2, 'R');

$pdf->Ln(10);
?>