<?php
session_start();

set_include_path('/var/www/html/');


require_once('config.php');

if (debug == true) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(1);
} else {
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(0);
}

require_once('vendor/autoload.php');

use LdapRecord\Container;
use LdapRecord\Connection;
use LdapRecord\Models\Entry;
use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Models\ActiveDirectory\Group;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

// Create a new connection:
$ldap_connection = new Connection([
    'hosts' => [LDAP_SERVER],
    'port' => LDAP_PORT,
    'base_dn' => LDAP_BASE_DN,
    'username' => LDAP_BIND_DN,
		'password' => LDAP_BIND_PASSWORD,
		'use_tls' => LDAP_STARTTLS,
]);
// Add the connection into the container:
Container::addConnection($ldap_connection);

require_once($root . 'includes/globalFunctions.php');
require_once($root . 'includes/db.php');
require_once($root . 'includes/classLogs.php');
require_once($root . 'includes/classPersons.php');
require_once($root . 'includes/classPerson.php');
require_once($root . 'includes/classLDAP.php');
require_once($root . 'includes/classLDAPPerson.php');
require_once($root . 'includes/classSettings.php');
require_once($root . 'includes/classTemplates.php');

$db = new db(db_host, db_username, db_password, db_name);
?>
