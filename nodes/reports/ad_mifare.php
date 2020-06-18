<?php
$personsClass = new Persons();

$personsAll = $personsClass->all();

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

<code>
	<?php
	foreach ($personsAll AS $person) {

		if (isset($person['sso_username']) AND isset($person['oxford_email'])) {
			$output  = "\$found= Get-ADUser -Filter \"SamAccountName -eq '" . $person['sso_username'] . "' -or mail -eq '" . $person['oxford_email'] . "'\" <br />";
			$output .= "if(\$found){<br />";

			$output .= "\$found|Set-ADUser -Replace @{pager = '" . $person['MiFareID'] . "'; mail = '" . $person['oxford_email'] . "'} -Verbose <br />";
			$output .= "}<br/><br/>";

			echo $output;

		} else {
			// user is not a student
		}
		/*
			$output  = "Set-ADUser -Identity " . $person['sso_username'] . " ";
		$output .= "-Replace @{pager ='" . $person['MiFareID'] . "'}";
		$output .= "<br />";

		echo $output;
		*/
	}

	?>
</code>
