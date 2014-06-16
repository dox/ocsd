<div class="page-header">
	<h1>Contact <small> please submit your query</small></h1>
</div>

<form class="form-horizontal" id="contactForm" role="form">
<div class="form-group">
	<label class="col-sm-2 control-label" for="inputEmail">Your E-Mail</label>
	<div class="col-sm-10">
		<input type="email" class="form-control" id="inputEmail" placeholder="E-Mail">
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label" for="inputStudent">Reference Student</label>
	<div class="col-sm-10">
		<?php
		if (isset($_GET['studentName'])) {
			$value = $_GET['studentName'];
		} else {
			$value = "";
		}
		?>
		<input type="text" class="form-control" id="inputStudent" placeholder="Full Student Name" value="<?php echo $value; ?>">
	</div>
</div>

<?php
if (isset($_GET['studentID'])) {
?>
<div class="form-group">
		<label class="col-sm-2 control-label" for="inputStudentID">Reference Student ID</label>
		<div class="col-sm-10">
			<input type="number" class="form-control" id="inputStudentID" placeholder="Student ID" value="<?php echo $_GET['studentID']; ?>">
		</div>
	</div>
<?php
}
?>
<div class="form-group">
	<label class="col-sm-2 control-label" for="textareaMessage">Query/Problem</label>
	<div class="col-sm-10">
		<textarea class="form-control" rows="3" id="textareaMessage" autofocus></textarea>
	</div>
</div>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
		<button type="button" class="btn btn-primary" id="submitFormButton">Submit</button>
	</div>
</div>
</form>

<script>
$(function() {
	$("#submitFormButton").click(function() {
		// validate and process form here
		var subject = "Message From OCSD";
		var recipient = "andrew.breakspear@seh.ox.ac.uk";
		var studentName = $("input#inputStudent").val();
		var studentID = $("input#inputStudentID").val();
		var message = $("textarea#textareaMessage").val();
		
		var messageBody = "Student: " + studentName + " (" + studentID + ")" + " - " + message;
		var url = 'actions/sendMail.php';
		
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
			subject: subject,
			recipient: recipient,
			message: messageBody
		}, function(data){
			//$("#response_added").append(data);
			alert("Message sent");
			$('#contactForm')[0].reset();
		},'html');
		
		return false;
	});
});
</script>