$( document ).ready(function() {

$(".emailParcelButton1").click(function() {
	$(this).parent().dropdown('toggle');
		
		var cudid = $(this).attr('id');
		
		var url = 'actions/sendEmail.php';
		
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
		    cudid: cudid
		}, function(data){
		    alert("Email sent to cudid:" + cudid);
		},'html');
	
	return false;
});


});