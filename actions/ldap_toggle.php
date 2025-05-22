<?php
include_once("../inc/autoload.php");

$username = $_POST['username'] ?? null;
$action = $_POST['action'] ?? null;

if (!$username || !in_array($action, ['enable', 'disable'])) {
	http_response_code(400);
	echo popover('warning', 'LDAP Result', 'Invalid request. [' . $action . '] is not a valid action.');
	exit;
}

$ldap = new Ldap();
$user = $ldap->findUser($username);

if (!$user) {
	http_response_code(404);
	echo popover('warning', 'LDAP Result', 'User not found. (No username given).');
	exit;
}

try {
	if ($action === 'disable') {
		$ldap->disableAccount($user);
		echo popover('success', 'LDAP Result', $user . ' disabled');
	} else {
		$ldap->enableAccount($user);
		echo popover('success', 'LDAP Result', $user . ' enabled');
	}
} catch (Exception $e) {
	http_response_code(500);
	echo popover('warning', 'LDAP Result', 'LDAP error: ' . $e->getMessage());
}