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

<div class="page-header">
	<h1><?php echo $user->fullDisplayName(true); ?> <small> Cohort: <?php echo $user->yr_cohort; ?></small></h1>
</div>

<style>
#map-canvas {
	width: 100%;
	height: 500px;
}
</style>


<div class="row">
	<div class="col-md-4">
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
		<p style="white-space:nowrap;"><i class="fa fa-barcode"></i> <span id="bodcard" class="inlineEditble" data-type="text" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_students.php" data-original-title="Bodcard"><?php echo $user->bodcard(); ?></span></p>
		<p style="white-space:nowrap;"><i class="fa fa-university"></i> <span id="oucs_id" class="inlineEditble" data-type="text" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_students.php" data-original-title="OUCS ID"><?php echo $user->oucs_id; ?></span></p>
		<p style="white-space:nowrap;"><i class="fa fa-graduation-cap"></i> OSS: <span id="oss_pn" class="inlineEditble" data-type="text" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_students.php" data-original-title="OSS ID"><?php echo $user->oss_pn; ?></span></p>
		<p style="white-space:nowrap;"><i class="fa fa-mobile"></i> <span id="mobile" class="inlineEditble" data-type="text" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_students.php" data-original-title="Mobile Telephone Number"><?php echo $user->mobile; ?></span></p>
		
		<p style="white-space:nowrap;"><i class="fa fa-envelope"></i> <span id="email1" class="inlineEditble" data-type="text" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_students.php" data-original-title="Oxford E-Mail Address"><a href="mailto:<?php echo $user->email1; ?>"><?php echo $user->email1; ?></a></span></p>
		
		<p style="white-space:nowrap;"><i class="fa fa-envelope-o"></i> <span id="email2" class="inlineEditble" data-type="text" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_students.php" data-original-title="Personal E-Mail Address"><a href="mailto:<?php echo $user->email2; ?>"><?php echo $user->email2; ?></a></span></p>

		
		<p><i class="fa fa-globe"></i> <span id="nationality" class="inlineEditble" data-type="text" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_students.php" data-original-title="Nationality"><?php echo $user->nationality; ?></span></p>
		
		<?php
		if (isingroup("OCSD Edit")) {
			echo "<p><button id=\"enableEdit\" class=\"btn\">Enable Edit Mode &raquo;</button></p>";
		}
		?>
		<div class="clearfix"></div>
	</div>
	<div class="col-md-8">
		<ul class="nav nav-tabs" id="myTab">
			<li class="active"><a href="#information" data-toggle="tab">Information</a></li>
			<li><a href="#addresses" data-toggle="tab">Addresses</a></li>
			<li><a href="#education" data-toggle="tab">Education</a></li>
			<li><a href="#college" data-toggle="tab">College</a></li>
			<li><a href="#awards" data-toggle="tab">Awards</a></li>
			<li><a href="#reports" data-toggle="tab">Reports</a></li>
		</ul>
		
		<div class="tab-content">
			<?php include('user_information.php'); ?>
			<?php include('user_addresses.php'); ?>
			<div class="tab-pane" id="education">
				<p>Coming soon</p>
			</div>
			<?php include('user_college.php'); ?>
			<?php include('user_awards.php'); ?>
			<?php include('user_reports.php'); ?>
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
$("#contactFormAdd").hide();
$("#photoUploadForm").hide();
$(".awardDeleteButton").hide();

//$.fn.editable.defaults.mode = 'inline';
$('.inlineEditble').editable('destroy');

$("#enableEdit").click(function() {
	if ($("#enableEdit").html() == "Disable Edit Mode") {
		$('.inlineEditble').editable('destroy');

		
		$("#awardsFormAdd").hide();
		$("#contactFormAdd").hide();
		$('#photoUploadForm').hide();
		$('.awardDeleteButton').hide();
		
		$("#enableEdit").html("Enable Edit Mode &raquo;");
		$("#enableEdit").removeClass("btn-warning");
	} else {
		$("#enableEdit").addClass("btn-warning");
		$("#enableEdit").html('Disable Edit Mode');
		
		$('#awardsFormAdd').show('slow');
		$("#contactFormAdd").show('slow');
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

$("#addressAddButton").click(function() {
	var studentkey = $("input#inputStudentkey").val();
	var line1 = $("input#inputAdd1").val();
	var line2 = $("input#inputAdd2").val();
	var line3 = $("input#inputAdd3").val();
	var line4 = $("input#inputAdd4").val();
	var town = $("input#inputTown").val();
	var county = $("input#inputCounty").val();
	var postcode = $("input#inputPostcode").val();
	var cykey = $("select#inputCykey").val();
	var phone = $("input#inputTelephone").val();
	var mobile = $("input#inputMobile").val();
	var email = $("input#inputEmail").val();
	var fax = $("input#inputFax").val();
	var defalt = $("input#inputDefault").val();
	var atkey = $("select#inputAtkey").val();
	
	var url = 'modules/addresses/actions/addStudentAddress.php';
	
	// perform the post to the action (take the info and submit to database)
	$.post(url,{
		studentkey: studentkey,
		line1: line1,
		line2: line2,
		line3: line3,
		line4: line4,
		town: town,
		county: county,
		postcode: postcode,
		cykey: cykey,
		phone: phone,
		mobile: mobile,
		email: email,
		fax: fax,
		defalt: defalt,
		atkey: atkey
	}, function(data){
		alert('Address added - please refresh this page');
	},'html');
	
	return false;
});
</script>

<script src="modules/students/js/upload.js"></script>