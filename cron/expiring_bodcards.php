<?php
include_once("../inc/autoload.php");

$sql = "SELECT cudid FROM Person $whereClause";
$personsAll = $db->get($sql);

$expiryAlertDays = 40;
$now = new DateTime();

$expiringUsers = array();

foreach ($personsAll as $person) {
	$person = new Person($person['cudid']);
	
	
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
