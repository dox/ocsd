<?php
$user = Tutors::find_by_uid($_GET['tutorid']);
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
		<div class="clearfix"></div>
	</div>
	<div class="col-md-8">
		<ul class="nav nav-tabs" id="myTab">
			<li class="active"><a href="#information" data-toggle="tab">Information</a></li>
		</ul>
		
		<div class="tab-content">
			<div class="tab-pane active" id="information">
			<?php
			printArray($user);
			?>
			</div>
		</div>
		
		
	</div>
</div>

<script src="modules/staff/js/upload.js"></script>