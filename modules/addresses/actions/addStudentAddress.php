<?php
include_once("../../../engine/initialise.php");

if (isset($_POST['studentkey'])) {
	$address = new Addresses;
	//$address->addrid		= $_POST['addrid'];
	$address->studentkey	= $_POST['studentkey'];
	$address->line1			= $_POST['line1'];
	$address->line2			= $_POST['line2'];
	$address->line3			= $_POST['line3'];
	$address->line4			= $_POST['line4'];
	$address->town			= $_POST['town'];
	$address->county		= $_POST['county'];
	$address->postcode		= $_POST['postcode'];
	$address->cykey			= $_POST['cykey'];
	$address->phone			= $_POST['phone'];
	$address->email			= $_POST['email'];
	$address->mobile		= $_POST['mobile'];
	$address->fax			= $_POST['fax'];
	$address->defalt		= $_POST['defalt'];
	$address->atkey			= $_POST['atkey'];
	
	$address->create();
	
	$log = new Logs;
	$log->notes			= "New Address added";
	$log->student_id	= $_POST['studentkey'];
	$log->type			= "create";
	$log->create();
}
?>