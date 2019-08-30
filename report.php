<?php
session_start();

require_once('config.php');
require_once('database/MysqliDb.php');
$db = new MysqliDb ($db_host, $db_username, $db_password, $db_name);
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/adLDAP/adLDAP.php');
require_once('includes/fpdf/fpdf.php');

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

try {
		$adldap
		 = new adLDAP();
	}
	catch (adLDAPException $e) {
	    echo $e;
	    exit();
	}
	
	$pdf = new FPDF();
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

$logSQLInsert = Array ("type" => "REPORT", "description" => $_SESSION['username'] . " ran report " . $_GET['n']);
$id = $db->insert ('_logs', $logSQLInsert);
?>