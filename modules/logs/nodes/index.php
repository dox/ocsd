<?php
$logs = Logs::find_all();
?>
<script src="js/jquery.fastLiveFilter.js"></script>
<script src="http://code.highcharts.com/highcharts.js"></script>

<div class="page-header">
	<h1>Logs <small></small></h1>
</div>

<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>

<div class="row">
	<div class="col-xs-3">
		<input type="text" class="form-control search-query" id="logs_search_input" placeholder="Quick Filter">
	</div>
</div>

<table class="table table-striped">
	<thead>
	    <tr>
	    	<th width="20%">Date Stamp</th>
	    	<th width="12%">Username</th>
	    	<th width="15%">Student ID</th>
	    	<th width="7%">Previous Value</th>
	    	<th width="7%">Updated Value</th>
	    	<th width="10%">Type</th>
	    	<th width="29%">Notes</th>
	    </tr>
	</thead>
	<tbody id="logs_search_list">
	<?php
	foreach ($logs AS $log) {
	    if (!isset($log->student_id)) {
	    	$log->student_id = "3";
	    }
	    $output  = "<tr>";
	    $output .= "<td>" . $log->date_stamp . "</td>";
	    
	    $output .= "<td>" . $log->username . " <span class=\"label label-default\">" . $log->ip . "</span>" . "</td>";
	    
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


<script>
$(function() {
	$('#logs_search_input').fastLiveFilter('#logs_search_list');
});
</script>

<?php
foreach ($logs AS $log) {
	$logType = $log->type;
	$logDate = date('Y-m-d', strtotime($log->date_stamp));
	
	$logArray[$logType][$logDate] = $logArray[$logType][$logDate] + 1;
}


foreach ($logArray AS $logTypeName => $logs) {
	$logType = $logTypeName;
	
	foreach ($logs AS $date => $value) {
		$logType;
		$logDateString[$logType][] = "[Date.UTC(" . date('Y', strtotime($date)) . ", " . (date('m', strtotime($date)) - 1) . ", "  . date('d', strtotime($date)) . "), " . $value . "]";
	}
}
?>

<script>
$(function () {
	$('#container').highcharts({
		chart: {
			type: 'spline'
		},
		credits: {
			enabled: false
		},
		title: {
			text: 'Logs History Over Time'
		},
		xAxis: {
			type: 'datetime',
			dateTimeLabelFormats: { // don't display the dummy year
			month: '%e. %b',
			year: '%b'
		}
	},
	yAxis: {
		title: {
			text: 'Total Log Events'
		},
		min: 0
	},
	tooltip: {
		formatter: function() {
			return '<b>'+ this.series.name +' events</b><br/>'+
			Highcharts.dateFormat('%e. %b', this.x) +': '+ this.y;
		}
	},
	series: [
		<?php
		foreach ($logDateString AS $name => $logs) {
			$output  = "{ name: '" . $name . "',";
			$output .= "data: [" . implode(",", $logs) . "] }";
			
			$seriesOutput[] = $output;
		}
		
		echo implode(",", $seriesOutput);
		?>
	]
	});
});
</script>