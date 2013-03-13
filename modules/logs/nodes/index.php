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
				$output  = "<tr>";
				$output .= "<td>" . $log->date_stamp . "</td>";
				$output .= "<td>" . $log->username . " <span class=\"label\">" . $log->ip . "</span>" . "</td>";
				$output .= "<td><a href=\"index.php?m=students&n=user.php&studentid=" . $log->student_id . "\">" . $log->student_id ."</a></td>";
				$output .= "<td><code>" . $log->prev_value . "</code></td>";
				$output .= "<td><code>" . $log->updated_value . "</code></td>";
				$output .= "<td>" . $log->type . "</td>";
				$output .= "<td>" . $log->notes . "</td>";
				$output .= "</tr>";
				
				echo $output;
			}
			?>
			</tbody>
		</table>
	</div>
</div>