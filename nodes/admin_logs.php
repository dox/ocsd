<?php
	// DELETE OLD LOGS
	$db->where("DATE_SUB(CURDATE(),INTERVAL 180 DAY) >= date_created");
	$db->delete('_logs');
	
	// FETCH CURRENT LOGS
	$logs = $db->orderBy("date_created", "Desc");
	$logs = $db->get('_logs', 1000);
	$logsCount = $db->count;
?>
<div class="bls">
	<div class="blt">
		<h6 class="blv">Admin / Logs</h6>
		<h2 class="blu">Logs</h2>
	</div>
</div>

<div class="bml bks">
	<div class="abw">
		<?php
			$logonsAll = $db->where('type', "LOGON");
			$logonsAll = $db->get('_logs', '15');
			$logonsAllCount = $db->count;
			
			$logonsByDay = $db->rawQuery("SELECT DATE(date_created) AS date_created, COUNT(*) AS cnt FROM _logs WHERE type = 'LOGON' GROUP BY DATE(date_created) ORDER BY date_created ASC");
			foreach ($logonsByDay AS $day) {
				$logonsCountArray[] = $day['cnt'];
			}
			$logonsCountArray = array_reverse($logonsCountArray);
		?>
	</div>
	
	<canvas id="sparkline1" width="378" height="94"
	class="bmm"
	data-chart="spark-line"
	data-dataset="[[<?php echo implode($logonsCountArray,","); ?>]]"
	data-labels="['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o']"
	style="width: 189px; height: 47px;"></canvas>
</div>
  
<div class="by aaj">
	<h6 class="atf">Log Events</h6>
	
	<?php
		foreach ($logs AS $log) {
			if (isset($log["cudid"])) {
				$logLink = "index.php?n=students_unique&cudid=" . $log['cudid'];
			} else {
				$logLink = "#";
			}
			
			$output  = "<a class=\"mo od tc ra\" href=\"" . $logLink . "\">";
			$output .= "<span><strong>" . date('Y-m-d H:i:s', strtotime($log['date_created'])) . "</strong> " . $log['description'] . "</span>";
			$output .= "<span class=\"asd\">" . $log['type'] . "</span>";
			
			echo $output;
		}
	?>
			
		
		
	</a>
	
</div>
<?php
if ($logsCount == 1000) {
	echo "<a href=\"#docsModal\" data-toggle=\"modal\" class=\"ce ko acb\">View more logs</a>";
} else {
	echo "Total logs: " . $logsCount;
}
?>

<div id="docsModal" class="cb fade" tabindex="-1" role="dialog" aria-labelledby="bmp">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="ol">
				<h4 class="modal-title" id="myModalLabel">Additional Logs Currently Unavailable </h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<p>More logs are available, but they're currently not available online.  Please contact the Site Administrator to access them.</p>
			</div>
			<div class="om">
				<button type="button" class="ce kh" data-dismiss="modal">Okay</button>
			</div>
		</div>
	</div>
</div>