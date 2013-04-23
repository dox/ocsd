<div class="row">
	<div class="span12">
		<div class="page-header">
			<h1>Contact <small> please submit your query</small></h1>
		</div>
	</div>
	<div class="span12">
		<form class="form-horizontal">
		<div class="control-group">
			<label class="control-label" for="inputEmail">Your E-Mail</label>
			<div class="controls">
				<input type="text" id="inputEmail" placeholder="E-Mail">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="inputStudent">Reference Student</label>
			<div class="controls">
				<?php
				if (isset($_GET['studentName'])) {
					$value = $_GET['studentName'];
				} else {
					$value = "";
				}
				?>
				<input type="text" id="inputStudent" placeholder="Full Student Name" value="<?php echo $value; ?>">
			</div>
		</div>
		<?php
		if (isset($_GET['studentID'])) {
		?>
		<div class="control-group">
			<label class="control-label" for="inputStudentID">Reference Student ID</label>
			<div class="controls">
				<input type="text" id="inputStudentID" placeholder="Student ID" value="<?php echo $_GET['studentID']; ?>">
			</div>
		</div>
		<?php
		}
		?>
		<div class="control-group">
			<label class="control-label" for="textareaMessage">Query/Problem</label>
			<div class="controls">
				<textarea rows="3" id="textareaMessage" autofocus></textarea>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<button type="button" class="btn btn-primary" id="submitFormButton">Submit</button>
			</div>
		</div>
		</form>
	</div>
</div>

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
		},'html');
		
		return false;
	});
});
</script>