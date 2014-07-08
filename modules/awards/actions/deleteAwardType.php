<?php
include_once("../../../engine/initialise.php");

if (isset($_POST['awdid'])) {
	$awards = new Awards;
	
	$awards->awdid		= $_POST['awdid'];
	$awards->delete();
	
	$log = new Logs;
	$log->notes			= "Award type deleted";
	$log->prev_value	= $_POST['awdid'];
	$log->type			= "delete";
	$log->create();
}
?>