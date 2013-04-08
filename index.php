<?php
include_once("engine/initialise.php");
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once("views/html_head.php"); ?>

<body>
	<?php
	include_once("views/navigation.php");
	include_once("views/hero.php");
	?>
	<div class="container">
		<?php
		if (isset($_GET['m'])) {
			$fileInclude = "modules/" . $_GET['m'] . "/nodes/" . $_GET['n'];
		} elseif(isset($_GET['n'])) {
			$fileInclude = "nodes/" . $_GET['n'];
		} else {
			$fileInclude = "nodes/index.php";
		}
		if (!isset($_SESSION['username']) && $fileInclude != "nodes/logon.php") {
			$fileInclude = "nodes/logon.php";
		} else {
			gatekeeper();
		}
		include_once($fileInclude);
		?>
		<?php include_once("views/footer.php"); ?>
	</div>
</body>
</html>