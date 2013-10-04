<?php
$user = Students::find_by_uid($_GET['studentid']);
$residences = ResidenceAddresses::find_all_by_student($_GET['studentid']);
$addresses = Addresses::find_all_by_student($_GET['studentid']);
$birthCountry = Countries::find_by_uid($user->birth_cykey);
$residenceCountry = Countries::find_by_uid($user->resid_cykey);
$citizenshipCountry = Countries::find_by_uid($user->citiz_cykey);
$ethnicCountry = Countries::find_by_uid($user->ethkey);
$degree = Grads::find_by_studentkey($user->studentid);
$subject = QualSubjects::find_by_qsid($degree->qskey);

$studentAwards = student_awardsClass::find_by_studentkey($user->id());
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
		<div>
			<?php echo $user->imageURL(true); ?>
			<div id="image-list"></div>
		</div>
		<form id="photoUploadForm" method="post" enctype="multipart/form-data"  action="modules/students/actions/photoUpload.php">  
			<input type="file" name="images" id="images" />  
			<button type="submit" id="btn">Upload Files!</button>
			<input type="hidden" id="studentkey" value="<?php echo $user->id(); ?>">
		</form>  
        <div id="response"></div>
        <hr />
		<div class="clearfix"></div>
		<p style="white-space:nowrap;"><i class="icon-barcode"></i> <span id="bodcard" class="inlineEditble" data-type="text" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_students.php" data-original-title="Bodcard"><?php echo $user->bodcard(); ?></span></p>
		<p style="white-space:nowrap;"><i class="icon-user"></i> <span id="oucs_id" class="inlineEditble" data-type="text" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_students.php" data-original-title="OUCS ID"><?php echo $user->oucs_id; ?></span></p>
		<p style="white-space:nowrap;"><i class="icon-qrcode"></i> OSS: <span id="oss_pn" class="inlineEditble" data-type="text" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_students.php" data-original-title="OSS ID"><?php echo $user->oss_pn; ?></span></p>
		<p style="white-space:nowrap;"><i class="icon-comment"></i> <span id="mobile" class="inlineEditble" data-type="text" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_students.php" data-original-title="Mobile Telephone Number"><?php echo $user->mobile; ?></span></p>
		
		<p style="white-space:nowrap;"><i class="icon-envelope"></i> <span id="email1" class="inlineEditble" data-type="text" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_students.php" data-original-title="Oxford E-Mail Address"><a href="mailto:<?php echo $user->email1; ?>"><?php echo $user->email1; ?></a></span></p>
		
		<p style="white-space:nowrap;"><i class="icon-envelope"></i> <span id="email2" class="inlineEditble" data-type="text" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_students.php" data-original-title="Personal E-Mail Address"><a href="mailto:<?php echo $user->email2; ?>"><?php echo $user->email2; ?></a></span></p>

		
		<p><i class="icon-globe"></i> <span id="nationality" class="inlineEditble" data-type="text" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_students.php" data-original-title="Nationality"><?php echo $user->nationality; ?></span></p>
		
		<?php
		if (isingroup("OCSD Edit")) {
			echo "<p><button id=\"enableEdit\" class=\"btn\">Enable Edit Mode &raquo;</button></p>";
		}
		?>
		<div class="clearfix"></div>
	</div>
	<div class="span9">
		<a href="index.php?n=contact.php&studentName=<?php echo $user->fullDisplayName();?>&studentID=<?php echo $user->studentid; ?>" class="btn btn-primary btn-mini pull-right"><i class="icon-flag icon-white"></i> Contact College Office</a>
		<ul class="nav nav-tabs" id="myTab">
			<li class="active"><a href="#information" data-toggle="tab">Information</a></li>
			<li><a href="#addresses" data-toggle="tab">Addresses</a></li>
			<li><a href="#education" data-toggle="tab">Education</a></li>
			<li><a href="#college" data-toggle="tab">College</a></li>
			<li><a href="#awards" data-toggle="tab">Awards</a></li>
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
		<p><button class="btn btn-mini pull-right disabled" type="button">Last Modified By: <?php echo $user->who_mod . " (" . convertToDateString($user->dt_lastmod) . ")"; ?></button></p>
			</div>
			<div class="tab-pane" id="addresses">
				<?
				$resStatusClass = new resStatus;
				$resStatuses = $resStatusClass->find_all();
				$resStatus = $resStatusClass->find_by_uid($user->rskey);
				?>
				<p class="lead">Resident Status: <?php echo $resStatus->status; ?></p>
				<h3>Home Residence</h3>
				
				<?php
				foreach ($addresses AS $address) {
					echo "<div class=\"row\">";
					echo "<div class=\"span3\">";
					echo $address->displayAddress();
					echo "</div>";
					echo "<div class=\"span6\">";
					
					$googleFrame  = "<iframe width=\"425\" height=\"350\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" ";
					$googleFrame .= "src=\"https://maps.google.co.uk/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=";
					$googleFrame .= $address->line1 . " " . $address->county . " " . $address->postcode;
					//$googleFrame .= "&amp;aq=&amp;sll=53.800651,-4.064941&amp;sspn=6.725398,25.444336&amp;ie=UTF8&amp;hq=&amp;hnear=53+Gwendwr+Rd,+London+W14+9BG,+United+Kingdom";
					$googleFrame .= "&amp;t=m&amp;z=14&amp;iwloc=A&amp;output=embed\">";
					$googleFrame .= "</iframe><br />";
					
					echo "<p>" . $googleFrame . "</p>";
					echo "</div>";
					echo "</div>";
				}
				?>
				
				<h3>College Residence</h3>
				<?php
				foreach ($residences AS $resAddress) {
					echo "<div class=\"row\">";
					echo "<div class=\"span9\">";
					echo $resAddress->displayAddress();
					echo "</div>";
					echo "</div>";
				}
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
			<div class="tab-pane" id="awards">
				<div id="awardsFormAdd">
					<form class="form-horizontal">
						<div class="control-group">
							<label class="control-label" for="inputAward">Award</label>
							<div class="controls">
								<select id="inputAward">
								
									<?php
									$awards = Awards::find_all();
									foreach($awards AS $award) {
										$output  = "<option value=\"" . $award->awdid . "\">";
										$output .= $award->name . " (" . $award->type . ")";
										$output .= "</option>";
										
										echo $output;
									}
									?>
									
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="inputDateAwarded">Date Awarded</label>
							<div class="controls">
								<input type="date" id="inputDateAwarded" placeholder="YYYY-MM-DD" value="<?php echo convertToDateString(null,false); ?>">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="inputDateFrom">Date From</label>
							<div class="controls">
								<input type="date" id="inputDateFrom" placeholder="YYYY-MM-DD" value="<?php echo convertToDateString(null,false); ?>">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="inputDateTo">Date To</label>
							<div class="controls">
								<input type="date" id="inputDateTo" placeholder="YYYY-MM-DD" value="<?php echo convertToDateString(null,false); ?>">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="inputAwardValue">Value</label>
							<div class="controls">
								<div class="input-prepend">
									<span class="add-on">£</span>
									<input class="span2" id="inputAwardValue" type="number">
								</div>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="inputNotes">Notes</label>
							<div class="controls">
								<textarea rows="3" id="inputNotes"></textarea>
							</div>
						</div>
						<button id="awardAddButton" type="button" class="btn">Submit</button>
						<input type="hidden" id="inputStudentkey" value="<?php echo $user->studentid; ?>">
					</form>
					<div id="response_added"></div>
					<div class="clearfix"></div>
				</div>
				<?php
				foreach ($studentAwards AS $studentAward) {
					$award = Awards::find_by_uid($studentAward->awdkey);
					
					echo "<div>";
					$button  = "<button class=\"btn btn-mini btn-danger pull-right awardDeleteButton\" id=\"" . $studentAward->sawid . "\">Delete</button>";
					$button .= "<button class=\"btn btn-mini pull-right\">Edit</button>";
					$button .= "";
					
					echo $button;
					
					echo "<h3>" . $award->name . " <span class=\"label\">" . $award->given_by . " " . $award->type . "</span></h3>";
					
					echo "<p>Awarded: " . $studentAward->dt_awarded . "</p>";
					echo "<p>From: " . convertToDateString($studentAward->dt_from) . " - To: " . convertToDateString($studentAward->dt_to) . "</p>";
					echo "<p>Value: " . $studentAward->value . "</p>";
					
					if (isset($studentAward->notes)) {
						echo "<p>Notes: " . $studentAward->notes . "</p>";
					}
					echo "</div>";
					echo "<hr />";
				}
				?>
			</div>
			<div class="tab-pane" id="reports">
				<p>
				<div class="btn-group">
					<?php
					echo "<a href=\"report_pdf.php?n=transcript.php&studentid=" . $user->id() . "\" class=\"btn\">Generate Transcript</a>";
					?>
					<button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
					<ul class="dropdown-menu">
						<li class="nav-header">With Letterhead</li>
						<li><a href="report_pdf.php?n=transcript.php&studentid=<?php echo $user->id(); ?>">With exam paper details</a></li>
						<li><a href="report_pdf.php?n=transcript.php&exams=false&studentid=<?php echo $user->id(); ?>">Without exam paper details</a></li>
						
						<li class="nav-header">Without Letterhead</li>
						<li><a href="report_pdf.php?n=transcript.php&studentid=<?php echo $user->id(); ?>&header=false">With exam paper details</a></li>
						<li><a href="report_pdf.php?n=transcript.php&exams=false&studentid=<?php echo $user->id(); ?>&header=false">Without exam paper details</a></li>
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

<script>
$(".awardDeleteButton").click(function() {
	var student_awdkey = $(this).attr('id');
	
	var url = 'modules/awards/actions/deleteStudentAward.php';
	
	if(confirm("Are you sure you want to delete this award?")) {
		$.post(url,{
			student_awdkey: student_awdkey
		}, function(data) {
		},'html');
	} else {
		e.preventDefault();
	}
	
	$(this).parent("div").hide();
	return false;
});

$("#awardsFormAdd").hide();
$("#photoUploadForm").hide();
$(".awardDeleteButton").hide();

//$.fn.editable.defaults.mode = 'inline';
$('.inlineEditble').editable('destroy');

$("#enableEdit").click(function() {
	if ($("#enableEdit").html() == "Disable Edit Mode") {
		$('.inlineEditble').editable('destroy');

		
		$("#awardsFormAdd").hide();
		$('#photoUploadForm').hide();
		$('.awardDeleteButton').hide();
		
		$("#enableEdit").html("Enable Edit Mode &raquo;");
		$("#enableEdit").removeClass("btn-warning");
	} else {
		$("#enableEdit").addClass("btn-warning");
		$("#enableEdit").html('Disable Edit Mode');
		
		$('#awardsFormAdd').show('slow');
		$('#photoUploadForm').show('slow');
		$('.awardDeleteButton').show();
		
		$('.inlineEditble').editable('enable');
		$('#gender').editable({
			source: [
				{value: 'M', text: 'Male'},
				{value: 'F', text: 'Female'}
			]
		});
    }
});
</script>

<script>
$("#awardAddButton").click(function() {
	var studentkey = $("input#inputStudentkey").val();
	var awdkey = $("select#inputAward").val();
	
	var dt_awarded = $("input#inputDateAwarded").val();
	if (dt_awarded == "") {
		alert("There is no 'Date Awarded' specified.");
		return false;
	}
	
	var dt_from = $("input#inputDateFrom").val();
	if (dt_from == "") {
		alert("There is no 'Date From' specified.");
		return false;
	}
	
	var dt_to = $("input#inputDateTo").val();
	if (dt_to == "") {
		alert("There is no 'Date To' specified.");
		return false;
	}
	
	var value = $("input#inputAwardValue").val();
	var notes = $("textarea#inputNotes").val();

	var url = 'modules/awards/actions/addStudentAward.php';
	
	// perform the post to the action (take the info and submit to database)
	$.post(url,{
		studentkey: studentkey,
		awdkey: awdkey,
		dt_awarded: dt_awarded,
		dt_from: dt_from,
		dt_to: dt_to,
		value: value,
		notes: notes
	}, function(data){
		alert('Award added - please refresh this page');
	},'html');
	
	return false;
});


</script>

  	
	
<script src="modules/students/js/upload.js"></script>