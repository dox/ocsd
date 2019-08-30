<?php
$searchTerm = $_POST['search_term'];
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
	$logSQLInsert = Array ("type" => "SEARCH", "cudid" => $searchResults[0]['cudid'], "description" => $message);

} else {
	$logSQLInsert = Array ("type" => "SEARCH", "description" => $message);
}
$id = $db->insert ('_logs', $logSQLInsert);


?>

<div class="by aaj">
	<h6 class="blv">Search / Search Results</h6>
	<h2 class="blu">Search Results (<?php echo $searchTerm ;?>)</h2>
	<?php
		foreach ($searchResults AS $person) {
		$output  = "<a class=\"mo od tc ra\" href=\"index.php?n=students_unique&cudid=" . $person['cudid'] . "\">";
		$output .= "<span>" . $person['firstname'] . " " . $person['lastname'] . "</span>";
		$output .= "<span>" . "test" . "</span>";
		$output .= "<span class=\"asd\">" . "test" . "</span>";
		$output .= "</a>";
		echo $output;
	}
	?>
</div>