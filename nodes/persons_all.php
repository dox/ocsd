<?php
$filter = array('api_token' => api_token, 'filter' => 'all');
$personsJSON = api_decode("person", "read", $filter);
$personsAll = $personsJSON->body;

$studentArrayTypes = array('GT', 'GR', 'UG', 'VR', 'PT', 'VD', 'VV', 'VC');

$studentOutput = "";
$studentOutputCount = 0;
$otherOutput = "";
$otherOutputCount = 0;

foreach ($personsAll AS $person) {
	$output  = "<tr>";
	$output .= "<td>" . cardTypeBadge($person->university_card_type) . " </td>";
	$output .= "<td><a href=\"index.php?n=persons_unique&cudid=" . $person->cudid . "\">" . $person->firstname . "</a></td>";
	$output .= "<td><a href=\"index.php?n=persons_unique&cudid=" . $person->cudid . "\">" . $person->lastname . "</a></td>";
	$output .= "<td>" . bodcardBadge($person->barcode7, $person->University_Card_End_Dt, false) . "</td>";
	$output .= "<td><a href=\"index.php?n=persons_unique&cudid=" . $person->cudid . "\">" . $person->sso_username . "</a></td>";
	$output .= "</tr>";

	if (in_array($person->university_card_type, $studentArrayTypes)) {
		$studentOutput .= $output;
		$studentOutputCount ++;
	} else {
		$otherOutput .= $output;
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

		<div class="dropdown">
			<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-stream"></i> API</button>
			<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
				<form action="/api/person/read.php" method="post">
					<input type="hidden" name="filter" id="filter" value="all" ?>
					<button type="submit" name="api_token" value="<?php echo api_token; ?>" class="dropdown-item">Read</button>
				</form>
			</div>
		</div>
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
