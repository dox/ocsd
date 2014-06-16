<?php
/*
if (isset($_GET['m']) || isset($_GET['n'])) {
	$output  = "<header class=\"headerPage\" id=\"overview\">";
	$output .= "<div class=\"container\">";
	$output .= "</div>";
	$output .= "</header>";
} else {
	$output  = "<header class=\"headerMain\" id=\"overview\">";
	$output .= "<div class=\"container\">";
	$output .= "<h1>" . SITE_NAME . "</h1>";
	$output .= "<p class=\"lead\">" . SITE_SLOGAN . "</p>";
	$output .= "</div>";
	$output .= "</header>";
}

echo $output;
*/
?>

<div class="jumbotron">
	<div class="container">
		<h1><?php echo SITE_SHORT_NAME; ?></h1>
		<p><?php echo SITE_SLOGAN; ?></p>
		<p><a class="btn btn-primary btn-lg" role="button">Please note this site is currently in development.</a></p>
	</div>
</div>