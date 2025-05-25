<?php
$person = new Person(filter_var($_GET['cudid'], FILTER_SANITIZE_STRING));

$data = array(
		'icon'		=> 'person',
		'title'		=> $person->FullName,
		'subtitle'	=> 'cudid: ' . $person->cudid,
		'badge'		=> $person->getTypeBadge(),
		'actions'	=> array (
			$person->actionsButton()
		)
);
echo pageTitle($data);
?>

<div class="row">
	<div class="col-md-4">
		<div class="profile-img">
			<img class="rounded w-100 mb-3" src="<?php echo $person->photograph(); ?>" alt="">
		</div>
		
		<h4 class="mb-3">Core Credentials</h4>
		SSO: <strong><?php echo $person->sso_username; ?></strong><br>
		Bodcard: <strong><?php echo $person->barcode7; ?></strong> <i><?php echo "Expires " . date('Y-m-d', strtotime($person->University_Card_End_Dt)); ?></i><br>
		<?php
		if (!empty($person->dob)) {
			echo "DOB: <strong>" . date('Y-m-d', strtotime($person->dob)) . "</strong> <i> Age " . ($person->age()) . "</i><br>";
		}
		
		if (!empty($person->gnd)) {
			if ($person->gnd == "M") {
				$colour = "gender-male";
			} elseif ($person->gnd == "F") {
				$colour = "gender-female";
			} else {
				$colour = "gender-other";
			}
			
			$pronouns = "";
			if (isset($person->consolidated_pronouns)) {
				$pronouns = " <i>(" . $person->consolidated_pronouns . ")</i>";
			}
			echo "Gender: <span class=\"badge " . $colour . "\">" . $person->gnd . "</span>" . $pronouns . "<br>";
		}
		?>
		Email: <strong><?php echo ($person->oxford_email); ?></strong><br>
		Email2: <strong><?php echo ($person->alt_email); ?></strong><br>
		MiFare: <strong><?php echo $person->MiFareID; ?></strong><br>
		Paxon: <strong><?php echo $person->PaxonID; ?></strong><br>
		SITS: <strong><?php echo $person->sits_student_code; ?></strong><br>
		SysIS: <strong><?php echo $person->university_card_sysis; ?></strong>
	</div>
	<div class="col-md-8">
		<div class="profile-head">
			<?php
			if (isset($person->rout_name) || isset($person->div_desc)) {
				echo "<h4>" . $person->rout_name . ", " . $person->div_desc . "</h4>";
			}
			?>
			
			<ul class="nav nav-pills mb-3" id="myTab" role="tablist">
				<?php
				$tabsArray = array(
					array(
						'name' => 'Suspensions',
						'url' => '/pages/tabs/Suspensions.php',
						'clean_name' => 'Suspensions'
					),
					array(
						'name' => 'Supervisors',
						'url' => '/pages/tabs/Supervisors.php',
						'clean_name' => 'Supervisors'
					),
					array(
						'name' => 'Applications',
						'url' => '/pages/tabs/Applications.php',
						'clean_name' => 'Applications'
					),
					array(
						'name' => 'Enrolments',
						'url' => '/pages/tabs/Enrolments.php',
						'clean_name' => 'Enrolments'
					),
					array(
						'name' => 'Addresses',
						'url' => '/pages/tabs/Addresses.php',
						'clean_name' => 'Addresses'
					),
					array(
						'name' => 'CollegeFees',
						'url' => '/pages/tabs/CollegeFees.php',
						'clean_name' => 'College Fees'
					),
					array(
						'name' => 'CoOwningDepartments',
						'url' => '/pages/tabs/CoOwningDepartments.php',
						'clean_name' => 'Co-owning Departments'
					),
					array(
						'name' => 'ExternalIds',
						'url' => '/pages/tabs/ExternalIds.php',
						'clean_name' => 'External IDs'
					),
					array(
						'name' => 'EnrolAwdProg',
						'url' => '/pages/tabs/EnrolAwdProg.php',
						'clean_name' => 'Enrol Award Programme'
					),
					array(
						'name' => 'TheResDeg',
						'url' => '/pages/tabs/TheResDeg.php',
						'clean_name' => 'Thesis Research Degree'
					),
					array(
						'name' => 'Qualifications',
						'url' => '/pages/tabs/Qualifications.php',
						'clean_name' => 'Qualifications'
					),
					array(
						'name' => 'YearsOfAwdProg',
						'url' => '/pages/tabs/YearsOfAwdProg.php',
						'clean_name' => 'Years of Award Programme'
					)
				);
				
				foreach ($tabsArray AS $tab) {
					$active = "";
			
					// Check if the method exists and returns data
					$method = strtolower($tab['name']); // Convert tab name to method name, e.g., "Suspensions" -> "suspensions"
					$dataExists = false;
			
					if (method_exists($person, $method)) {
						$dataExists = !empty($person->$method()->all()); // Call the method dynamically and check if data is returned
					}
			
					// Only show the tab if there's data
					if ($dataExists) {
						if ($tab == "Suspensions") {
							$active = "active";
						}
						$output  = "<li class=\"nav-item\" role=\"presentation\">";
						$output .= "<button class=\"nav-link " . $active . "\" id=\"" . $tab['name'] . "-tab\" data-bs-toggle=\"tab\" data-bs-target=\"#" . $tab['name'] . "-tab-pane\" type=\"button\" role=\"tab\" aria-controls=\"" . $tab . "-tab-pane\" aria-selected=\"true\">" . $tab['clean_name'] . "</button>";
						$output .= "</li>";
						echo $output;
					}
				}
				?>
			</ul>
			
			<div class="tab-content" id="myTabContent">
				<?php
				foreach ($tabsArray AS $tab) {
					// Check again for data before displaying content
					$method = strtolower($tab['name']); // Convert tab name to method name, e.g., "Suspensions" -> "suspensions"
					$dataExists = false;
			
					if (method_exists($person, $method)) {
						$dataExists = !empty($person->$method()); // Call the method dynamically and check if data is returned
					}
			
					// Only show the tab content if there's data
					if ($dataExists) {
						$active = "";
						if ($tab == "Suspensions") {
							$active = "show active";
						}
			
						$url = $tab['url'] . "?cudid=" . $person->cudid;
						
						$output  = "<div class=\"tab-pane fade " . $active . "\" id=\"" . $tab['name'] . "-tab-pane\" role=\"tabpanel\" aria-labelledby=\"" . $tab['name'] . "-tab\" tabindex=\"0\">";
						$output .= "<div w3-include-html=\"" . $url . "\"></div>";
						$output .= "</div>";
			
						echo $output;
					}
				}
				?>
			</div>
		</div>
	</div>
</div>


<script>
function includeHTML() {
  var z, i, elmnt, file, xhttp;
  /* Loop through a collection of all HTML elements: */
  z = document.getElementsByTagName("*");
  for (i = 0; i < z.length; i++) {
	elmnt = z[i];
	/*search for elements with a certain atrribute:*/
	file = elmnt.getAttribute("w3-include-html");
	if (file) {
	  /* Make an HTTP request using the attribute value as the file name: */
	  xhttp = new XMLHttpRequest();
	  xhttp.onreadystatechange = function() {
		if (this.readyState == 4) {
		  if (this.status == 200) {elmnt.innerHTML = this.responseText;}
		  if (this.status == 404) {elmnt.innerHTML = "Page not found.";}
		  /* Remove the attribute, and call this function once more: */
		  elmnt.removeAttribute("w3-include-html");
		  includeHTML();
		}
	  } 
	  xhttp.open("GET", file, true);
	  xhttp.send();
	  /* Exit the function: */
	  return;
	}
  }
}
includeHTML();

document.addEventListener("DOMContentLoaded", function () {
	// Find the first visible nav-link inside #myTab
	const firstTabButton = document.querySelector('#myTab .nav-link');
	if (firstTabButton) {
		const tab = new bootstrap.Tab(firstTabButton);
		tab.show();
	}
});
</script>
