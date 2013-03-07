<?php
function printArray($array) {
	echo ("<pre>");
	print_r ($array);
	echo ("</pre>");
}

function convertToDateString($dateString, $time = false) {
	if ($time == "true") {
		$dateFormat = "Y-m-d H:i:s";
	} else {
		$dateFormat = "Y-m-d";
	}
	
	if ($dateString == "") {
		$returnDate = "Unknown";
	} else {
		$date = strtotime($dateString);
		$returnDate = date($dateFormat, $date);
	}
	
	return $returnDate;
}


function age($date = NULL) {
	if ($birthDate == NULL) {
		$birthDate = date('Y-m-d');
	}
	
	return intval(substr(date('Ymd') - date('Ymd', strtotime($date)), 0, -4));
}

function autoPluralise ($singular, $plural, $count = 1) {
	// fantasticly clever function to return the correct plural of a word/count combo
	// Usage:	$singular	= single version of the word (e.g. 'Bus')
	//       	$plural 	= plural version of the word (e.g. 'Busses')
	//			$count		= the number you wish to work out the plural from (e.g. 2)
	// Return:	the singular or plural word, based on the count (e.g. 'Jobs')
	// Example:	autoPluralise("Bus", "Busses", 3)  -  would return "Busses"
	//			autoPluralise("Bus", "Busses", 1)  -  would return "Bus"

	return ($count == 1)? $singular : $plural;
} // END function autoPluralise


function sendMail($subject = "No Subject Specified", $recipients = NULL, $body = NULL) {
	require_once($_SERVER['DOCUMENT_ROOT'] . '/ocsd/engine/PHPMailer/class.phpmailer.php');
	
	$mail = new PHPMailer;
	
	$mail->IsSMTP();
	$mail->Host = EMAIL_HOST;
	
	$mail->From = EMAIL_FROM;
	$mail->FromName = SITE_SHORT_NAME;
	$mail->AddAddress('andrew.breakspear@seh.ox.ac.uk', 'Andrew Breakspear');
	$mail->AddReplyTo(EMAIL_FROM, SITE_ADMIN_NAME);
	
	$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
	//$mail->AddAttachment('/var/tmp/file.tar.gz');         // Add attachments
	//$mail->AddAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
	$mail->IsHTML(true);                                  // Set email format to HTML
	
	$mail->Subject = $subject;
	$mail->Body    = $body;
	//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
	
	if(!$mail->Send()) {
	   echo 'Message could not be sent.';
	   echo 'Mailer Error: ' . $mail->ErrorInfo;
	   exit;
	}
	
	echo 'Message has been sent';
}
?>