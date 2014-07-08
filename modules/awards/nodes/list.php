<?php
$awards = Awards::find_all();
?>
<script src="js/jquery.fastLiveFilter.js"></script>

<div class="page-header">
	<h1>Awards <small></small></h1>
</div>

<h2>Awards List</h2>
<table class="table table-striped">
	<thead>
		<tr>
			<th width="50%">Name</th>
			<th width="20%">Type</th>
			<th width="20%">Given By</th>
			<th width="10%"></th>
		</tr>
	</thead>
	<tbody id="awards_search_list">
	<tr>
		<td><a href="#" class="myeditable" id="new_award_name" data-type="text" data-name="new_award_name" data-original-title="Enter Award Name"></a></td>
		<td><a href="#" class="myeditable" id="new_award_type" data-type="select" data-name="new_award_type" data-original-title="Select Award Type"></a></td>
		<td><a href="#" class="myeditable" id="new_award_given_by" data-type="select" data-name="new_award_given_by" data-original-title="Select Award Given By"></a></td>
		<td><button class="btn btn-primary btn-sm" id="new_award_save">Add Award</button></td>
	</tr>
	</tbody>
</table>

<div class="row">
	<div class="col-xs-3">
		<input type="text" class="form-control search-query" id="awards_search_input" placeholder="Quick Filter">
	</div>
</div>
<table class="table table-striped">
	<thead>
		<tr>
			<th width="50%">Name</th>
			<th width="20%">Type</th>
			<th width="20%">Given By</th>
			<th width="10%"></th>
		</tr>
	</thead>
	<tbody id="awards_search_list">
	<?php
	foreach ($awards AS $award) {
		$output  = "<tr>";
		$output .= "<td>" . $award->name . "</td>";
		
		$output .= "<td>" . $award->type . "</td>";
		
		$output .= "<td>" . $award->given_by ."</td>";
		
		$output .= "<td>";
		$output .= "<div class=\"btn-group\">";
		$output .= "<button type=\"button\" class=\"btn btn-default btn-xs dropdown-toggle\" data-toggle=\"dropdown\">";
		$output .= "Action <span class=\"caret\"></span>";
		$output .= "</button>";
		$output .= "<ul class=\"dropdown-menu\" role=\"menu\">";
		//$output .= "<li><a href=\"#\">Edit</a></li>";
		//$output .= "<li><a href=\"#\">Recipients</a></li>";
		$output .= "<li class=\"divider\"></li>";
		$output .= "<li><a id=\"" . $award->awdid . "\" class=\"delete_award\" href=\"#\">Delete</a></li>";
		$output .= "</ul>";
		$output .= "</div>";
		$output .= "</td>";
		
		$output .= "</tr>";
		
		echo $output;
	}
	?>
	</tbody>
</table>

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

<style>
table.table tr td div.btn-group {
	display:none;
}
table.table tr:hover td div.btn-group {
	display:inline-block;
}
</style>

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

$('.delete_award').click(function() {
	var r=confirm("Are you sure you want to delete this award?  This cannot be undone.");
	
	if (r==true) {
		var thisObject = $(this);
		var awdid = $(thisObject).attr('id');
		
		var url = 'modules/awards/actions/deleteAwardType.php';
		
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
		    awdid: awdid
		}, function(data){
			$(thisObject).parent().parent().parent().parent().parent().fadeOut();
		},'html');
	} else {
	}
	
	return false;
});
</script>