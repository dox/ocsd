<?php
$persons = new Persons();
	
$personsAll = $persons->all();

$studentArrayTypes = array('GT', 'GR', 'UG', 'VR', 'PT', 'VD', 'VV', 'VC');

$studentOutput = "";
$studentOutputCount = 0;
$otherOutput = "";
$otherOutputCount = 0;

foreach ($personsAll AS $person2) {
	$person = new Person($person2['cudid']);
	
	if (in_array($person->university_card_type, $studentArrayTypes)) {
		$studentOutput .= $person->tableRow();
		$studentOutputCount ++;
	} else {
		$otherOutput .= $person->tableRow();
		$otherOutputCount ++;
	}
}
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2"><i class="fas fa-user-friends"></i> Persons</h1>
	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="btn-group mr-2">
			<button type="button" class="btn btn-sm btn-outline-secondary">void</button>
			<button type="button" class="btn btn-sm btn-outline-secondary">void</button>
		</div>
		
		<button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"><span data-feather="calendar"></span>void</button>
	</div>
</div>

<nav>
	<div class="nav nav-tabs" id="nav-tab" role="tablist">
		<a class="nav-item nav-link active" id="nav-students-tab" data-toggle="tab" href="#nav-students" role="tab" aria-controls="nav-students" aria-selected="true">Students (<?php echo $studentOutputCount ?>)</a>
		<a class="nav-item nav-link" id="nav-other-tab" data-toggle="tab" href="#nav-other" role="tab" aria-controls="nav-other" aria-selected="false">Non-Students (<?php echo $otherOutputCount ?>)</a>
	</div>
</nav>

<div class="tab-content" id="nav-tabContent">
	<div class="tab-pane fade show active" id="nav-students" role="tabpanel" aria-labelledby="nav-students-tab">
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
				<?php echo $studentOutput; ?>
			</tbody>
		</table>
	</div>
	<div class="tab-pane fade" id="nav-other" role="tabpanel" aria-labelledby="nav-other-tab">
		<table class="table">
			<thead>
				<tr>
					<th scope="col">[Type] Name</th>
					<th scope="col">Bodcard</th>
					<th scope="col">SSO</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $otherOutput; ?>
			</tbody>
		</table>
	</div>
</div>