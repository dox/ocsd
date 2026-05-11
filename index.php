<?php
include_once("inc/autoload.php");
requireLogin(); // Redirects if not logged in
?>
<!doctype html>
<html lang="en">
	<head>
		<?php include_once("inc/html_head.php"); ?>
	</head>
	<body>
	<?php include_once("inc/view_navbar.php"); ?>
	
	<div class="container my-5">
		<?php
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
		
		include_once("inc/view_footer.php");
		
		?>
	</div>
	
	<script src="js/main.js"></script>
</body>
</html>
