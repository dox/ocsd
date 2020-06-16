<?php
session_start();
$root = $_SERVER['DOCUMENT_ROOT'];

require_once($root . '/config.php');

if (debug == true) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(1);
} else {
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(0);
}

require_once($root . '/includes/globalFunctions.php');
require_once($root . '/includes/database/MysqliDb.php');
require_once($root . '/includes/classLogs.php');
require_once($root . '/includes/classLog.php');
require_once($root . '/includes/classPersons.php');
require_once($root . '/includes/classPerson.php');
require_once($root . '/includes/adLDAP/adLDAP.php');
require_once($root . '/includes/ldapFunctions.php');

try {
	$adldap
	 = new adLDAP();
}
catch (adLDAPException $e) {
    echo $e;
    exit();
}

$db = new MysqliDb (db_host, db_username, db_password, db_name);
?>
