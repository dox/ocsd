<?php
$personsClass = new Persons();

if (isset($_GET['filter'])) {
	if ($_GET['filter'] == "students") {
		$persons = $personsClass->allStudents();
	} elseif ($_GET['filter'] == "staff") {
		$persons = $personsClass->allStaff();
	} elseif ($_GET['filter'] == "search") {
		$persons = $personsClass->search($_POST['navbar_search']);
	} else {
		$persons = $personsClass->all();
	}
} else {
	$_GET['filter'] = "all";
	$persons = $personsClass->all();
}
?>

<div class="content">
	<!-- Page title -->
	<div class="page-header">
		<div class="row align-items-center">
			<div class="col-auto">
				<div class="page-pretitle">Filter: <?php echo $_GET['filter']; ?></div>
				<h2 class="page-title" role="heading" aria-level="1"><?php echo count($persons); ?> Persons</h2>
			</div>
			<!-- Page title actions -->
			<div class="col-auto ml-auto d-print-none">
				<span class="d-none d-sm-inline">
					<a href="#" class="btn btn-white">
						New view
					</a>
				</span>
				<a href="#" class="btn btn-primary ml-3 d-none d-sm-inline-block" data-toggle="modal" data-target="#modal-report">
					<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
					Create new report
				</a>
				<a href="#" class="btn btn-primary ml-3 d-sm-none btn-icon" data-toggle="modal" data-target="#modal-report" aria-label="Create new report">
					<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
				</a>
			</div>
		</div>
	</div>

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
				<p class="empty-subtitle text-muted">
					Try adjusting your search or filter to find what you're looking for.
				</p>
			</div>
			<?php
		}
		?>
	</div>
</div>
