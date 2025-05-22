<?php
require_once 'inc/autoload.php';

// One shared LDAP connection
$ldap = new Ldap();

// User instance can be reused as needed
$user = new User($ldap);

$username = 'breakspear' ?? '';
$password = 'P!ssport7dd' ?? '';

if ($user->authenticate($username, $password)) {
	header('Location: /dashboard.php');
	exit;
} else {
	echo "Login failed: " . $ldap->getLastError();
}
echo "EOF";

?>
