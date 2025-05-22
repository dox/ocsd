<?php
function requireLogin() {
	global $user;
	if (!$user->isLoggedIn()) {
		header("Location: login.php");
		exit;
	}
}

function autoPluralise ($singular, $plural, $count = 1) {
	// function to return the correct plural of a word/count combo
	// Usage:	$singular	= single version of the word (e.g. 'Bus')
	//       	$plural 	= plural version of the word (e.g. 'Busses')
	//			$count		= the number you wish to work out the plural from (e.g. 2)
	// Return:	the singular or plural word, based on the count (e.g. 'Jobs')
	// Example:	autoPluralise("Bus", "Busses", 3)  -  would return "Busses"
	//			autoPluralise("Bus", "Busses", 1)  -  would return "Bus"

	return ($count == 1)? $singular : $plural;
}

function printArray($array) {
	echo ("<pre>");
	print_r ($array);
	echo ("</pre>");
}

function setting($name) {
	global $db;
	
	$sql = "SELECT * FROM _settings WHERE name = :name LIMIT 1";
	
	$result = $db->query($sql, [':name' => $name]);
	
	if (empty($result)) {
		return false;
	} else {
		return $result[0]['value'];
	}
}

function alert($type, $title, $content) {
	// List of valid Bootstrap alert types
	$validTypes = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];

	// Ensure the provided type is valid, default to 'info' if invalid
	if (!in_array($type, $validTypes)) {
		$type = 'info';  // Default type if the passed type is not valid
	}

	// Generate the alert HTML
	$output  = "<div class=\"alert alert-$type alert-dismissible fade show\" role=\"alert\">";
	$output .= "<strong>" . ucfirst($title) . "</strong> "; // Capitalize the alert type
	$output .= $content;
	$output .= "<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>";
	$output .= "</div>";
	
	return $output;
}

function popover($type = null, $title = null, $text = null) {
	// List of valid Bootstrap alert types
	$validTypes = ['success', 'warning', 'info', 'unknown'];
	
	// Ensure the provided type is valid, default to 'info' if invalid
	if (!in_array($type, $validTypes)) {
		$type = 'unknown';  // Default type if the passed type is not valid
	}
	
	$iconArray = array(
		'success' => 'check-circle',
		'warning' => 'exclamation-circle',
		'info' => 'info-circle',
		'unknown' => 'question-diamond'
	);
	
	$output  = "<button type=\"button\" class=\"btn btn-link py-0 px-2\" data-bs-toggle=\"popover\" data-bs-title=\"" . $title . "\" data-bs-content=\"" . $text . "\">";
	$output .= icon($iconArray[$type]);
	$output .= "</button>";
	
	return $output;
}

function icon(string $iconName, string $size = '16'): string {
	$iconPath = $_SERVER["DOCUMENT_ROOT"] . '/icons/' . $iconName . '.svg';
	
	// Check if the requested SVG file exists
	if (file_exists($iconPath)) {
		// Load the SVG content
		$svgContent = file_get_contents($iconPath);
		
		// Replace existing width/height
		$svgContent = preg_replace('/(width|height)="\d+(\.\d+)?"/', '$1="' . $size . '"', $svgContent);
		
		// If missing, inject width/height into <svg> tag
		if (!preg_match('/width="\d/', $svgContent)) {
			$svgContent = preg_replace('/<svg\b([^>]*)>/', '<svg$1 width="' . $size . '"', $svgContent, 1);
		}
		if (!preg_match('/height="\d/', $svgContent)) {
			$svgContent = preg_replace('/<svg\b([^>]*)>/', '<svg$1 height="' . $size . '"', $svgContent, 1);
		}

		return $svgContent;
	}
	
	// If the file doesn't exist, return a default SVG
	$defaultIconPath = $_SERVER["DOCUMENT_ROOT"] . '/icons/question-diamond.svg';
	
	if (file_exists($defaultIconPath)) {
		$defaultSvgContent = file_get_contents($defaultIconPath);
		
		// Adjust size of the default SVG
		$defaultSvgContent = preg_replace('/(width|height)="\d+(\.\d+)?"/', '$1="' . $size . '"', $defaultSvgContent);

		return $defaultSvgContent;
	}

	// If the default SVG is missing as well, return a simple placeholder SVG
	return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" fill="currentColor"><rect width="100%" height="100%" /></svg>';
}

function daysSince($oldDate) {
	$now = new DateTime();  // Current time
	$oldDate = new DateTime($oldDate);  // Convert the passed date to a DateTime object

	$interval = $now->diff($oldDate);  // Get the difference between now and the old date

	return $interval->days;  // Return the number of days
}

function convertDateToWinTime($date) {
	// Convert to Unix timestamp
	$unixTime = strtotime($date);
	
	// Epoch difference between Unix and WinTime (1601-01-01)
	$epochDiff = 11644473600;  // 11644473600 seconds between Unix epoch and WinTime epoch

	// Convert Unix time to WinTime (in 100-nanosecond intervals)
	$winTime = ($unixTime + $epochDiff) * 10000000; // Multiply by 10,000,000 to get 100-nanosecond intervals

	return $winTime;
}

function timeAgo($timestamp) {
	$time = time() - $timestamp; // Get the difference between current time and the given timestamp
	
	// Define time units
	$units = [
		'year' => 31536000,  // 365 days in seconds
		'month' => 2592000,  // 30 days in seconds
		'day' => 86400,      // 1 day in seconds
		'hour' => 3600,      // 1 hour in seconds
		'minute' => 60,      // 1 minute in seconds
		'second' => 1        // 1 second
	];

	// Loop through units and determine the time ago
	foreach ($units as $unit => $value) {
		$unitTime = floor($time / $value);
		if ($unitTime >= 1) {
			$timeAgo = $unitTime . ' ' . $unit . ($unitTime > 1 ? 's' : '') . ' ago';
			break;
		}
	}
	
	return $timeAgo;
}

function cliOutput($message = null, $colour = null) {
	if ($colour == "black") {
		$colour = "30m";
	} elseif ($colour == "red") {
		$colour = "31m";
	} elseif ($colour == "green") {
		$colour = "32m";
	} elseif ($colour == "yellow") {
		$colour = "33m";
	} elseif ($colour == "blue") {
		$colour = "34m";
	} elseif ($colour == "magenta") {
		$colour = "35m";
	} elseif ($colour == "cyan") {
		$colour = "36m";
	} elseif ($colour == "white") {
		$colour = "97m";
	} else {
		$colour = "39m";
	}
	
	$message = "\033[" . $colour . $message . "\n";
	
	echo $message;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendMail($subject, $recipients = NULL, $body = NULL) {
	global $log, $mail;
	
	try {
		//Server settings
		$mail->isSMTP();
		$mail->Host       = smtp_server;
		$mail->Port       = smtp_port;
	
		
		$mail->setFrom(smtp_sender_address, smtp_sender_name);
		$mail->addReplyTo(smtp_sender_address, smtp_sender_name);
		
		//Recipients
		foreach ($recipients['to'] AS $recipient) {
			if (isset($recipient)) {
				$mail->addAddress($recipient);
			}
		}
		
		foreach ($recipients['cc'] AS $recipient) {
			if (isset($recipient)) {
				$mail->addCC($recipient);
			}
		}
		
		foreach ($recipients['bcc'] AS $recipient) {
			if (isset($recipient)) {
				$mail->addBCC($recipient);
			}
		}
		
		//Attachments
		//$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
		//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
	
		//Content
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body    = $body;
	
		$mail->send();
		
		foreach ($recipients as $type => $details) {
			if (isset($details['email'])) {
				$emails[] = $details['email'];
			}
		}
		
		$logData = [
			'category' => 'email',
			'result'   => 'success',
			'description' => 'Email sent to: ' . implode(', ', $emails)
		];
		$log->create($logData);
	} catch (Exception $e) {
		$logData = [
			'category' => 'email',
			'result'   => 'danger',
			'description' => 'Email failed to: ' . implode(', ', $emails) . '. Mailer Error: ' . $mail->ErrorInfo
		];
		$log->create($logData);
	}
}

function renderTemplate(string $template, array $variables): string {
	foreach ($variables as $key => $value) {
		// Replace {{key}} with value, using str_replace
		$template = str_replace('{{' . $key . '}}', $value, $template);
	}
	return $template;
}

function generateSecurePassword($length = 12, $includeSymbols = true): string {
	$lower = 'abcdefghijklmnopqrstuvwxyz';
	$upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$numbers = '0123456789';
	$symbols = '!@#$%^&*()-_=+[]{}|;:,.<>?';
	
	$characters = $lower . $upper . $numbers;
	if ($includeSymbols) {
		$characters .= $symbols;
	}
	
	$charactersLength = strlen($characters);
	$password = '';
	
	for ($i = 0; $i < $length; $i++) {
		$index = random_int(0, $charactersLength - 1);
		$password .= $characters[$index];
	}
	
	return $password;
}
?>
