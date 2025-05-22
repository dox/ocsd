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
	$templateMessage = "<p>Dear {{firstname}}, your College computer account for St Edmund Hall has now been activated, and your details are listed below:</p>
	
	<p>Username: {{username}}</p>
	<p>Password: <code>{{password}}</code> (case sensitive)</p>
	
	<p>This College username (but not the password) is the same as your University username (which you use to access www.office.com [the University email system], SOLO [library catalogue], Webauth [for accessing things such as Canvas, the virtual learning environment] etc.). For more information on your University username, please see https://help.it.ox.ac.uk/webauth/oxfordusername</p>
	
	<h3>WiFI/Internet</h3>
	<p>Your College username/password will grant you access to the SEH WiFi (please 'forget' the SEH Guest WiFi network if you have been using it).</p>
	
	<p>You can also use this username/password to log on to the protected sections of the www.seh.ox.ac.uk website.</p>
	
	<p>Please note that your Internet access is monitored. Downloading of illegal material (such as films or music) is strictly prohibited and, if caught, will be fined &pound;100/offence by the University Information Security team.</p>
	
	<h3>Printing</h3>
	<p>You can either print from any of the onsite computers at St Edmund Hall, or you can log on to http://printing.seh.ox.ac.uk from your own computer.</p>
	
	<p>Paper is available from the Lodge</p>
	
	<h3>Computers</h3>
	<p>The above username/password will log you on to any computer at St Edmund Hall.</p>
	
	<p>If you wish to change your College password, please <a href=\"https://www.seh.ox.ac.uk/it/password/index.php?action=change&login={{username}}\">click here</a></p>
	
	<p>Please note: it is important that you do not share these details with anyone. It is used to track who had access and made changes to specific information. You are responsible for everything done on the system using your username and password.</p>
	
	<h3>SCR Access</h3>
	<p>If you have been granted SCR access you can book meals online by visiting https://scr.seh.ox.ac.uk.</p>
	
	<p>Please direct any queries regarding your battels payments, meal charges or dinner bookings to fees@seh.ox.ac.uk.</p>
	
	<p>For any College-IT issues, please email help@seh.ox.ac.uk</p>
	
	<p>Regards,<br /><br />
	
	IT Office<br />
	St Edmund Hall<br /></p>";
	
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
		],
		'bcc' => [
			'email' => 'andrew.breakspear@seh.ox.ac.uk'
		]
	];
	
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