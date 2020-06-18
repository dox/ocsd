<?php
function printArray($array) {
	echo ("<pre>");
	print_r ($array);
	echo ("</pre>");
}

function convertToDateString($dateString, $time = false) {
	if ($time == "true") {
		$dateFormat = "d F Y H:i:s";
	} else {
		$dateFormat = "d F Y";
	}

	if ($dateString == "") {
		$dateString = date('Y-m-d H:i:s');
	}

	$date = strtotime($dateString);
	$returnDate = date($dateFormat, $date);

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

function makeEmail($address) {
	$output  = "<a href=\"mailto:" . $address . "\">" . $address . "</a>";
	return $output;
}

function sendMail($subject = "No Subject Specified", $recipients = NULL, $body = NULL, $senderAddress = NULL, $senderName = NULL) {
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/PHPMailer/class.phpmailer.php');

	$mail = new PHPMailer;

	$mail->IsSMTP();
	$mail->Host = smtp_server;
	$mail->IsHTML(true);

	if (isset($senderAddress)) {
		$mail->From = $senderAddress;
		$mail->FromName = $senderName;
		$mail->AddReplyTo($senderAddress, $senderName);
	} else {
		$mail->From = "communications@seh.ox.ac.uk";
		$mail->FromName = "SEH Communications";
		$mail->AddReplyTo("communications@seh.ox.ac.uk", "SEH Communications");

	}

	//$recipients = explode(",", $recipients);

	//echo $recipients;

	//$mail->AddAddress("noreply@seh.ox.ac.uk");
	foreach ($recipients AS $recipient) {
		$mail->addBCC($recipient);
	}




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

	//echo 'Message has been sent';
}

function isingroup($groupname) {
	$username = $_SESSION['username'];
	$usergroups = $_SESSION['userinfo'][0]['memberof'];

	//clean the LDAP group names up
	foreach ($usergroups AS $group) {
		if (strlen($group) > 1) {
			$firstcomma = strpos($group, ",");
			$firstCN = strpos($group, "CN=");
			$name = substr($group, $firstCN + 3, $firstcomma - 3);

			$groupArray[] = $name;
		}
	}

	// check each LDAP group this user is a member of against $groupnamecheck
	foreach ($groupArray AS $group) {
		if ($groupname == $group) {
			return true;
			break;
		}
	}
}

function generateRandomString($string = null) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    $stringLengthMin = floor(strlen($string)/0.5);
    $stringLengthMax = ceil(strlen($string)*0.5);
    $stringLength = rand($stringLengthMin,$stringLengthMax);

    for ($i = 0; $i < $stringLength; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function gatekeeper($groupnamecheck = "All Staff") {
	if (isingroup($groupnamecheck)) {
	} else {
		echo "SECURITY ACCESS DENIED";
		exit;
	}
}

function curPageURL() {
	$pageURL = 'http';

	if ($_SERVER["HTTPS"] == "on") {
		$pageURL .= "s";
	}

	$pageURL .= "://";

	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}

	return $pageURL;
}
?>
