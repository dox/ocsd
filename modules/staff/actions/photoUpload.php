<?php

include_once("../../../engine/initialise.php");

foreach ($_FILES["images"]["error"] as $key => $error) {
	$fileUploadLocation = $_SERVER['DOCUMENT_ROOT'] . "/ocsd/uploads/userphoto/";
	
	if ($error == UPLOAD_ERR_OK) {
		$staffClass = new Tutors;
		$staff = $staffClass->find_by_uid($_GET['tutorkey']);
		
		// check if there is a file we need to delete
		if (isset($staff->photo)) {
			$originalFile = $fileUploadLocation . $staff->photo;
			unlink($originalFile);
		}
		
		$name = $_FILES["images"]["name"][$key];
		$newFile = $fileUploadLocation . $name;
		
		move_uploaded_file($_FILES["images"]["tmp_name"][$key], $newFile);
		
		$previousValue = $staff->photo;
		$staff->inlineUpdate($_GET['tutorkey'], "photo", $name);
		
		$log = new Logs;
		$log->student_id	= $staff->id();
		$log->notes			= "Staff photo uploaded/amended for " . $staff->fullDisplayName();
		$log->prev_value	= $previousValue;
		$log->updated_value	= $_FILES['images']['name'][$key];
		$log->type			= "update";
		
		$log->create();
	}
}
?>