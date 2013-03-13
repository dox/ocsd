<?php
include_once("../engine/initialise.php");

if (isset($_POST['pk']) && isset($_POST['name']) && isset($_POST['value'])) {
	$post_key = $_POST['name'];
	$post_value = $_POST['value'];
	$post_uid = $_POST['pk'];
	
	$user = new Students;
	
	$user->inlineUpdate($post_uid, $post_key, $post_value);
	
	sendMail("AJAX Update", "andrew.breakspear@seh.ox.ac.uk", $_POST['pk']);
}
?>
