<?php
$awards = Awards::find_all();
?>
<script src="js/jquery.fastLiveFilter.js"></script>
<script src="http://code.highcharts.com/highcharts.js"></script>

<div class="row">
	<div class="span12">
		<div class="page-header">
			<h1>Awards <small></small></h1>
		</div>
	</div>
</div>
<div class="row">
	<div class="span3">
		<h2>Add New</h2>
		
		<table id="user" class="table table-bordered table-striped">
			<tbody>
				<tr>
					<td>Name</td>
					<td><a href="#" class="myeditable" id="new_award_name" data-type="text" data-name="new_award_name" data-original-title="Enter Award Name"></a></td>
				</tr>
				<tr>
					<td>Type</td>
					<td><a href="#" class="myeditable" id="new_award_type" data-type="select" data-name="new_award_type" data-original-title="Select Award Type"></a></td>
				</tr>
				<tr>
					<td>Given By</td>
					<td><a href="#" class="myeditable" id="new_award_given_by" data-type="select" data-name="new_award_given_by" data-original-title="Select Award Given By"></a></td>
				</tr>
			</tbody>
		</table>
		
		<div class="alert hidden" id="msg"></div>
		
		<button class="btn btn-primary" id="new_award_save">Add Award</button>
		<button id="reset-btn" class="btn pull-right">Reset</button>
	</div>
	<div class="row">
	<div class="span9">
		<h2>Awards List</h2>
		<input type="text" class="input-medium search-query" id="awards_search_input" placeholder="Quick Filter">
		
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Name</th>
					<th>Type</th>
					<th>Given By</th>
				</tr>
			</thead>
			<tbody id="awards_search_list">
			<?php
			foreach ($awards AS $award) {
				$output  = "<tr>";
				$output .= "<td>" . $award->name . "</td>";
				
				$output .= "<td>" . $award->type . "</td>";
				
				$output .= "<td>" . $award->given_by ."</td>";
				
				$output .= "</tr>";
				
				echo $output;
			}
			?>
			</tbody>
		</table>
	</div>
</div>
<script>
$(function() {
	$('#awards_search_input').fastLiveFilter('#awards_search_list');
});
</script>

<?php
	foreach ($awards AS $award) {
		$awardTypes[$award->type] = "{value: '" . $award->type . "', text: '" . $award->type . "'}";
		$awardGivenBy[$award->given_by] = "{value: '" . $award->given_by . "', text: '" . $award->given_by . "'}";
	}
	
?>
<script>
$('#new_award_name').editable();

$('#new_award_type').editable({
	source: [<?php echo implode(",", $awardTypes) ?>]
});

$('#new_award_given_by').editable({
	source: [<?php echo implode(",", $awardGivenBy) ?>]
});

$('#new_award_save').click(function() {
	$('.myeditable').editable('submit', { 
		url: 'modules/awards/actions/addAward.php', 
		ajaxOptions: {
			dataType: 'json' //assuming json response
		},           
		success: function(data, config) {
			if(data && data.id) {  //record created, response like {"id": 2}
				$("#awards_search_list").prepend("<tr class=\"success\"><td>" + data.name + "</td><td>" + data.type + "</td><td>" + data.given_by + "</td></tr>");
			} else if(data && data.errors){
				config.error.call(this, data.errors);
			}
		},
		error: function(errors) {
			if(data && data.id) {  //record created, response like {"id": 2}
				$('#msg').addClass('alert-error').removeClass('alert-success hidden').html("Error when adding Award").show();
			}
		}
	});
});

$('#reset-btn').click(function() {
	$('#new_award_name').editable('setValue', "").editable('option', 'pk', null).removeClass('editable-unsaved');
	$('#new_award_type').editable('setValue', "").editable('option', 'pk', null).removeClass('editable-unsaved');
	
	$('#msg').hide();                
});
</script>