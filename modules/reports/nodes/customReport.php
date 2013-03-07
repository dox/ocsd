<?php
$sql  = $_POST['textareaSQL'];

$report = new Students;

$reportResults = $report->find_by_sql($sql);
$objectVarArray = $report->object_vars();

foreach ($objectVarArray AS $key => $value) {
	$fieldArray[] = $key;
}
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