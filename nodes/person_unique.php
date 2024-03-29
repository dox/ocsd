<?php
$clean_cudid = filter_var($_GET['cudid'], FILTER_SANITIZE_STRING);

$personObject = new Person($clean_cudid);

$ldapPerson = new LDAPPerson($personObject->sso_username, $personObject->oxford_email);
?>

<div class="row">
	<div class="col-md-4">
		<div class="profile-img">
			<img class="img-thumbnail w-100 mb-3" src="<?php echo $personObject->photoSrc(); ?>" alt="">
		</div>
		
		<h4 class="mb-3">Core Credentials</h4>
		SSO: <strong><?php echo $personObject->sso_username; ?></strong><br>
		Bodcard: <strong><?php echo $personObject->barcode7; ?></strong> <i><?php echo "Expires " . date('Y-m-d', strtotime($personObject->University_Card_End_Dt)); ?></i><br>
		<?php
		if (!empty($personObject->dob)) {
			echo "DOB: <strong>" . date('Y-m-d', strtotime($personObject->dob)) . "</strong> <i> Age " . age($personObject->dob) . "</i><br>";
		}
		
		if (!empty($personObject->gnd)) {
			if ($personObject->gnd == "M") {
				$colour = "gender-male";
			} elseif ($personObject->gnd == "F") {
				$colour = "gender-female";
			} else {
				$colour = "gender-other";
			}
			
			$pronouns = "";
			if (isset($personObject->consolidated_pronouns)) {
				$pronouns = " <i>(" . $personObject->consolidated_pronouns . ")</i>";
			}
			echo "Gender: <span class=\"badge " . $colour . "\">" . $personObject->gnd . "</span>" . $pronouns . "<br>";
		}
		?>
		<style>
		.gender-male {
			background-color: #0d6efd;
		}
		.gender-female {
			background-color: #d63384;
		}
		.gender-other {
			background-color: #adb5bd;
		}
		</style>
		Email: <strong><?php echo makeEmail($personObject->oxford_email); ?></strong><br>
		Email2: <strong><?php echo makeEmail($personObject->alt_email); ?></strong><br>
		MiFare: <strong><?php echo $personObject->MiFareID; ?></strong><br>
		Paxon: <strong><?php echo $personObject->PaxonID; ?></strong><br>
		SITS: <strong><?php echo $personObject->sits_student_code; ?></strong><br>
		SysIS: <strong><?php echo $personObject->university_card_sysis; ?></strong><br>
		<?php echo $ldapPerson->actionsButton(); ?>
	</div>
	<div class="col-md-8">
		<div class="profile-head">
			<h2><span class="badge bg-secondary float-end"><?php echo $personObject->university_card_type; ?></span></h2>
			<h2><?php echo $personObject->FullName; ?></h2>
			<?php
			if (isset($personObject->rout_name) || isset($personObject->div_desc)) {
				echo "<h4>" . $personObject->rout_name . ", " . $personObject->div_desc . "</h4>";
			}
			?>
			  
			<ul class="nav nav-tabs" id="myTab" role="tablist">
			<?php
			$tabsArray = array("Suspensions", "Supervisors", "Applications", "Enrolments", "Addresses", "CollegeFees", "CoOwningDepartments", "ExternalIds", "EnrolAwdProg", "TheResDeg", "Qualifications", "YearsOfAwdProg");
			
			foreach ($tabsArray AS $tab) {
				$active = "";
				if ($tab == "Suspensions") {
					$active = "active";
				}
				$output  = "<li class=\"nav-item\" role=\"presentation\">";
				$output .= "<button class=\"nav-link " . $active . "\" id=\"" . $tab . "-tab\" data-bs-toggle=\"tab\" data-bs-target=\"#" . $tab . "-tab-pane\" type=\"button\" role=\"tab\" aria-controls=\"" . $tab . "-tab-pane\" aria-selected=\"true\">" . $tab . "</button>";
				$output .= "</li>";
					
				echo $output;
			}
			?>
			</ul>
			
			<div class="tab-content" id="myTabContent">
				<?php
				foreach ($tabsArray AS $tab) {
					$active = "";
					if ($tab == "Suspensions") {
						$active = "show active";
					}
					
					$url = "../nodes/persons_unique_tabs/" . $tab . ".php" . "?cudid=" . $personObject->cudid;
					
					$output  = "<div class=\"tab-pane fade " . $active . "\" id=\"" . $tab . "-tab-pane\" role=\"tabpanel\" aria-labelledby=\"" . $tab . "-tab\" tabindex=\"0\">";
					$output .= "<div w3-include-html=\"" . $url . "\"></div>";
					$output .= "</div>";
					
					echo $output;
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
</script>
