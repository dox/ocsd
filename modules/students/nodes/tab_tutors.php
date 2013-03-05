<?php
include_once("../../../engine/initialise.php");
$allTutors = Tutors::find_all();
?>
<div class="well" id="tutorfilter">
	<form class="form-inline">
		<select>
			<option>- All -</option>
			<option>2</option>
			<option>3</option>
			<option>4</option>
			<option>5</option>
		</select>
		<select>
			<option>- All -</option>
			<option>2</option>
			<option>3</option>
			<option>4</option>
			<option>5</option>
		</select>
		
		<button type="submit" class="btn">Filter</button>
	</form>
</div>

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