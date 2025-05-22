<?php
include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../config.php';

session_start();

if (debug) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(1);
} else {
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(0);
}

include __DIR__ . '/global.php';
include __DIR__ . '/database.php';
include __DIR__ . '/class_settings.php';
include __DIR__ . '/class_logs.php';
include __DIR__ . '/class_ldap.php';
include __DIR__ . '/class_ldap_user.php';
include __DIR__ . '/class_user.php';

include __DIR__ . '/class_person.php';
include __DIR__ . '/class_Addresses.php';
include __DIR__ . '/class_Applications.php';
include __DIR__ . '/class_Enrolments.php';
include __DIR__ . '/class_CollegeFees.php';
include __DIR__ . '/class_CoOwningDepartments.php';
include __DIR__ . '/class_ExternalIds.php';
include __DIR__ . '/class_EnrolAwdProg.php';
include __DIR__ . '/class_TheResDeg.php';
include __DIR__ . '/class_Qualifications.php';
include __DIR__ . '/class_Supervisors.php';
include __DIR__ . '/class_Suspensions.php';
include __DIR__ . '/class_YearsOfAwdProg.php';

// One shared LDAP connection
$ldap = new Ldap();

// Currently logged in user
$user = new User($ldap);

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
$mail = new PHPMailer(true);
?>