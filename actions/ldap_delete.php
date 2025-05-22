<?php
include_once("../inc/autoload.php");

$username = $_POST['username'] ?? null;

if (!$username) {
	http_response_code(400);
	echo popover('warning', 'LDAP Result', 'Invalid request. No username given.');
	exit;
}

$ldap = new Ldap();
$user = $ldap->findUser($username);

if (!$user) {
	http_response_code(404);
	echo popover('warning', 'LDAP Result', 'Invalid request. User not found.');
	exit;
}

try {
	$ldap->deleteAccount($user);
	echo popover('success', 'LDAP Result', 'User deleted.');
} catch (Exception $e) {
	http_response_code(500);
	echo popover('warning', 'LDAP Result', 'LDAP error: ' . $e->getMessage());
}