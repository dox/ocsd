<?php

$student = $db->where("cudid", $_GET['cudid']);
$student = $db->getOne("Student");
$person = $db->where("cudid", $_GET['cudid']);
$person = $db->getOne("Person");

$applications = $db->where("cudid", $_GET['cudid']);
$applications = $db->getOne("Applications");

$supervisors = $db->where("cudid", $_GET['cudid']);
$supervisors = $db->getOne("Supervisors");

$contactDetails = $db->where ("cudid", $_GET['cudid']);
$contactDetails = $db->get("ContactDetails");
$addresses = $db->where ("cudid", $_GET['cudid']);
$addresses = $db->get("Addresses");

$logSQLInsert = Array ("type" => "VIEW", "cudid" => $person["cudid"], "description" => $_SESSION["username"] . " viewed " . $person['FullName']);
$id = $db->insert ('_logs', $logSQLInsert);

if ($person['firstname'] <> $person['known_as']) {
	$name = $person['FullName'] . " (" . $person['known_as'] . ")";
} else {
	$name = $person['FullName'];
}
?>
<div class="bls">
	<div class="blt">
		<h6 class="blv"><a class="breadcrumb-item" href="index.php">OCSD</a> / <a href="index.php?n=persons_all">Persons</a> / <a href="index.php?n=students_unique&cudid=<?php echo $person['cudid'];?>"><?php echo $name; ?></a> / </h6>
		<h2 class="blu"><?php echo $name; ?></h2>
	</div>
</div>





<div class="container">
	<nav>
		<div class="nav nav-tabs" id="nav-tab" role="tablist">
			<a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Home</a>
			<a class="nav-item nav-link" id="nav-addresses-tab" data-toggle="tab" href="#nav-addresses" role="tab" aria-controls="nav-addresses" aria-selected="false">Contact / Addresses</a>
			<a class="nav-item nav-link" id="nav-application-tab" data-toggle="tab" href="#nav-application" role="tab" aria-controls="nav-application" aria-selected="false">Applications</a>
			<a class="nav-item nav-link" id="nav-course-tab" data-toggle="tab" href="#nav-course" role="tab" aria-controls="nav-course" aria-selected="false">Course</a>
			<a class="nav-item nav-link" id="nav-supervisors-tab" data-toggle="tab" href="#nav-supervisors" role="tab" aria-controls="nav-supervisors" aria-selected="false">Supervisors</a>
			<a class="nav-item nav-link" id="nav-signpass-tab" data-toggle="tab" href="#nav-signpass" role="tab" aria-controls="nav-contact" aria-selected="false">Signpass (Beta)</a>
			<a class="nav-item nav-link" id="nav-datadump-tab" data-toggle="tab" href="#nav-datadump" role="tab" aria-controls="nav-contact" aria-selected="false">Data Dump</a>
		</div>
	</nav>
</div>
<div class="tab-content" id="nav-tabContent">
	<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
		<img src="../photos/UAS_UniversityCard-<?php echo $person['university_card_sysis']; ?>.jpg" class="img-thumbnail float-right" style="max-height: 400px; max-width: 400px;" alt="Bodcard Photo">
		<p>Student Type: <?php echo $person['university_card_type']; ?></p>
		<p>Student Number: <?php echo $person['sits_student_code']; ?></p>
		<p>SSO: <?php echo $person['sso_username']; ?></p>
		<p>Oxford Email: <?php echo $person['oxford_email']; ?></p>
		<p>Bodcard: <?php echo $person['barcode']; ?></p>
		<?php
			if (isset($person['internal_tel'])) {
				echo "<p>Chorus Telephone Number: " . $person['internal_tel'] . "</p>";
			}
		?>
			</div>
	<div class="tab-pane fade" id="nav-addresses" role="tabpanel" aria-labelledby="nav-addresses-tab">
		<?php
		foreach ($contactDetails AS $contact) {
			$output  = "<div class=\"card\">";
			$output .= "<div class=\"card-body\">";
				$output .= "<strong>" . $contact['SubType'] . "</strong> " . $contact['Value'];
			$output .= "</div>";
			$output .= "</div>";
			
			echo $output;
		}
		
		foreach ($addresses AS $address) {
			if ($address["AddressTyp"] == "T") {
				$addressType = "Term-Time";
			} elseif ($address["AddressTyp"] == "C") {
				$addressType = "Contact";
			} elseif ($address["AddressTyp"] == "Z") {
				$addressType = "UNKNOWN";
			} elseif ($address["AddressTyp"] == "H") {
				$addressType = "Home";
			} else {
				$addressType = "Other";
			}
			$output  = "<div class=\"card\" style=\"width: 28rem;\">";
			$output .= "<div class=\"card-body\">";
			$output .= "<h5 class=\"card-title\">" . $addressType . "</h5>";
			$output .= "<h6 class=\"card-subtitle mb-2 text-muted\"><span class=\"badge badge-light\">Last updated: " . (date('Y-m-d', strtotime($address["LastUpdateDt"]))) . "</span></h6>";
			$output .= "<p class=\"card-text\">";
				if ($address["Line1"]) { $output .= $address["Line1"] . "<br />"; }
				if ($address["Line2"]) { $output .= $address["Line2"] . "<br />"; }
				if ($address["Line3"]) { $output .= $address["Line3"] . "<br />"; }
				if ($address["Line4"]) { $output .= $address["Line4"] . "<br />"; }
				if ($address["Line5"]) { $output .= $address["Line5"] . "<br />"; }
				if ($address["City"]) { $output .= $address["City"] . "<br />"; }
				if ($address["PostCode"]) { $output .= $address["PostCode"] . "<br />"; }
				if ($address["State"]) { $output .= $address["State"] . "<br />"; }
				if ($address["County"]) { $output .= $address["County"] . "<br />"; }
				if ($address["AddressCtryDesc"]) { $output .= $address["AddressCtryDesc"] . "<br />"; }
			$output .= "<ul class=\"list-group list-group-flush\">";
				if ($address["AddressEmail"]) { $output .= "<li class=\"list-group-item\"><strong>Email:</strong> " . $address['AddressEmail'] . "</li>"; }
				if ($address["TelNo"]) { $output .= "<li class=\"list-group-item\"><strong>Telephone:</strong> " . $address['TelNo'] . "</li>"; }
				if ($address["MobileNo"]) { $output .= "<li class=\"list-group-item\"><strong>Mobile:</strong> " . $address['MobileNo'] . "</li>"; }
			$output .= "</ul>";
			$output .= "<a href=\"https://www.google.co.uk/maps?q=" . $address["Line1"] . "," . $address["Line2"] . "," . $address["PostCode"] . "," . $address["County"] . "\" class=\"card-link\"><i class=\"fas fa-map-marker-alt\"></i> Google Maps</a>";
			$output .= "</div>";
			$output .= "</div>";
			
			echo $output;
		}
		?>
	</div>
	<div class="tab-pane fade" id="nav-datadump" role="tabpanel" aria-labelledby="nav-datadump-tab">
		<h2>Student Table:</h2>
		<pre><?php print_r($student); ?></pre>
		
		<h2>Person Table:</h2>
		<pre><?php print_r($person); ?></pre>
	</div>
	<div class="tab-pane fade" id="nav-supervisors" role="tabpanel" aria-labelledby="nav-supervisors-tab">
		<pre><?php print_r($supervisors); ?></pre>
	</div>
	<div class="tab-pane fade" id="nav-application" role="tabpanel" aria-labelledby="nav-application-tab">
		<h2>Applications Table:</h2>
		<pre><?php print_r($applications); ?></pre>
	</div>
	<div class="tab-pane fade" id="nav-signpass" role="tabpanel" aria-labelledby="nav-signpass-tab">
		<h2>Sign Pass</h2>
		<pre><?php include_once("signpass.php"); ?></pre>
	</div>
	<div class="tab-pane fade" id="nav-course" role="tabpanel" aria-labelledby="nav-course-tab">
		<p>Year of Study: <?php echo $applications['YearOfProg']; ?></p>
		<p>Student Type: <?php echo $applications['CrsLevel']; ?></p>
		<p>Course Name: <?php echo $applications['CrsName']; ?></p>
		<p>Award Type: <?php echo $applications['AwdName']; ?></p>
		<p>Course start date: <?php echo $person['crs_start_dt']; ?></p>
		<p>Course expected end date: <?php echo $person['crs_exp_end_dt']; ?></p>


	</div>
</div>
</div>
