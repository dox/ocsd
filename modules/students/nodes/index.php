<div class="row">
	<div class="span12">
		<div class="page-header">
			<h1>Users <small><?php echo count($allStudents) . " Students"; ?></small></h1>
		</div>
		<p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
		
		<div class="tabbable"> <!-- Only required for left/right tabs -->
			<ul class="nav nav-tabs" id="ajax_tabs">
				<li class="active"><a data-toggle="tab" href="#ajax_students">Students</a></li>
				<li><a data-toggle="tab" href="#ajax_tutors">Tutors</a></li>
				<li><a data-toggle="tab" href="#ajax_archive">Archive</a></li>
			</ul>
			
			<div class="tab-pane active" id="ajax_students" data-target="modules/students/nodes/tab_students.php"></div>
			<div class="tab-pane" id="ajax_tutors" data-target="modules/students/nodes/tab_tutors.php"></div>
			<div class="tab-pane" id="ajax_archive" data-target="modules/students/nodes/tab_archive.php"></div>
		</div>
		
		<p><a class="btn" href="#">View details &raquo;</a></p>
	</div>
</div>

