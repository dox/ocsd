<?php
include_once("../../../engine/initialise.php");

foreach ($_FILES["images"]["error"] as $key => $error) {
	$fileUploadLocation = $_SERVER['DOCUMENT_ROOT'] . "/ocsd/uploads/userphoto/";
	
	if ($error == UPLOAD_ERR_OK) {
		$students = new Students;
		$student = $students->find_by_uid($_GET['studentkey']);
		
		// check if there is a file we need to delete
		if (isset($student->photo)) {
			$originalFile = $fileUploadLocation . $student->photo;
			unlink($originalFile);
		}
		
		$name = $_FILES["images"]["name"][$key];
		$newFile = $fileUploadLocation . $name;
		
		move_uploaded_file($_FILES["images"]["tmp_name"][$key], $newFile);
		
		$previousValue = $student->photo;
		$student->inlineUpdate($_GET['studentkey'], "photo", $name);
		
		$log = new Logs;
		$log->student_id	= $student->id();
		$log->notes			= "Photo uploaded/amended";
		$log->prev_value	= $previousValue;
		$log->updated_value	= $_FILES['images']['name'][$key];
		$log->type			= "update";
		
		$log->create();
	}
}
?>