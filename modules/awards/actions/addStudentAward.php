<?php
include_once("../../../engine/initialise.php");


if (isset($_POST['studentkey'])) {
	$studentaward = new student_awardsClass;
	$studentaward->studentkey	= $_POST['studentkey'];
	$studentaward->awdkey		= $_POST['awdkey'];
	$studentaward->dt_awarded	= $_POST['dt_awarded'];
	$studentaward->dt_from		= $_POST['dt_from'];
	$studentaward->dt_to		= $_POST['dt_to'];
	$studentaward->value		= $_POST['value'];
	$studentaward->notes		= $_POST['notes'];
	$studentaward->create();
	
	$log = new Logs;
	$log->student_id	= $_POST['studentkey'];
	$log->notes			= "Student award added";
	$log->updated_value	= $_POST['awdkey'];
	$log->type			= "create";
	$log->create();
}
?>