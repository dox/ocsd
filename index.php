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
		}
		include_once($fileInclude);
		?>
		<?php include_once("views/footer.php"); ?>
	</div>
	
	<script src="js/jquery.js"></script>
	<script src="js/bootstrap.js"></script>
	<script src="js/bootstrap-typeahead.js"></script>
</body>
</html>

<?php
//navigation quick search
$allStudents = Students::find_all();

if (count($allStudents) > 0) {
	foreach ($allStudents AS $student) {
		$name = str_replace("'", "", $student->fullDisplayName());
		//$name = htmlspecialchars($name, ENT_QUOTES);
		
		$searchOutput[] = "{id: " . $student->studentid . ", name: '" . $name . "'}";
	}
}
?>
<script>
$(document).ready(function() {
	var usersAhead = [<?php echo implode(",", $searchOutput);?>];
	
	$('#searchAhead').typeahead({
		source: usersAhead,
		matchProp: 'name',
		sortProp: 'name',
		valueProp: 'id',
		itemSelected: function (item) {
			// go to user_overview.php and pass the userUID var in the $_GET
			location.href = "index.php?m=students&n=user.php&studentid=" + item
		}
	});
});
</script>