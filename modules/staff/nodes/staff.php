<?php
$user = Tutors::find_by_uid($_GET['tutorid']);
?>
<div class="row">
	<div class="span12">
		<div class="page-header">
			<h1><?php echo $user->fullDisplayName(); ?></h1>
		</div>
	</div>
</div>
<div class="row">
	<div class="span3">
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
	<div class="span9">
		<a href="index.php?n=contact.php&studentName=<?php echo $user->fullDisplayName();?>&studentID=<?php echo $user->studentid; ?>" class="btn btn-primary btn-mini pull-right"><i class="fa fa-flag"></i> Contact College Office</a>
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