<?php
$searchTerm = $_POST['navbar_search'];
$filter = array('api_token' => api_token, 'filter' => 'navsearch', 'searchterm' => $searchTerm);
$personsJSON = api_decode("person", "read", $filter);
$personsAll = $personsJSON->body;

$message = $_SESSION["username"] . " searched for  '" . $searchTerm . "' (" . $personsJSON->count . " " . autoPluralise("result)", "results)", $personsJSON->count);
if ($personsJSON->count == 1) {
	$logInsert = (new Logs)->insert("view","success" . $personsAll[0]['cudid'],"Search for <code>" . $searchTerm . "</code> returned " . $personsJSON->count . " result");
} else {
	$logInsert = (new Logs)->insert("view","success",null,"Search for <code>" . $searchTerm . "</code> returned " . $personsJSON->count . " results");
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2"><i class="fas fa-user-friends"></i> Search for <code><?php echo $searchTerm; ?></code> returned  <?php echo $personsJSON->count . autoPluralise(" result", " results", $personsJSON->count); ?></h1>

	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="btn-group mr-2">
			<button type="button" class="btn btn-sm btn-outline-secondary">void</button>
			<button type="button" class="btn btn-sm btn-outline-secondary">void</button>
		</div>

		<div class="dropdown">
			<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-stream"></i> API</button>
			<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
				<form action="/api/person/read.php" method="post">
					<input type="hidden" name="filter" id="filter" value="navsearch" ?>
					<input type="hidden" name="searchterm" id="searchterm" value="<?php echo $searchTerm;?>" ?>
					<button type="submit" name="api_token" value="<?php echo api_token; ?>" class="dropdown-item">NavSearch</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php
$resultsOutput = "";

foreach ($personsAll AS $person) {
	//echo "<div class=\"col-sm\">";
	//echo "<p>" . $person->photoAvatar() . "</p>";
	//echo "</div>";

	$output  = "<tr>";
	$output .= "<td>" . cardTypeBadge($person->university_card_type) . " </td>";
	$output .= "<td><a href=\"index.php?n=persons_unique&cudid=" . $person->cudid . "\">" . $person->firstname . "</a></td>";
	$output .= "<td><a href=\"index.php?n=persons_unique&cudid=" . $person->cudid . "\">" . $person->lastname . "</a></td>";
	$output .= "<td>" . bodcardBadge($person->barcode7, $person->University_Card_End_Dt, false) . "</td>";
	$output .= "<td><a href=\"index.php?n=persons_unique&cudid=" . $person->cudid . "\">" . $person->sso_username . "</a></td>";
	$output .= "</tr>";
	$resultsOutput .= $output;
}
?>

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
