<?php
$logs = Logs::find_all();
?>
<div class="row">
	<div class="span12">
		<div class="page-header">
			<h1>Logs <small></small></h1>
		</div>
	</div>
</div>
<div class="row">
	<div class="span12">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Date Stamp</th>
					<th>Username</th>
					<th>Student ID</th>
					<th>Previous Value</th>
					<th>Updated Value</th>
					<th>Type</th>
					<th>Notes</th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ($logs AS $log) {
				if (!isset($log->student_id)) {
					$log->student_id = "3";
				}
				$output  = "<tr>";
				$output .= "<td>" . $log->date_stamp . "</td>";
				
				$output .= "<td>" . $log->username . " <span class=\"label\">" . $log->ip . "</span>" . "</td>";
				
				if ($log->student_id == 0) {
					$output .= "<td></td>";
				} else {
					$output .= "<td><a href=\"index.php?m=students&n=user.php&studentid=" . $log->student_id . "\">" . $log->student_id ."</a></td>";
				}
				
				if ($log->prev_value == "") {
					$output .= "<td></td>";
				} else {
					$output .= "<td><code>" . $log->prev_value . "</code></td>";
				}
				
				if ($log->updated_value == "") {
					$output .= "<td></td>";
				} else {
					$output .= "<td><code>" . $log->updated_value . "</code></td>";
				}
				
				if ($log->type == "") {
					$output .= "<td>unknown</td>";
				} else {
					$output .= "<td>" . $log->type . "</td>";
				}
				
				$output .= "<td>" . $log->notes . "</td>";
				$output .= "</tr>";
				
				echo $output;
			}
			?>
			</tbody>
		</table>
	</div>
</div>