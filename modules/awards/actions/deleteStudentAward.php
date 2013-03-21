<?php
include_once("../../../engine/initialise.php");

if (isset($_POST['student_awdkey'])) {
	$studentaward = new student_awardsClass;
	$studentaward->sawid		= $_POST['student_awdkey'];
	$studentaward->delete();
	
	$log = new Logs;
	$log->student_id	= $_POST['studentkey'];
	$log->notes			= "Student award deleted";
	$log->updated_value	= $_POST['awdkey'];
	$log->type			= "delete";
	$log->create();
}
?>