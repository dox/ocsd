<?php
$searchTerm = $_POST['navbar_search'];
$sql  = "SELECT * FROM Person WHERE ";
$sql .= "sits_student_code LIKE '%" . $searchTerm . "%' OR ";
$sql .= "firstname LIKE '%" . $searchTerm . "%' OR ";
$sql .= "lastname LIKE '%" . $searchTerm . "%' OR ";
$sql .= "alt_email LIKE '%" . $searchTerm . "%' OR ";
$sql .= "sso_username LIKE '%" . $searchTerm . "%' OR ";
$sql .= "university_card_sysis LIKE '%" . $searchTerm . "%' OR ";
$sql .= "barcode7 LIKE '%" . $searchTerm . "%' ";
//$sql .= "ORDER BY date_created ASC";

$searchResults = $db->rawQuery($sql);
$searchResultsCount = $db->count;

$message = $_SESSION["username"] . " searched for  '" . $searchTerm . "' (" . $searchResultsCount . " " . autoPluralise("result)", "results)", $searchResultsCount);
if ($searchResultsCount == 1) {
	$logInsert = (new Logs)->insert("view","success",$searchResults[0]['cudid'],"Search for <code>" . $searchTerm . "</code> returned " . $searchResultsCount . " result");
} else {
	$logInsert = (new Logs)->insert("view","success",null,"Search for <code>" . $searchTerm . "</code> returned " . $searchResultsCount . " results");
}


?>

<div class="container">
<div class="row">
	<?php
	$resultsOutput = "";
	foreach ($searchResults AS $searchResult) {
		$person = new Person($searchResult['cudid']);
		echo "<div class=\"col-sm\">";
		echo "<p>" . $person->photoAvatar() . "</p>";
		echo "</div>";
		
		$resultsOutput .= $person->tableRow();
	}
	?>
</div>
</div>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2"><?php echo count($searchResults) . " Search " . autoPluralise("Result", "Results", count($searchResults)) . " for <code>" . $searchTerm . "</code>";?></h1>
	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="btn-group mr-2">
			<button type="button" class="btn btn-sm btn-outline-secondary">void</button>
			<button type="button" class="btn btn-sm btn-outline-secondary">void</button>
		</div>
		
		<div class="btn-group" role="group">
			<button id="btnGroupDrop1" type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
			<div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1">
				<a class="dropdown-item emailParcelButton1" href="#" id="<?php echo $person->cudid; ?>"><strong>Email</strong> "You have a delivery"</a>
				<a class="dropdown-item" href="#">Dropdown link</a>
			</div>
		</div>
	</div>
</div>

<table class="table">
	<thead>
		<tr>
			<th scope="col"></th>
			<th scope="col">First Name</th>
			<th scope="col">Last Name</th>
			<th scope="col">Bodcard</th>
			<th scope="col">SSO</th>
		</tr>
	</thead>
	<tbody>
		<?php echo $resultsOutput; ?>
	</tbody>
</table>
</div>