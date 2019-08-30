<?php
$personsClass = new Person();
	
$personsAll = $personsClass->allPersons ("Person");
$personsAllCount = $personsClass->allPersonsCount();

$studentArrayTypes = array('GT', 'GR', 'UG', 'VR', 'PT', 'VD', 'VV', 'VC');

$studentOutput = "";
$studentOutputCount = 0;
$otherOutput = "";
$otherOutputCount = 0;

foreach ($personsAll AS $person) {
	if (in_array($person['university_card_type'], $studentArrayTypes)) {
		$studentOutput .= "<a class=\"mo od tc ra\" href=\"index.php?n=students_unique&cudid=" . $person['cudid'] . "\">";
		$studentOutput .= "<span>" . $person['firstname'] . " " . $person['lastname'] . "</span>";
		//$output .= "<span>" . "test" . "</span>";
		$studentOutput .= "<span class=\"asd\">" . $person['sso_username'] . "</span>";
		$studentOutput .= "</a>";
		
		$studentOutputCount ++;
	} else {
		$otherOutput .= "<a class=\"mo od tc ra\" href=\"index.php?n=students_unique&cudid=" . $person['cudid'] . "\">";
		$otherOutput .= "<span>" . $person['firstname'] . " " . $person['lastname'] . "</span>";
		//$output .= "<span>" . "test" . "</span>";
		$otherOutput .= "<span class=\"asd\">" . $person['sso_username'] . "</span>";
		$otherOutput .= "</a>";
		
		$otherOutputCount ++;
	}
}
?>
<div class="bls">
	<div class="blt">
		<h6 class="blv"><a class="breadcrumb-item" href="index.php">OCSD</a> / <a href="index.php?n=persons_all">Persons</a> / </h6>
		<h2 class="blu">Persons</h2>
	</div>
</div>

<div class="container">
	<nav>
		<div class="nav nav-tabs" id="nav-tab" role="tablist">
			<a class="nav-item nav-link active" id="nav-students-tab" data-toggle="tab" href="#nav-students" role="tab" aria-controls="nav-students" aria-selected="true">Students (<?php echo $studentOutputCount ?>)</a>
			<a class="nav-item nav-link" id="nav-other-tab" data-toggle="tab" href="#nav-other" role="tab" aria-controls="nav-other" aria-selected="false">Non-Students (<?php echo $otherOutputCount ?>)</a>
		</div>
	</nav>
</div>

<div class="tab-content" id="nav-tabContent">
	<div class="tab-pane fade show active" id="nav-students" role="tabpanel" aria-labelledby="nav-students-tab">
		<!--<h6 class="atf">Students</h6>-->
		<?php echo $studentOutput; ?>
	</div>
	<div class="tab-pane fade" id="nav-other" role="tabpanel" aria-labelledby="nav-other-tab">
		<!--<h6 class="atf">Non-Students</h6> -->
		<?php echo $otherOutput; ?>
	</div>
</div>