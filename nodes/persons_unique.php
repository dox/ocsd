<?php
$person = new Person($_GET['cudid']);

$student = $db->where("cudid", $_GET['cudid']);
$student = $db->getOne("Student");

$applications = $db->where("cudid", $_GET['cudid']);
$applications = $db->getOne("Applications");

$supervisors = $db->where("cudid", $_GET['cudid']);
$supervisors = $db->getOne("Supervisors");

$contactDetails = $db->where ("cudid", $_GET['cudid']);
$contactDetails = $db->get("ContactDetails");
$addresses = $db->where ("cudid", $_GET['cudid']);
$addresses = $db->get("Addresses");

if (isset($person->cudid)) {
	$logInsert = (new Logs)->insert("view","success",$person->cudid,$person->FullName . " record viewed");
} else {
	$logInsert = (new Logs)->insert("view","error",null,"<code>" . $_GET['cudid'] . "</code> record viewed but doesn't exist");
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2"><?php echo $person->cardTypeBadge() . " " . $person->fullName(); ?></h1>
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

<nav>
	<div class="nav nav-tabs" id="nav-tab" role="tablist">
		<a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Home</a>
		<a class="nav-item nav-link" id="nav-addresses-tab" data-toggle="tab" href="#nav-addresses" role="tab" aria-controls="nav-addresses" aria-selected="false">Contact / Addresses</a>
		<a class="nav-item nav-link" id="nav-application-tab" data-toggle="tab" href="#nav-application" role="tab" aria-controls="nav-application" aria-selected="false">Applications</a>
		<a class="nav-item nav-link" id="nav-course-tab" data-toggle="tab" href="#nav-course" role="tab" aria-controls="nav-course" aria-selected="false">Course</a>
		<a class="nav-item nav-link" id="nav-supervisors-tab" data-toggle="tab" href="#nav-supervisors" role="tab" aria-controls="nav-supervisors" aria-selected="false">Supervisors</a>
		<a class="nav-item nav-link" id="nav-signpass-tab" data-toggle="tab" href="#nav-signpass" role="tab" aria-controls="nav-contact" aria-selected="false">Signpass (Beta)</a>
		<a class="nav-item nav-link" id="nav-datadump-tab" data-toggle="tab" href="#nav-datadump" role="tab" aria-controls="nav-contact" aria-selected="false">Data Dump</a>
		<?php if (LDAP_ENABLE == true) { ?><a class="nav-item nav-link" id="nav-ldap-tab" data-toggle="tab" href="#nav-ldap" role="tab" aria-controls="nav-contact" aria-selected="false">LDAP</a><?php } ?>
	</div>
</nav>
<br />

<div class="tab-content" id="nav-tabContent">
	<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
		<?php echo $person->photoCard(); ?>
		<table class="table">
			<thead>
				<tr>
					<th scope="col">Key</th>
					<th scope="col">Value</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($person as $key => $value) {
					if (isset($value)) {
						$output  = "<tr>";
						$output = "<td>" . $key . "</td>";
						$output .= "<td>" . $value . "</td>";
						$output .= "</tr>";

						echo $output;
					}
				}
				?>
			</tbody>
		</table>
		<p>Student Number: <?php echo $person->sits_student_code; ?></p>
		<p>SSO: <?php echo $person->sso_username; ?></p>
		<p>Oxford Email: <?php echo makeEmail($person->oxford_email); ?></p>
		<p>Bodcard: <?php echo $person->barcode7; ?></p>
		<?php
			if (isset($person->internal_tel)) {
				echo "<p>Chorus Telephone Number: " . $person->internal_tel . "</p>";
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
		<p>Course start date: <?php echo $person->crs_start_dt; ?></p>
		<p>Course expected end date: <?php echo $person->crs_exp_end_dt; ?></p>


	</div>
	<div class="tab-pane fade" id="nav-ldap" role="tabpanel" aria-labelledby="nav-ldap-tab">
		<?php include_once("persons_unique-ldap.php"); ?>
	</div>
</div>
</div>
