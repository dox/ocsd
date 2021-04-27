<?php
$person = new Person($_GET['cudid']);

if (isset($person->cudid)) {
	$logInsert = (new Logs)->insert("view","success",$person->cudid,"{cudid:" . $person->cudid . "}" . $person->FullName . " record viewed");
} else {
	$logInsert = (new Logs)->insert("view","error",null,"<code>" . $_GET['cudid'] . "</code> record viewed but doesn't exist");
}

if (obscure == true) {
	$obscureClass = $class . " obscure";
	$obscureImgClass = $class . " obscureImg";
}

$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"images/icons.svg#bell\"/></svg> Test Button", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteMealModal\"");
$icons[] = array("class" => "btn-warning", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"images/icons.svg#email\"/></svg> Email", "value" => "onclick=\"window.open('mailto:" . $person->oxford_email . "')\"");

echo displayTitle($person->FullName, "CUD Filter: " . $_GET['cudid'], $icons);
?>

<div class="row">
		<div class="col-lg-4">
			<div class="card">
				<img src="<?php echo $person->photo();?>" class="card-img-top <?php echo $obscureImgClass; ?>" alt="Card top image">
				<div class="card-body text-center">
					<h3 class="mb-3"><span class="<?php echo $obscureClass; ?>"><?php echo $person->FullName; ?></span> <button class="btn btn-outline-primary btn-sm"><?php echo $person->university_card_type;?></button></h3>
					<?php echo "SSO: <span class=\"" . $obscureClass . "\">" . $person->sso_username . "</span>"; ?>
					</div>
				</div>
				<?php include("nodes/persons_unique_tabs/ldap.php");?>
				<div class="card mb-3">
					<div class="card-body">
						<div class="media">
							<div class="media-body">
								<p class="text-muted mb-0">Bodcard: <?php echo $person->barcode7 . " (" . $person->bodcardDaysLeft() . " days left)"; ?></p>
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
				<?php include("nodes/persons_unique_tabs/Addresses.php");?>
				<?php include("nodes/persons_unique_tabs/EmergencyContacts.php");?>
				<?php include("nodes/persons_unique_tabs/Student.php");?>
				<?php include("nodes/persons_unique_tabs/CoOwningDepartments.php");?>
				<?php include("nodes/persons_unique_tabs/ExternalIds.php");?>
			</div>
			<div class="col-lg-8">
				<?php include("nodes/persons_unique_tabs/Suspensions.php");?>
				<?php include("nodes/persons_unique_tabs/Supervisors.php");?>
				<?php include("nodes/persons_unique_tabs/Applications.php");?>
				<?php include("nodes/persons_unique_tabs/AppliedCollDept.php");?>
				<?php include("nodes/persons_unique_tabs/CollegeFees.php");?>
				<?php include("nodes/persons_unique_tabs/EnrolAwdProg.php");?>
				<?php include("nodes/persons_unique_tabs/Enrolments.php");?>
				<?php include("nodes/persons_unique_tabs/GROUP_MEMBERSHIPS.php");?>
				<?php include("nodes/persons_unique_tabs/Member_Addresses.php");?>
				<?php include("nodes/persons_unique_tabs/Member_Employments.php");?>
				<?php include("nodes/persons_unique_tabs/Member_Intermissions.php");?>
				<?php include("nodes/persons_unique_tabs/Member_Other.php");?>
				<?php include("nodes/persons_unique_tabs/Member_Person.php");?>
				<?php include("nodes/persons_unique_tabs/Member_Resources.php");?>
				<?php include("nodes/persons_unique_tabs/Member_Rights_to_work.php");?>
				<?php include("nodes/persons_unique_tabs/Member_Staff.php");?>
				<?php include("nodes/persons_unique_tabs/Member_Subjects.php");?>
				<?php include("nodes/persons_unique_tabs/Qualifications.php");?>
				<?php include("nodes/persons_unique_tabs/Reassessments.php");?>
				<?php include("nodes/persons_unique_tabs/TheResDeg.php");?>
				<?php include("nodes/persons_unique_tabs/Tutelages.php");?>
				<?php include("nodes/persons_unique_tabs/YearsOfAwdProg.php");?>

			</div>
		</div>
		<?php include("nodes/persons_unique_tabs/logs.php");?>
	</div>
</div>
