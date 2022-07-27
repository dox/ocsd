<?php
include_once("includes/autoload.php");
//require_once 'vendor/autoload.php';

//$pdf = new FPDF();
$pdf = new \Mpdf\Mpdf();
$pdf->AddPage();


if (isset($_SESSION['username'])) {
	// include the node for the current report
	if (isset($_GET['n'])) {
		$fileInclude = "nodes/reports/" . $_GET['n'] . ".php";
		include_once($fileInclude);
	}
	$pdf->Output();
} else {
	echo "LOGON!";
}

//$logInsert = (new Logs)->insert("view","success",null,"<code>" . $_GET['n'] . "</code> report run");
?>
