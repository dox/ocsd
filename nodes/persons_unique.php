<?php
$person = new Person($_GET['cudid']);

if (isset($person->cudid)) {
	$logInsert = (new Logs)->insert("view","success",$person->cudid,$person->FullName . " record viewed");
} else {
	$logInsert = (new Logs)->insert("view","error",null,"<code>" . $_GET['cudid'] . "</code> record viewed but doesn't exist");
}
?>
<div class="content">
	<div class="container-xl">
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col-auto">
					<!-- Page pre-title -->
					<div class="page-pretitle">
						CUD Filter: <?php echo $_GET['cudid'];?>
					</div>
					<h2 class="page-title">
						<?php echo $person->FullName; ?>
					</h2>
				</div>
				<!-- Page title actions -->
				<div class="col-auto ml-auto d-print-none">
					<span class="d-none d-sm-inline">
						<a href="mailto:<?php echo $person->oxford_email;?>" class="btn btn-white">
							Email
						</a>
					</span>
					<a href="#" class="btn btn-primary ml-3 d-none d-sm-inline-block" data-toggle="modal" data-target="#modal-report">
						<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
						Quick Actions
					</a>
					<a href="#" class="btn btn-primary ml-3 d-sm-none btn-icon" data-toggle="modal" data-target="#modal-report" aria-label="Create new report">
						<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
					</a>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-4">
				<div class="card">
					<img src="<?php echo $person->photo();?>" class="card-img-top" alt="Card top image">
					<div class="card-body text-center">
						<h3 class="mb-3"><?php echo $person->FullName; ?></h3>
						<button class="btn btn-outline-primary btn-sm">
							<span class="fa fa-twitter"></span><?php echo $person->university_card_type;?></button>
						</div>
					</div>
					<div class="card">
						<div class="card-body">
							<div class="media">
								<span class="avatar avatar-xxl mr-5" style="background-image: url(../photos/UAS_UniversityCard-<?php echo $person->university_card_sysis;?>.jpg)"></span>
								<div class="media-body">
									<h4 class="m-0"><?php echo $person->sso_username; ?></h4>
									<p class="text-muted mb-0">Bodcard: <?php echo $person->barcode7; ?></p>
									<p class="text-muted mb-0">Card Start Date: <?php echo date('Y-m-d', strtotime($person->University_Card_Start_Dt)); ?></p>
									<p class="text-muted mb-0">Card End Date: <?php echo date('Y-m-d', strtotime($person->University_Card_End_Dt)); ?></p>
									<p class="text-muted mb-0">Student Code: <?php echo $person->sits_student_code; ?></p>
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
					<?php include("nodes/persons_unique_tabs/ldap.php");?>
					<?php include("nodes/persons_unique_tabs/Addresses.php");?>
					<?php include("nodes/persons_unique_tabs/EmergencyContacts.php");?>
					<?php include("nodes/persons_unique_tabs/ExternalIds.php");?>
				</div>
				<div class="col-lg-8">
					<?php include("nodes/persons_unique_tabs/Suspensions.php");?>
					<?php include("nodes/persons_unique_tabs/Supervisors.php");?>
					<?php include("nodes/persons_unique_tabs/CoOwningDepartments.php");?>
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
					<?php include("nodes/persons_unique_tabs/Student.php");?>
					<?php include("nodes/persons_unique_tabs/TheResDeg.php");?>
					<?php include("nodes/persons_unique_tabs/Tutelages.php");?>
					<?php include("nodes/persons_unique_tabs/YearsOfAwdProg.php");?>

				</div>
			</div>
			<?php include("nodes/persons_unique_tabs/logs.php");?>
		</div>
	</div>
</div>
