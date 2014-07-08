<?php
$user = Tutors::find_by_uid($_GET['tutorid']);
$titles = Titles::find_all();

?>
<div class="page-header">
	<h1><?php echo $user->fullDisplayName(); ?> <small><?php echo $user->identifier; ?></small></h1>
</div>
<div class="row">
	<div class="col-md-4">
		<div>
			<?php echo $user->imageURL(true); ?>
			<div id="image-list"></div>
		</div>
		<form id="photoUploadForm" method="post" enctype="multipart/form-data"  action="modules/staff/actions/photoUpload.php">  
			<input type="file" name="images" id="images" />  
			<button type="submit" id="btn">Upload Files!</button>
			<input type="hidden" id="tutorkey" value="<?php echo $user->id(); ?>">
		</form>
        <div id="response"></div>
        <hr />
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
		</ul>
		
		<div class="tab-content">
			<div class="tab-pane active" id="information">
				<p>Title Key: <span id="titlekey" data-type="select" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_staff.php" data-value="<?php echo $user->titlekey; ?>" data-original-title="Titley"><?php echo $user->title(); ?></span></p>
				<p>Initials: <span id="initials" class="inlineEditble" data-type="text" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_staff.php" data-original-title="Initials"><?php echo $user->initials; ?></span></p>
				<p>Forenames: <span id="forenames" class="inlineEditble" data-type="text" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_staff.php" data-original-title="Forenames"><?php echo $user->forenames; ?></span></p>
				<p>Surname: <span id="surname" class="inlineEditble" data-type="text" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_staff.php" data-original-title="Surname"><?php echo $user->surname; ?></span></p>
				<p>Identifier: <span id="identifier" class="inlineEditble" data-type="text" data-pk="<?php echo $user->id(); ?>" data-url="/ocsd/actions/u_staff.php" data-original-title="Identifier"><?php echo $user->identifier; ?></span></p>
			</div>
		</div>
		
		
	</div>
</div>

<script>
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
		
		$('#photoUploadForm').show('slow');
		
		$('.inlineEditble').editable('enable');
		$('#titlekey').editable({
			source: [
				<?php
				foreach ($titles AS $title) {
					$outputArray[] = "{value: '" . $title->titleid . "', text: '" . $title->title . "'}";
				}
				
				echo implode(",", $outputArray);
			?>
			]
		});
    }
});
</script>

<script src="modules/staff/js/upload.js"></script>