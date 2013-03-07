$(function() {
	$("#MainTabs").tab();
	
	$("#MainTabs").bind("show", function(e) {    
		var contentID  = $(e.target).attr("data-target");
		var contentURL = $(e.target).attr("href");
		
		if (typeof(contentURL) != 'undefined')
			$(contentID).load(contentURL, function(){ $("#MainTabs").tab(); });
		else
			$(contentID).tab('show');
	});
	
	$('#MainTabs a:first').tab("show");
});
