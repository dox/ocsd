<?php
$person = new Person($_GET['cudid']);



if (obscure == true) {
	$obscureClass = $class . " obscure";
	$obscureImgClass = $class . " obscureImg";
}

$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"images/icons.svg#bell\"/></svg> Test Button", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteMealModal\"");
$icons[] = array("class" => "btn-warning", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"images/icons.svg#email\"/></svg> Email", "value" => "onclick=\"window.open('mailto:" . $person->oxford_email . "')\"");

echo displayTitle($person->FullName, "CUD Filter: " . $_GET['cudid'], $icons);
?>

<div class="row mb-3">
	<div class="col-4">
		<div class="row mb-3">
			<div class="col">
				<img src="<?php echo $person->photo();?>" class="img-fluid" width="100%">
			</div>
		</div>
	</div>
	<div class="col-8">
		<div class="row mb-3">
			<div class="col-3">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title"><?php echo $person->sso_username; ?></h5>
						<h6 class="card-subtitle mb-2 text-muted">Oxford SSO</h6>
					</div>
				</div>
			</div>
			<div class="col-3">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title"><?php echo $person->university_card_type;?></h5>
						<h6 class="card-subtitle mb-2 text-muted">Bodcard Type</h6>
					</div>
				</div>
			</div>
			<div class="col-3">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title"><?php echo $person->barcode7; ?></h5>
						<h6 class="card-subtitle mb-2 text-muted">Bodcard Number <?php echo "(" . $person->bodcardDaysLeft() . " days left)"; ?></h6>
					</div>
				</div>
			</div>
			<div class="col-3">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">coming soon</h5>
						<h6 class="card-subtitle mb-2 text-muted">LDAP Username</h6>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-6">
				<?php include("nodes/persons_unique_tabs/Addresses.php");?>
			</div>
			<div class="col-6">
				<?php include("nodes/persons_unique_tabs/ldap.php");?>
				<?php include("nodes/persons_unique_tabs/CoOwningDepartments.php");?>
			</div>
		</div>
	</div>
</div>

	<?php include("nodes/persons_unique_tabs/Suspensions.php");?>
	<?php include("nodes/persons_unique_tabs/Supervisors.php");?>
	<?php include("nodes/persons_unique_tabs/Applications.php");?>
	<?php include("nodes/persons_unique_tabs/Enrolments.php");?>
	<?php include("nodes/persons_unique_tabs/GROUP_MEMBERSHIPS.php");?>
	<?php include("nodes/persons_unique_tabs/Member_Addresses.php");?>
	<?php include("nodes/persons_unique_tabs/Member_Employments.php");?>
	<?php include("nodes/persons_unique_tabs/AppliedCollDept.php");?>
	<?php include("nodes/persons_unique_tabs/CollegeFees.php");?>
	<?php include("nodes/persons_unique_tabs/EnrolAwdProg.php");?>
	<?php include("nodes/persons_unique_tabs/TheResDeg.php");?>
	<?php include("nodes/persons_unique_tabs/Member_Intermissions.php");?>
	<?php include("nodes/persons_unique_tabs/Member_Other.php");?>
	<?php include("nodes/persons_unique_tabs/Member_Person.php");?>

<div class="row mb-3">
	<div class="col">
		<?php include("nodes/persons_unique_tabs/YearsOfAwdProg.php");?>
	</div>
</div>

<hr />
<div class="row">

	
		<div class="col-lg-4">
				
				<div class="card mb-3">
					<div class="card-body">
						<div class="media">
							<div class="media-body">
								<p class="text-muted mb-0">Card Start Date: <?php echo date('Y-m-d', strtotime($person->University_Card_Start_Dt)); ?></p>
								<p class="text-muted mb-0">Card End Date: <?php echo date('Y-m-d', strtotime($person->University_Card_End_Dt)); ?></p>
								<p class="text-muted mb-0">Nationality: <?php echo $person->nationality(); ?></p>
								<ul class="social-links list-inline mb-0 mt-2">
									<li class="list-inline-item">
										<a href="#" title="Phone" data-bs-toggle="tooltip"><svg width="1em" height="1em" class="me-2"><use xlink:href="images/icons.svg#telephone"/></svg></a>
									</li>
									<li class="list-inline-item">
										<a href="mailto:<?php echo $person->oxford_email; ?>" title="Email"><svg width="1em" height="1em" class="me-2"><use xlink:href="images/icons.svg#email"/></svg></a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				
				<?php include("nodes/persons_unique_tabs/EmergencyContacts.php");?>
				<?php include("nodes/persons_unique_tabs/Student.php");?>
				
				<?php include("nodes/persons_unique_tabs/ExternalIds.php");?>
			</div>
			<div class="col-lg-8">
				
				
				
				
				<?php include("nodes/persons_unique_tabs/Member_Resources.php");?>
				<?php include("nodes/persons_unique_tabs/Member_Rights_to_work.php");?>
				<?php include("nodes/persons_unique_tabs/Member_Staff.php");?>
				<?php include("nodes/persons_unique_tabs/Member_Subjects.php");?>
				<?php include("nodes/persons_unique_tabs/Qualifications.php");?>
				<?php include("nodes/persons_unique_tabs/Reassessments.php");?>
				
				<?php include("nodes/persons_unique_tabs/Tutelages.php");?>
				

			</div>
		</div>
		<?php include("nodes/persons_unique_tabs/logs.php");?>
	</div>
</div>
