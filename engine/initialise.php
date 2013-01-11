<?php
session_start();
require_once(dirname(__FILE__) . '/config.php');


require_once(dirname(__FILE__) . '/database.php');
require_once(dirname(__FILE__) . '/globalFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/ocsd/modules/students/start.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/ocsd/modules/addresses/start.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/ocsd/modules/countries/start.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/ocsd/modules/qualifications/start.php');
require_once(dirname(__FILE__) . '/adLDAP/adLDAP.php');

try {
    $adldap = new adLDAP();
}
catch (adLDAPException $e) {
    echo $e;
    exit();
}
?>