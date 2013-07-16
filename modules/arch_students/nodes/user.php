<?php
$user = ArchStudents::find_by_uid($_GET['arstudentid']);
$addresses = ArchAddresses::find_all_by_student($user->id());
$degree = ArchGrads::find_academic_record_by_studentkey($user->id());
?>
<div class="row">
	<div class="span12">
		<div class="page-header">
			<h1><?php echo $user->fullDisplayName(); ?> <small> Cohort: <?php echo $user->yr_cohort; ?></small></h1>
		</div>
	</div>
</div>
<div class="row">
	<div class="span3">
		<?php echo $user->imageURL(true); ?>
		<div class="clearfix"></div>
		<p><i class="icon-barcode"></i> <?php echo $user->bodcard(); ?></p>
		<p><i class="icon-qrcode"></i> <?php echo $user->oss_pn; ?></p>
		<p><i class="icon-user"></i> <?php echo $user->oucs_id; ?></p>
		<?php
		if ($user->mobile) {
			echo "<p><i class=\"icon-comment\"></i> " . $user->mobile . "</p>";
		}
		if ($user->email1) {
			echo "<p><i class=\"icon-envelope\"></i> <a href=\"mailto:" . $user->email1 . "\">" . $user->email1 . "</a></p>";
		}
		if ($user->email2) {
			echo "<p><i class=\"icon-envelope\"></i> <a href=\"mailto:" . $user->email2 . "\">" . $user->email2 . "</a></p>";
		}
		?>
		
		<p><i class="icon-globe"></i> <?php echo $user->nationality; ?></p>
		
		<p><a class="btn" href="index.php?n=404.php">Edit Details &raquo;</a></p>
		<div class="clearfix"></div>
	</div>
	<div class="span9">
		<div class="alert alert-info">
			<strong>Archive</strong> This student is listed in the OCSD archives.
		</div>
		<ul class="nav nav-tabs" id="myTab">
			<li class="active"><a href="#information" data-toggle="tab">Information</a></li>
			<li><a href="#addresses" data-toggle="tab">Addresses</a></li>
			<li><a href="#education" data-toggle="tab">Education</a></li>
			<li><a href="#college" data-toggle="tab">College</a></li>
			<li><a href="#reports" data-toggle="tab">Reports</a></li>
		</ul>
		
		<div class="tab-content">
			<div class="tab-pane active" id="information">
					<div class="span2 well lead">
						English 1st: <?php echo $user->eng_lang; ?>
					</div>
					<div class="span3 well lead">
						College Status: <?php echo $user->st_type; ?>
					</div>
					<div class="span2 well lead">
						Course Year: <?php echo $user->course_yr; ?>
					</div>
				<p>Disability: <?php echo $user->disability; ?></p>
				<div class="clearfix"></div>
				<hr />
				<p>Full Name: <?php echo $user->fullDisplayName(); ?></p>
				<p>Preferred First Name: <?php echo $user->prefname; ?></p>
				<p>Previous Family Name: <?php echo $user->prev_surname; ?></p>
				<p><?php echo $user->suffix; ?></p>
				<p>Marital Status: <?php echo $user->marital_status; ?></p>
				<p>DOB: <?php echo convertToDateString($user->dt_birth) . " (Age: " . age(convertToDateString($user->dt_birth)) . ")"; ?></p>
				<p>Gender: <?php echo $user->gender; ?></p>
				<p>Country of Birth: <?php if (isset($birthCountry->cyid)) { echo $birthCountry->fullDisplayName(true); }?></p>
				<p>Country of Residence: <?php if (isset($residenceCountry->cyid)) { echo $residenceCountry->fullDisplayName(true); }?></p>
				<p>County of Citizenship: <?php if (isset($citizenshipCountry->cyid)) { echo $citizenshipCountry->fullDisplayName(true); }?></p>
				<p>Opt Out: <?php echo $user->optout; ?></p>
				<p>Family: <?php echo $user->family; ?></p>
				<hr />
		
		<p>Occup BG: <?php echo $user->occup_bg; ?></p>
		
		<hr />
		<p>Ethnic Origin: <?php if (isset($ethnicCountry->cyid)) { echo $ethnicCountry->fullDisplayName(true); }?></p>
		<p>RS Key: <?php echo $user->rskey; ?></p>
		<p>CS Key: <?php echo $user->cskey; ?></p>
		<p>Religion: <?php echo $user->relkey; ?></p>
		<p>RC Key: <?php echo $user->rckey; ?></p>
		<p>SSN Reference: <?php echo $user->SSNref; ?></p>
		<p>Fee Status: <?php echo $user->fee_status; ?></p>
		<hr />
		
		
		
		<p>Date Started: <?php echo convertToDateString($user->dt_start); ?></p>
		<p>Date End: <?php echo convertToDateString($user->dt_end); ?></p>
		<p>Date Matriculated: <?php echo convertToDateString($user->dt_matric); ?></p>
		<p>Year Applied: <?php echo $user->yr_app; ?></p>
		<p>Year Entry: <?php echo $user->yr_entry; ?></p>
		<p>Date Created: <?php echo convertToDateString($user->dt_created); ?></p>
		
		<?php
		if ($user->notes) {
			echo "<hr />";
			echo "<h3>Notes: </h3>";
			echo "<p>" . $user->notes . "</p>";
		}
		printArray($user);
		?>
		<div class="clearfix"></div>
		<p><button class="btn btn-mini pull-right disabled" type="button">Last Modified By: <?php echo $user->who_mod . " (" . convertToDateString($user->dt_lastmod) . ")"; ?></button></p>
			</div>
			<div class="tab-pane" id="addresses">
				<?php
				echo "<h3>Home Residence</h3>";
				foreach ($addresses AS $address) {
					echo $address->displayAddress();
				}
				
				echo "<h3>College Residence</h3>";
				foreach ($residences AS $resAddress) {
					echo $resAddress->displayAddress();
				}
				//echo $residence->displayAddress();
				 ?>
			</div>
			<div class="tab-pane" id="education">
				<p>Coming soon</p>
			</div>
			<div class="tab-pane" id="college">
				<?php
				printArray($degree);
				?>
				<p>Degree: <?php echo $degree->abbrv; ?></p>
				<p>Course: <?php echo $degree->course_key; ?></p>
				<p>Res. Status: </p>
				<p>Subject: <?php echo $subject->name; ?></p>
				<p>Year: ?? of ??</p>
				<p>Options: <?php echo $degree->options; ?></p>
				<p>Class: <?php echo $degree->qualname; ?></p>
				<hr />
				<p>Matric: </p>
				<p>Cohort: </p>
				<p>Res. Category: </p>
				<p>Start: <?php echo convertToDateString($user->dt_start); ?></p>
				<p>Finish: </p>
				<p>Conferment: <?php echo convertToDateString($user->dt_confer); ?></p>
				<p>M.A.: </p>
				<p>Fee Status: </p>
				<p>SSN Ref: </p>
				<hr />
				<p>1st College Choice: <?php echo $degree->collkey; ?></p>
				<p>Def. Entry: <?php echo $degree->def_entry; ?></p>
				<p>Ox. App. No: <?php echo $degree->oxford_appno; ?></p>
				<p>Dropped Cond. Offer: <?php echo $degree->drop_cond_offer; ?></p>
				<p>App. Year: </p>
				<p>UCAS App. No.: <?php echo $degree->ucas_appno; ?></p>
				<p>IC Pool: <?php echo $degree->ic_pool; ?></p>
				<p>Entry Year: </p>
				<p>App. Type: <?php echo $degree->app_type; ?></p>
			</div>
			<div class="tab-pane" id="reports">
				
				<div class="btn-group">
					<?php
					echo "<a href=\"report_pdf.php?n=arch_transcript.php&arstudentid=" . $user->id() . "\" class=\"btn\">Generate Transcript</a>";
					?>
					<button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
					<ul class="dropdown-menu">
						<li class="nav-header">With Letterhead</li>
						<li><a href="report_pdf.php?n=arch_transcript.php&arstudentid=<?php echo $user->id(); ?>">With exam paper details</a></li>
						<li><a href="report_pdf.php?n=arch_transcript.php&exams=false&arstudentid=<?php echo $user->id(); ?>">Without exam paper details</a></li>
						
						<li class="nav-header">Without Letterhead</li>
						<li><a href="report_pdf.php?n=arch_transcript.php&arstudentid=<?php echo $user->id(); ?>&header=false">With exam paper details</a></li>
						<li><a href="report_pdf.php?n=arch_transcript.php&exams=false&arstudentid=<?php echo $user->id(); ?>&header=false">Without exam paper details</a></li>
					</ul>
					
					
				</div>
				<p>test test</p>
				<p>test test</p>
				<p>test test</p>
				<p>test test</p>
				<p>test test</p>
				<p>test test</p>
			</div>
		</div>
		
		
	</div>
</div>

<script>
$(function () {
	$('#myTab a:last').tab('show');
})
</script>