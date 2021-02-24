<?php
$personsClass = new Persons();

if (isset($_GET['filter'])) {
	if ($_GET['filter'] == "students") {
		$persons = $personsClass->allStudents();
	} elseif ($_GET['filter'] == "staff") {
		$persons = $personsClass->allStaff();
	} elseif ($_GET['filter'] == "search") {
		$persons = $personsClass->search($_POST['navbar_search']);
	} elseif ($_GET['filter'] == "suspended") {
		$persons = $personsClass->suspendedPersons();
	} else {
		$persons = $personsClass->all();
	}
} else {
	$_GET['filter'] = "all";
	$persons = $personsClass->all();
}

$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"images/icons.svg#bell\"/></svg> Test Button", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteMealModal\"");
$icons[] = array("class" => "btn-warning", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"images/icons.svg#email\"/></svg> Test Button", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteMealModal\"");

echo displayTitle(count($persons) . " Persons", "Filter: " . $_GET['filter'], $icons);
?>

<div class="row">
	<?php
	if ($persons) {
		foreach ($persons AS $personUnique) {
			$person = new Person($personUnique['cudid']);
			echo "<div class=\"col-md-6 col-lg-4\">";
			echo $person->makeListItem();
			echo "</div>";
		}
	} else {
	?>
	<div class="empty">
		<div class="empty-icon">
			<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="10" cy="10" r="7"></circle><line x1="21" y1="21" x2="15" y2="15"></line></svg>
		</div>
		<p class="empty-title h3">No results found</p>
		<p class="empty-subtitle text-muted">Try adjusting your search or filter to find what you're looking for.</p>
	</div>
	<?php } ?>
</div>
