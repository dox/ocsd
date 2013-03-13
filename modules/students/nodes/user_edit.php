<?php
$user = Students::find_by_uid($_GET['studentid']);
$residences = ResidenceAddresses::find_all_by_student($_GET['studentid']);
$addresses = Addresses::find_all_by_student($_GET['studentid']);
$birthCountry = Countries::find_by_uid($user->birth_cykey);
$residenceCountry = Countries::find_by_uid($user->resid_cykey);
$citizenshipCountry = Countries::find_by_uid($user->citiz_cykey);
$ethnicCountry = Countries::find_by_uid($user->ethkey);
$degree = Grads::find_by_studentkey($user->studentid);

?>
<div class="row">
	<div class="span12">
		<div class="page-header">
			<h1>Edit User <small><?php echo $user->fullDisplayName(); ?></small></h1>
		</div>
	</div>
</div>
<div class="row">
	<div class="span3">
		<form class="form-horizontal">
			<?php echo $user->imageURL(true); ?>
			<div class="clearfix"></div>
			<div class="control-group">
				<label class="control-label" for="inputBodcard">Bod Card</label>
				<div class="controls">
					<input type="text" id="inputBodcard" placeholder="Bodcard" value="<?php echo $user->bodcard(false); ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="inputOUCSID">OUCS ID</label>
				<div class="controls">
					<input type="text" id="inputOUCSID" placeholder="OUCS ID" value="<?php echo $user->oucs_id; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="inputMobile">Mobile Number</label>
				<div class="controls">
					<input type="text" id="inputMobile" placeholder="Mobile Number" value="<?php echo $user->mobile; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="inputEmail1">Oxford E-Mail Address</label>
				<div class="controls">
					<input type="text" id="inputEmail1" placeholder="Oxford E-Mail Address" value="<?php echo $user->email1; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="inputEmail2">Personal E-Mail Address</label>
				<div class="controls">
					<input type="text" id="inputEmail2" placeholder="Personal E-Mail Address" value="<?php echo $user->email2; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="inputNationality">Nationality</label>
				<div class="controls">
					<input type="text" id="inputNationality" placeholder="Nationality" value="<?php echo $user->nationality; ?>">
				</div>
			</div>
			
			<a class="btn btn-primary" href="index.php?n=404.php">Update Details</a>
		</form>
		<div class="clearfix"></div>
	</div>
	<div class="span9">
		<ul class="nav nav-tabs" id="myTab">
			<li class="active"><a href="#information" data-toggle="tab">Information</a></li>
			<li><a href="#addresses" data-toggle="tab">Addresses</a></li>
			<li><a href="#education" data-toggle="tab">Education</a></li>
			<li><a href="#college" data-toggle="tab">College</a></li>
			<li><a href="#reports" data-toggle="tab">Reports</a></li>
		</ul>
		
		<div class="tab-content">
			<div class="tab-pane active" id="information">
				<form class="form-horizontal">
					<div class="control-group">
						<label class="control-label" for="inputEng_lang">English 1st</label>
						<div class="controls">
							<input type="text" id="inputEng_lang" placeholder="English 1st" value="<?php echo $user->eng_lang; ?>">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputSt_type">College Status</label>
						<div class="controls">
							<input type="text" id="inputSt_type" placeholder="College Status" value="<?php echo $user->st_type; ?>">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputCourse_yr">Course Year</label>
						<div class="controls">
							<input type="text" id="inputCourse_yr" placeholder="Course Year" value="<?php echo $user->course_yr; ?>">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputDisability">Disability</label>
						<div class="controls">
							<input type="text" id="inputDisability" placeholder="Disability" value="<?php echo $user->disability; ?>">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputEng_lang">English 1st</label>
						<div class="controls">
							<input type="text" id="inputEng_lang" placeholder="English 1st" value="<?php echo $user->eng_lang; ?>">
						</div>
					</div>
					
					<hr />
					
					<div class="control-group">
						<label class="control-label" for="inputTitle">Title</label>
						<div class="controls">
							<input type="text" id="inputTitle" placeholder="Title" value="<?php echo $user->title; ?>">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputForenames">Forenames</label>
						<div class="controls">
							<input type="text" id="inputForenames" placeholder="Forenames" value="<?php echo $user->forenames; ?>">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputPrefname">Preferred Name(s)</label>
						<div class="controls">
							<input type="text" id="inputPrefname" placeholder="Preferred Name(s)" value="<?php echo $user->prefname; ?>">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputPrev_surname">Previous Surname(s)</label>
						<div class="controls">
							<input type="text" id="inputPrev_surname" placeholder="Previous Surname(s)" value="<?php echo $user->prev_surname; ?>">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputSuffix">Suffix</label>
						<div class="controls">
							<input type="text" id="inputSuffix" placeholder="Suffix" value="<?php echo $user->suffix; ?>">
						</div>
					</div>
					
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
				<p>
				<div class="btn-group">
					<?php
					echo "<a href=\"report_pdf.php?n=transcript.php&studentid=" . $user->id() . "\" class=\"btn\">Generate Transcript</a>";
					?>
					<button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
					<ul class="dropdown-menu">
						<li><a href="report_pdf.php?n=transcript.php&exams=false&studentid=<?php echo $user->id(); ?>">Without exam paper details</a></li>
					</ul>
				</div>
				</p>
				<p><button class="btn">Cert. College Membership</button></p>
				<p><button class="btn">Cert. College Membership v.2</button></p>
				<p><button class="btn">Council Tax Exemption</button></p>
				<p><button class="btn">Immigration Permit Confirmation</button></p>
			</div>
		</div>
		
		
	</div>
</div>