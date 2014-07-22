<?php
$award = Awards::find_by_uid($_GET['awdid']);
$issued = student_awardsClass::find_all_by_awdkey($award->awdid);
?>

<div class="page-header">
	<h1><?php echo $award->name; ?> <small>Awarded to <?php echo count($issued) . autoPluralise(" student", " students", count($issued)); ?></small></h1>
	<p class="lead">Type: <?php echo $award->type; ?><br />
	Given by: <?php echo $award->given_by; ?></p>
</div>

<table class="table table-striped">
	<thead>
		<tr>
			<th width="25%">Student Name</th>
			<th width="15%">Date Awarded</th>
			<th width="15%">Date From</th>
			<th width="15%">Date To</th>
			<th width="15%">Value</th>
			<th width="25%">Notes</th>
		</tr>
	</thead>
	<tbody id="awards_search_list">
	<?php
	foreach ($issued AS $issue) {
		$student = Students::find_by_uid($issue->studentkey);
		
		$output  = "<tr>";
		$output .= "<td><a href=\"index.php?m=students&n=user.php&studentid=" . $student->studentid . "\">" . $student->fullDisplayName() . "</a></td>";
		$output .= "<td>" . $issue->dt_awarded . "</td>";
		$output .= "<td>" . $issue->dt_from ."</td>";
		$output .= "<td>" . $issue->dt_to ."</td>";
		$output .= "<td>" . $issue->value ."</td>";
		$output .= "<td>" . $issue->notes ."</td>";
		$output .= "</tr>";
		
		echo $output;
	}
	?>
	</tbody>
</table>