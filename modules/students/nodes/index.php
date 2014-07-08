<script>
$(function() {
	$("#MainTabs").tab();
	
	// load first tab
	$('#MainTabs a:first').tab('show');

	
	// onclick on each tab, show the loading, then load the ajax content
	$('#MainTabs a').click(function (e) {
		e.preventDefault();
		
		$(this).tab('show');
		
		var contentID  = $(e.target).attr("data-target");
		var contentURL = $(e.target).attr("href");
		
		$(contentID).load(contentURL, function(){ $("#MainTabs").tab(); });
	})
});
</script>

<div class="page-header">
	<h1>Users</h1>
</div>
<p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>

<ul class="nav nav-tabs" id="MainTabs">
	<li><a data-target="#ajax_students" data-toggle="tab" href="modules/students/nodes/tab_students.php">Students</a></li>
	<li><a data-target="#ajax_tutors" data-toggle="tab" href="modules/students/nodes/tab_tutors.php">Tutors</a></li>
	<li><a data-target="#ajax_archive" data-toggle="tab" href="modules/students/nodes/tab_archive.php">Archive</a></li>
</ul>

<div class="tab-content">
	<div class="tab-pane" id="ajax_students"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
	<div class="tab-pane" id="ajax_tutors"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
	<div class="tab-pane" id="ajax_archive"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
</div>