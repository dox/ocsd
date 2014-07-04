<?php
include_once("../../../engine/initialise.php");
$allTutors = Tutors::find_all();
?>
<script src="js/jquery.fastLiveFilter.js"></script>

<p><input type="text" class="form-control search-query" id="tutor_search_input" placeholder="Quick Filter"></p>

<a href="index.php?m=students&n=add_tutor.php" class="btn btn-primary">Add New</a>
<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th>BodCard</th>
    		<th>Full Name</th>
    	</tr>
    </thead>
    <tbody id="tutor_search_list">
    	<?php
	   	foreach($allTutors AS $user) {
    		echo "<tr>";
    		echo "<td>" . $user->tutid . "</td>";
			echo "<td><a href=\"index.php?m=staff&n=staff.php&tutorid=" . $user->tutid . "\">" . $user->fullDisplayName() . "</a> " . $user->identifier . "</td>";
			echo "</tr>";
		}
		?>
	</tbody>
</table>

<script>
$(function() {
	$('#tutor_search_input').fastLiveFilter('#tutor_search_list');
});
</script>