<?php
include_once("../../../engine/initialise.php");
$allStudents = Students::find_all();
?>
<script src="js/jquery.fastLiveFilter.js"></script>

<p><input type="text" class="form-control search-query" id="search_input" placeholder="Quick Filter"></p>

<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th>BodCard</th>
			<th>OUCS ID</th>
    		<th>Full Name</th>
    	</tr>
    </thead>
    <tbody id="search_list">
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

<script>
$(function() {
	$('#search_input').fastLiveFilter('#search_list');
});
</script>