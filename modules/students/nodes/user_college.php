<div class="tab-pane" id="college">
	<h3>PostGraduate/Undergraduate</h3>
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
	
	<h3>Tutors/Examinations</h3>
	<p>Coming soon...</p>
</div>