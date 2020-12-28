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
?>

<div class="content">
	<!-- Page title -->
	<div class="page-header">
		<div class="row align-items-center">
			<div class="col">
				<div class="page-pretitle">CUD Filter: <span class="<?php echo $obscureClass; ?>"><?php echo $_GET['cudid'];?></span></div>
				<h2 class="page-title <?php echo $obscureClass; ?>"><?php echo $person->FullName; ?></h2>
			</div>
			<!-- Page title actions -->
			<div class="col-auto ml-auto d-print-none">
				<span class="d-none d-sm-inline">
					<a href="mailto:<?php echo $person->oxford_email;?>" class="btn btn-white">Email</a>
				</span>
			</div>
		</div>
	</div>

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
				<div class="card">
					<div class="card-body">
						<div class="media">
							<div class="media-body">
								<p class="text-muted mb-0">Bodcard: <?php echo $person->barcode7 . " (" . $person->bodcardDaysLeft() . " days left)"; ?></p>
								<p class="text-muted mb-0">Card Start Date: <?php echo date('Y-m-d', strtotime($person->University_Card_Start_Dt)); ?></p>
								<p class="text-muted mb-0">Card End Date: <?php echo date('Y-m-d', strtotime($person->University_Card_End_Dt)); ?></p>
								<p class="text-muted mb-0">Nationality: <?php echo $person->nationality(); ?></p>
								<ul class="social-links list-inline mb-0 mt-2">
									<li class="list-inline-item">
										<a href="javascript:void(0)" title="Phone" data-toggle="tooltip"><i class="fe fe-phone"></i></a>
									</li>
									<li class="list-inline-item">
										<a href="javascript:void(0)" title="Email" data-toggle="tooltip"><i class="fe fe-mail"></i></a>
									</li>
									<li class="list-inline-item">
										<a href="javascript:void(0)" title="1234567890" data-toggle="tooltip"><i class="fe fe-phone"></i></a>
									</li>
									<li class="list-inline-item">
										<a href="javascript:void(0)" title="@skypename" data-toggle="tooltip"><i class="fe fe-mail"></i></a>
									</li>
									<li class="list-inline-item">
										<a href="persons_unique_tabs/signpass.php" title="Signpass" data-toggle="tooltip"><i class="fe fe-credit-card"></i></a>
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
