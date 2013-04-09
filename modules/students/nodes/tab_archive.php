<?php
include_once("../../../engine/initialise.php");
$archStudents = ArchStudents::find_all();
?>
<script src="js/jquery.fastLiveFilter.js"></script>

<input type="text" class="input-medium search-query" id="arch_search_input" placeholder="Quick Filter">

<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th>BodCard</th>
			<th>OUCS ID</th>
    		<th>Full Name</th>
    	</tr>
    </thead>
    <tbody id="arch_search_list">
    	<?php
	   	foreach($archStudents AS $user) {
    		echo "<tr>";
    		echo "<td>" . $user->bodcard() . "</td>";
    		echo "<td>" . $user->oucs_id . "</td>";
			echo "<td><a href=\"index.php?m=arch_students&n=user.php&arstudentid=" . $user->ar_studentid . "\">" . $user->fullDisplayName() . "</a></td>";
			echo "</tr>";
		}
		?>
	</tbody>
</table>

<script>
$(function() {
	$('#arch_search_input').fastLiveFilter('#arch_search_list');
});
</script>