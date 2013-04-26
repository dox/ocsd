<?php
include_once("../../../engine/initialise.php");
$allTutors = Tutors::find_all();
?>
<script src="js/jquery.fastLiveFilter.js"></script>

<input type="text" class="input-medium search-query" id="tutor_search_input" placeholder="Quick Filter">

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