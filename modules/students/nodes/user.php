<?php
$user = Students::find_by_uid($_GET['studentid']);
$residences = ResidenceAddresses::find_all_by_student($_GET['studentid']);
$addresses = Addresses::find_all_by_student($_GET['studentid']);
$birthCountry = Countries::find_by_uid($user->birth_cykey);
$residenceCountry = Countries::find_by_uid($user->resid_cykey);
$citizenshipCountry = Countries::find_by_uid($user->citiz_cykey);
$ethnicCountry = Countries::find_by_uid($user->ethkey);
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
		
		<p><a class="btn" href="#">Edit Details &raquo;</a></p>
		<div class="clearfix"></div>
	</div>
	<div class="span9">
		<ul class="nav nav-tabs" id="myTab">
			<li class="active"><a href="#information" data-toggle="tab">Information</a></li>
			<li><a href="#addresses" data-toggle="tab">Addresses</a></li>
			<li><a href="#education" data-toggle="tab">Education</a></li>
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
				<p>Full Name: <?php echo $user->title . " " . $user->forenames . " " . $user->initials . " " . $user->surname; ?></p>
				<p>Preferred First Name: <?php echo $user->prefname; ?></p>
				<p>Previous Family Name: <?php echo $user->prev_surname; ?></p>
				<p><?php echo $user->suffix; ?></p>
				<p>Marital Status: <?php echo $user->marital_status; ?></p>
				<p>DOB: <?php echo $user->dt_birth; ?></p>
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
			<div class="tab-pane" id="education">Coming soon...</div>
		</div>
		
		
	</div>
</div>

<script>
$(function () {
	$('#myTab a:last').tab('show');
})
</script>