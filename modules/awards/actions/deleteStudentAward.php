<?php
include_once("../../../engine/initialise.php");

if (isset($_POST['student_awdkey'])) {
	$studentAwards = new student_awardsClass;
	$studentAward = $studentAwards->find_by_uid($_POST['student_awdkey']);
	
	$studentAwards->sawid		= $_POST['student_awdkey'];
	$studentAwards->delete();
	
	$log = new Logs;
	$log->student_id	= $studentAward->studentkey;
	$log->notes			= "Student award deleted";
	//$log->prev_value	= $studentAward-sawid;
	$log->type			= "delete";
	$log->create();
}
?>