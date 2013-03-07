<?php
echo "sending";
include_once("../engine/initialise.php");

if (isset($_POST['recipient'])) {
	if (isset($_POST['recipient'])) {
		$msg_recipient = $_POST['recipient'];
	}
	
	if (isset($_POST['subject'])) {
		$msg_subject = $_POST['subject'];
	} else {
		$msg_subject = "No Subject Specified";
	}
	
	if (isset($_POST['message'])) {
		$msg_body  = $_POST['message'];
	} else {
		$msg_body = "No Message Specified";
	}
	
	sendMail($msg_subject, $msg_recipient, $msg_body);
}
?>