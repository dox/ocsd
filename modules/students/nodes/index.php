<div class="row">
	<div class="span12">
		<div class="page-header">
			<h1>Users <small><?php echo count($allStudents) . " Students"; ?></small></h1>
		</div>
		<p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
		
		<ul class="nav nav-tabs" id="MainTabs">
			<li><a data-target="#ajax_students" data-toggle="tab" href="modules/students/nodes/tab_students.php">Students</a></li>
			<li><a data-target="#ajax_tutors" data-toggle="tab" href="modules/students/nodes/tab_tutors.php">Tutors</a></li>
			<li><a data-target="#ajax_archive" data-toggle="tab" href="modules/students/nodes/tab_archive.php">Archive</a></li>
		</ul>
		
		<div class="tab-content">
			<div class="tab-pane" id="ajax_students"><i class="icon-refresh"></i> Loading...</div>
			<div class="tab-pane" id="ajax_tutors"><i class="icon-refresh"></i> Loading...</div>
			<div class="tab-pane" id="ajax_archive"><i class="icon-refresh"></i> Loading...</div>
		</div>
		
		<p><a class="btn" href="#">View details &raquo;</a></p>
	</div>
</div>

