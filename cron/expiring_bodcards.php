<?php
include_once("../inc/autoload.php");

$cudPersons = (new Persons())->all();

$expiryAlertDays = 40;
$now = new DateTime();

$expiringUsers = array();

foreach ($cudPersons as $person) {
	// ignore students
	if ($person->isStudent()) {
		continue;
	}
	
	$expiryDate = DateTime::createFromFormat('Ymd', $person->University_Card_End_Dt);

	if ($expiryDate !== false) {
		// Calculate difference
		$diff = $now->diff($expiryDate);

		// $diff->invert === 0 means expiryDate is in the future
		if ($diff->invert === 0 && $diff->days <= $expiryAlertDays) {
			$expiringUsers[] = $person->FullName . " (" . $person->sso_username . ") expires in " . $diff->days . autoPluralise(" day", " days", $diff->days);
		}
	}
}

if (count($expiringUsers) > 0) {
	$message = "<p>The following users have expiring Bodcards in the next " . $expiryAlertDays . " days</p>";
	$message .= implode("<br>", $expiringUsers);
	
	$recipients = [
		'to' => [
			'email' => 'human.resources@seh.ox.ac.uk'
		]
	];
	
	sendMail('Expiring Staff Bodcards', $recipients, $message);
	
	echo $message;
}
?>
