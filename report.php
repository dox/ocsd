<?php
include_once("engine/initialise.php");
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once("views/html_head.php"); ?>

<body>
<div class="container">
	<div class="row">
		<div class="span12">

<?php

if (isset($_GET['header'])) {
	if ($_GET['header'] == 'true') {
		include_once("modules/reports/views/header.php");
	}
}

echo "<p>" . date('d F Y') . "</p>";

if (isset($_GET['n'])) {
	$fileInclude = "modules/reports/nodes/" . $_GET['n'];
}

include_once($fileInclude);

?>
		</div>
	</div>
</div>
</body>
</html>