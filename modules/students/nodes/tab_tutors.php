<?php
include_once("../../../engine/initialise.php");
$allTutors = Tutors::find_all();
?>
<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th>BodCard</th>
    		<th>Full Name</th>
    	</tr>
    </thead>
    <tbody>
    	<?php
	   	foreach($allTutors AS $user) {
    		echo "<tr>";
    		echo "<td>" . $user->tutid . "</td>";
			echo "<td><a href=\"index.php?n=404.php&tutorid=" . $user->tutid . "\">" . $user->fullDisplayName() . "</a> " . $user->identifier . "</td>";
			echo "</tr>";
		}
		?>
	</tbody>
</table>