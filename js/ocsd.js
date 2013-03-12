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

$(function() {
	$("#submitFormButton").click(function() {
		// validate and process form here
		var subject = "Message From OCSD";
		var recipient = $("input#inputEmail").val();
		var message = $("textarea#textareaMessage").val();
		
		if (recipient == "") {
			alert("Please enter a valid e-mail address.");
			return false;
		}
		
		var url = 'actions/sendMail.php';
		
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
			subject: subject,
			recipient: recipient,
			message: message
		}, function(data){
			//$("#response_added").append(data);
			alert("Message sent");
		},'html');
		
		return false;
	});
});

$(function() {
	$(".label").tooltip({
		'selector': '',
		'placement': 'top'
	});
});
