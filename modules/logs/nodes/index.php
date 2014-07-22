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
	<div class="col-xs-9">
		<button class="btn btn-danger pull-right" id="purgeOldLogsButton">Purge Old Logs</button>
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
	$logDate = date('z', strtotime($log->date_stamp));
	
	$logArrayByType[$logDate][$log->type] ++;
	
	if ($log->type == "logon") {
		$logsByDay_logon[$logDate] = $logsByDay_logon[$logDate] + 1;
	} elseif ($log->type == "create") {
		$logsByDay_create[$logDate] = $logsByDay_create[$logDate] + 1;
	} elseif ($log->type == "delete") {
		$logsByDay_delete[$logDate] = $logsByDay_delete[$logDate] + 1;
	} elseif ($log->type == "report") {
		$logsByDay_report[$logDate] = $logsByDay_create[$logDate] + 1;
	}
}
?>


<?php
// take the array $logsByDay_logon and re order to friendly date names, and only the last 3o days
$totalDays = 30;

$i = 0;
do {
	$date = strtotime("-" . $i . " day");
	$friendlyDate = "'" . date('M d',$date) . "'";
	
	if ($logsByDay_logon[date('z',$date)] <= 0) {
		$value = 0;
	} else {
		$value = $logsByDay_logon[date('z',$date)];
	}
	
	$graphData_logon[$friendlyDate] = $value;
	$i++;
} while ($i < $totalDays);

$i = 0;
do {
	$date = strtotime("-" . $i . " day");
	$friendlyDate = "'" . date('M d',$date) . "'";
	
	if ($logsByDay_create[date('z',$date)] <= 0) {
		$value = 0;
	} else {
		$value = $logsByDay_create[date('z',$date)];
	}
	
	$graphData_create[$friendlyDate] = $value;
	$i++;
} while ($i < $totalDays);

$i = 0;
do {
	$date = strtotime("-" . $i . " day");
	$friendlyDate = "'" . date('M d',$date) . "'";
	
	if ($logsByDay_delete[date('z',$date)] <= 0) {
		$value = 0;
	} else {
		$value = $logsByDay_delete[date('z',$date)];
	}
	
	$graphData_delete[$friendlyDate] = $value;
	$i++;
} while ($i < $totalDays);

$i = 0;
do {
	$date = strtotime("-" . $i . " day");
	$friendlyDate = "'" . date('M d',$date) . "'";
	
	if ($logsByDay_report[date('z',$date)] <= 0) {
		$value = 0;
	} else {
		$value = $logsByDay_report[date('z',$date)];
	}
	
	$graphData_report[$friendlyDate] = $value;
	$i++;
} while ($i < $totalDays);

$graphData_logon = array_reverse($graphData_logon);
$graphData_create = array_reverse($graphData_create);
$graphData_delete = array_reverse($graphData_delete);
$graphData_report = array_reverse($graphData_report);
?>

<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'column'
            },
            title: {
                text: null
            },
            subtitle: {
                text: null
            },
            xAxis: {
                categories: [<?php echo implode(",", array_keys($graphData_logon)); ?>],
                title: {
                    text: null
                },
                dateTimeLabelFormats: { // don't display the dummy year
                    month: '%e. %b',
                    year: '%b'
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Log Events'
                },
               // labels: {
               //     overflow: 'justify'
               // }
                stackLabels: {
                    enabled: false
                }
            },
            tooltip: {
                formatter: function() {
                    return ''+
                        this.series.name +': '+ this.y;
                }
            },
            plotOptions: {
          	  column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: false
                    }
                }
            },
            legend: {
            	enabled: true
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Logon',
                data: [<?php echo implode(",", $graphData_logon);?>]
            }, {
	            name: 'Create',
                data: [<?php echo implode(",", $graphData_create);?>]
            }, {
	            name: 'Delete',
                data: [<?php echo implode(",", $graphData_delete);?>]
            }, {
	            name: 'Report',
                data: [<?php echo implode(",", $graphData_report);?>]
            }]
        });
    });
    
});
</script>

<script>
$("#purgeOldLogsButton").click(function() {
	var r=confirm("Are you sure you want to delete all logs older than 180 days?  This action cannot be undone!");
	
	if (r==true) {
		var thisObject = $(this);
		
		var url = 'modules/logs/actions/purgeOldLogs.php';
		
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
		}, function(data){
			alert("Logs older than 180 have been purged from the database.")
		},'html');
	} else {
	}
	
	return false;
});
</script>