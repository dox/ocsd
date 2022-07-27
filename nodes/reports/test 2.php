<?php
	$pdf->SetAutoPageBreak(false);
	$pdf->SetFont("Times", 'B', 10);
	
	$pdf->WriteHTML('<table border="1">');
	$pdf->WriteHTML('<tr>');
	$pdf->WriteHTML('<td>');
	$pdf->WriteHTML('<h1>Hello world!</h1>');
	$pdf->WriteHTML('</td>');
	$pdf->WriteHTML('</tr>');
	$pdf->WriteHTML('</table>');

	
?>