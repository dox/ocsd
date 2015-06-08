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
	<p>DOB: <span id="dt_birth" class="inlineEditble" data-type="combodate" data-value="<?php echo $user->dt_birth; ?>" data-format="YYYY-MM-DD" data-viewformat="YYYY/MM/DD" data-pk="<?php echo $user->id();?>" data-url="/ocsd/actions/u_students.php" data-original-title="Date of Birth"></span> (Age: <?php echo age($user->dt_birth); ?>)</p>
	<p>Gender: <span id="gender" data-type="select" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_students.php" data-value="<?php echo $user->gender; ?>" data-original-title="Gender"><?php echo $user->gender; ?></span></p>
			<p>Country of Birth: <?php if (isset($birthCountry->cyid)) { echo $birthCountry->fullDisplayName(true); }?></p>
	<p>Country of Residence: <?php if (isset($residenceCountry->cyid)) { echo $residenceCountry->fullDisplayName(true); }?></p>
	<p>County of Citizenship: <?php if (isset($citizenshipCountry->cyid)) { echo $citizenshipCountry->fullDisplayName(true); }?></p>
	<p>Opt Out: <?php echo $user->optout; ?></p>
	<p>Family: <?php echo $user->family; ?></p>
	<hr />
	
	<p>Occup BG: <?php echo $user->occup_bg; ?></p>
	
	<hr />
	
	<p>Ethnic Origin: <?php if (isset($ethnicCountry->cyid)) { echo $ethnicCountry->fullDisplayName(true); }?></p>
	<p>CS Key: <?php echo $user->cskey; ?></p>
	<p>Religion: <?php echo $user->relkey; ?></p>
	<p>RC Key: <?php echo $user->rckey; ?></p>
	<p>SSN Reference: <?php echo $user->SSNref; ?></p>
	<p>Fee Status: <?php echo $user->fee_status; ?></p>
	
	<hr />
	
	<p>Date Started: <span id="dt_start" class="inlineEditble" data-type="combodate" data-value="<?php echo $user->dt_start; ?>" data-format="YYYY-MM-DD" data-viewformat="YYYY/MM/DD" data-pk="<?php echo $user->id();?>" data-url="/ocsd/actions/u_students.php" data-original-title="Date Started"></span></p>
	<p>Date End: <span id="dt_end" class="inlineEditble" data-type="combodate" data-value="<?php echo $user->dt_end; ?>" data-format="YYYY-MM-DD" data-viewformat="YYYY/MM/DD" data-pk="<?php echo $user->id();?>" data-url="/ocsd/actions/u_students.php" data-original-title="Date End"></span></p>
	<p>Date Matriculated: <span id="dt_matric" class="inlineEditble" data-type="combodate" data-value="<?php echo $user->dt_matric; ?>" data-format="YYYY-MM-DD" data-viewformat="YYYY/MM/DD" data-pk="<?php echo $user->id();?>" data-url="/ocsd/actions/u_students.php" data-original-title="Date Matriculated"></span></p>
	<p>Year Applied: <?php echo $user->yr_app; ?></p>
	<p>Year Entry: <?php echo $user->yr_entry; ?></p>
	<p>Date Created: <?php echo convertToDateString($user->dt_created); ?></p>
	
	<hr />
	<h3>Notes</h3>
	<span id="notes" class="inlineEditble" data-type="textarea" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_students.php" data-original-title="Notes"><?php echo $user->notes; ?></span>

	<div class="clearfix"></div>
	
	<p><button class="btn btn-default btn-xs pull-right disabled" type="button">Last Modified By: <?php echo $user->who_mod . " (" . convertToDateString($user->dt_lastmod) . ")"; ?></button></p>
</div>