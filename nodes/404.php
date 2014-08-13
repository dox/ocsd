<style>
body {
background: url('img/404-02.jpg') no-repeat center center fixed;
-webkit-background-size: cover;
-moz-background-size: cover;
-o-background-size: cover;
background-size: cover;
color: white;
}
</style>

<h1>Page Not Found</h1>
<blockquote>
	<p>You can't always get what you want.</p>
	<small>The Rolling Stones</small>
</blockquote>
<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />

<?php
// find out the page the user came from and record this 404 in the logs
$page = $_SERVER['HTTP_REFERER'];

$log = new Logs;
$log->notes			= "404: ". $page;
$log->type			= "error";
$log->create();

?>