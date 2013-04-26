<?php
include_once("../../../engine/initialise.php");

foreach ($_FILES["images"]["error"] as $key => $error) {
	if ($error == UPLOAD_ERR_OK) {
		$name = $_FILES["images"]["name"][$key];
		move_uploaded_file($_FILES["images"]["tmp_name"][$key], $_SERVER['DOCUMENT_ROOT'] . "/ocsd/uploads/userphoto/" . $_FILES['images']['name'][$key]);
		
		$students = new Students;
		$student = $students->find_by_uid($_GET['studentkey']);
		$previousValue = $student->photo;
		$student->inlineUpdate($_GET['studentkey'], "photo", $name);
		
		$log = new Logs;
		$log->student_id	= $student->id();
		$log->notes			= "Photo uploaded/amedned";
		$log->prev_value	= $previousValue;
		$log->updated_value	= $_FILES['images']['name'][$key];
		$log->type			= "update";
		
		$log->create();
	}
}
?>