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

$(".ldap_disable_user").click(function() {
	$(this).parent().dropdown('toggle');

	var samaccountname = $(this).attr('id');
	var url = 'actions/ldap_disable_user.php';

	$.post(url,{
		samaccountname: samaccountname
	}, function(data){
		alert("Disable User: " + samaccountname);
	},'html');

	return false;
});

$(".ldap_enable_user").click(function() {
	$(this).parent().dropdown('toggle');

	var samaccountname = $(this).attr('id');
	var url = 'actions/ldap_enable_user.php';

	$.post(url,{
		samaccountname: samaccountname
	}, function(data){
		alert("Enable User: " + samaccountname);
	},'html');

	return false;
});

$(".ldap_provision_user").click(function() {
	$(this).parent().parent().parent().parent().fadeOut('slow');

	var cudid = $(this).attr('id');
	var url = 'actions/ldap_provision_user.php';

	if ($(this).hasClass("provision_with_email")) {
		var email = 'true';
	} else {
		var email = 'false';
	}

	$.post(url,{
		cudid: cudid,
		email: email
	}, function(data){
		alert("LDAP Result: " + data);
	},'html');

	return false;
});

});
