<?php
include_once("../inc/autoload.php");

$cudid = $_POST['cudid'] ?? null;

if (!$cudid) {
	http_response_code(400);
	echo popover('warning', 'LDAP Result', 'Invalid request. No CUD ID provided.');
	exit;
}

$person = new Person(filter_var($_POST['cudid'], FILTER_SANITIZE_STRING));
$randomPassword = generateSecurePassword(6, true);

$newUserArray = array(
	'cn' => $person->FullName,
	'samaccountname' => strtolower($person->sso_username),
	'userprincipalname' => $person->sso_username . LDAP_ACCOUNT_SUFFIX,
	'password' => $randomPassword,
	'ou' => LDAP_BASE_DN,
	'mail' => $person->oxford_email,
	'description' => '\\\\helium\users\%username%',
	'givenname' => $person->firstname,
	'sn' => $person->lastname,
	'displayname' => $person->FullName,
	'pager' => $person->MiFareID
);

if ($ldap->create($newUserArray)) {
	if ($person->isStudent()) {
		$templateMessage = setting('ldap_provision_staff_template');
	} else {
		$templateMessage = setting('ldap_provision_student_template');
	}
	
	$data = [
		'firstname' => $person->firstname,
		'password' => $randomPassword,
		'username' => strtolower($person->sso_username),
	];
	
	$finalMessage = renderTemplate($templateMessage, $data);
	
	$recipients = [
		'to' => [
			'email' => $person->oxford_email
		],
		'cc' => [
			'email' => $person->alt_email
		]
	];
	
	foreach (explode(",", setting('ldap_provision_recipients')) AS $recipient) {
		$recipients['bcc'][] = $recipient;
	}
	
	sendMail('SEH IT Credentials', $recipients, $finalMessage);
	
	$log->create([
		'type' => 'ldap',
		'result' => 'success',
		'description' => "Created LDAP user: " . strtolower($person->sso_username)
	]);
	
	// email
	echo popover('success', 'LDAP Result', 'Success saving user details to LDAP.');
} else {
	$log->create([
		'type' => 'ldap',
		'result' => 'warning',
		'description' => "Failed to create LDAP user: " . strtolower($person->sso_username)
	]);
	echo popover('warning', 'LDAP Result', 'Failed saving user details to LDAP.');
}