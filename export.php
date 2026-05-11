<?php
include_once("inc/autoload.php");
requireLogin(); // Redirects if not logged in

if ($user->isLoggedIn()) {
	$requestedPage = $_GET['page'] ?? 'index';
	if (!preg_match('/^[A-Za-z0-9_-]+$/', $requestedPage)) {
		$requestedPage = '404';
	}
} else {
	$requestedPage = 'logon';
}

$pagePath = __DIR__ . "/pages/{$requestedPage}.php";

// Fallback if file doesn’t exist
if (!file_exists($pagePath)) {
	$pagePath = __DIR__ . "/pages/404.php";
}

include_once($pagePath);
?>
