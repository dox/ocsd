<?php
$sql  = "SELECT ";

foreach ($_POST['fields'] AS $field) {
	$fieldArray[] = $field;
}

$sql .= implode(", ", $fieldArray) . " ";
$sql .= "FROM students WHERE ";

if (isset($_POST['course_yr'])) {
	foreach ($_POST['course_yr'] AS $course_yr) {
		$courseArray[] = "course_yr = '" . $course_yr . "' ";
	}
	
	$sql .= implode("OR ", $courseArray);
}

$reportResults = Students::find_by_sql($sql);

?>

<div class="row">
	<div class="span12">
		<div class="page-header">
			<h1>Custom Report <small><?php echo count($reportResults) . autoPluralise(" result found", " results found", count($reportResults)) ;?></small></h1>
		</div>
	</div>
</div>
<div class="row">
	<div class="span12">
		<p><code><?php echo $sql; ?></code></p>
		
		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<?php
					foreach ($fieldArray AS $field) {
						echo "<th>" . $field . "</th>";
					}
					?>
				</tr>
			</thead>
			<tbody>
    			<?php
			   	foreach($reportResults AS $user) {
			   		echo "<tr>";
					foreach ($fieldArray AS $field) {
						echo "<td>" . $user->$field . "</td>";
					}
					
					echo "</tr>";
				}
				?>
			</tbody>
		</table>
	</div>
</div>