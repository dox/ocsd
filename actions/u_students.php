<?php
include_once("../engine/initialise.php");

if (isset($_POST['pk']) && isset($_POST['name']) && isset($_POST['value'])) {
	$post_key = $_POST['name'];
	$post_value = $_POST['value'];
	$post_uid = $_POST['pk'];
	
	$user = new Students;
	$existingUser = $user->find_by_uid($_POST['pk']);
	
	$user->inlineUpdate($post_uid, $post_key, $post_value);
	
	$log = new Logs;
	$log->student_id	= $post_uid;
	$log->notes			= $post_key . " value changed";
	$log->prev_value	= $existingUser->$post_key;
	$log->updated_value	= $post_value;
	$log->type			= "update";
	
	$log->create();
}
?>
