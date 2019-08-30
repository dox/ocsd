<?php
	session_start();
	
	require_once('config.php');
	require_once('includes/globalFunctions.php');
	require_once('database/MysqliDb.php');
	include_once 'includes/classPerson.php';
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/adLDAP/adLDAP.php');
	
	try {
		$adldap
		 = new adLDAP();
	}
	catch (adLDAPException $e) {
	    echo $e;
	    exit();
	}
	
	$db = new MysqliDb ($db_host, $db_username, $db_password, $db_name);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="keywords" content="">
		<meta name="author" content="">
		
		<title>St Edmund Hall, SCR Meal Booking System (TEST)</title>
		
		<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,400italic" rel="stylesheet">
		<link href="/css/toolkit-light.css" rel="stylesheet">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
		<link href="/css/application.css" rel="stylesheet">
</head>