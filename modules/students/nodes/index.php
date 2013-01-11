<?php
$allStudents = Students::find_all();
$allTutors = Tutors::find_all();
?>
<div class="row">
	<div class="span12">
		<div class="page-header">
			<h1>Users <small><?php echo count($allStudents) . " Students"; ?></small></h1>
		</div>
		<p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
		
		<div class="tabbable"> <!-- Only required for left/right tabs -->
			<ul class="nav nav-tabs">
				<li class="active"><a href="#students" data-toggle="tab">Students On Roll</a></li>
				<li><a href="#staff" data-toggle="tab">Tutors</a></li>
				<li><a href="#archive" data-toggle="tab">Archive</a></li>
			</ul>
			
			<div class="tab-content">
				<div class="tab-pane active" id="students">
					<p>I'm in Section 1.</p>
					<table class="table table-bordered table-striped">
						<thead>
						<tr>
							<th>BodCard</th>
							<th>OUCS ID</th>
							<th>Full Name</th>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach($allStudents AS $user) {
							echo "<tr>";
							echo "<td>" . $user->bodcard() . "</td>";
							echo "<td>" . $user->oucs_id . "</td>";
							echo "<td><a href=\"index.php?m=students&n=user.php&studentid=" . $user->studentid . "\">" . $user->fullDisplayName() . "</a></td>";
							echo "</tr>";
						}
						?>
						</tbody>
					</table>
				</div>
				
				<div class="tab-pane" id="staff">
					<table class="table table-bordered table-striped">
						<thead>
						<tr>
							<th>BodCard</th>
							<th>Full Name</th>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach($allTutors AS $tutor) {
							echo "<tr>";
							echo "<td>" . $tutor->tutid . "</td>";
							echo "<td><a href=\"index.php?m=students&n=user.php&studentid=" . $tutor->tutid . "\">" . $tutor->fullDisplayName() . "</a> " . $tutor->identifier . "</td>";
							echo "</tr>";
						}
						?>
						</tbody>
					</table>
				</div>
				
				<div class="tab-pane" id="archive">
					<p><?php //printArray($allStudents); ?>
				</div>
			</div>
		</div>
		<p><a class="btn" href="#">View details &raquo;</a></p>
	</div>
</div>


