<?php
$emailAllowedUsers[] = "BREAKSPEAR";
$emailAllowedUsers[] = "TREHEARNE";
$emailAllowedUsers[] = "PARFITT";
$emailAllowedUsers[] = "ESTALL";
$emailAllowedUsers[] = "WILLIS";
$emailAllowedUsers[] = "BROOKS";

if (!in_array(strtoupper($_SESSION["username"]), $emailAllowedUsers) ) {
	$logInsert = (new Logs)->insert("view","error",null,"Emergency email system access attempted");
	echo "<br /><div class=\"alert alert-danger\" role=\"alert\">You do not have permission to use this feature.  Contact <a href=\"mailto:help@seh.ox.ac.uk\">help@seh.ox.ac.uk</a></div>";
	die;
}
?>

<?php
if (isset($_POST['emailRecipients']) && isset($_POST['emailSubject']) && isset($_POST['emailMessage'])) {
	$recipientsArray = explode("\n", $_POST['emailRecipients']);
	foreach ($recipientsArray AS $recipient) {
		$recipients[] = str_replace(' ', '', $recipient);
	}
	
	$message = htmlspecialchars($_POST['emailMessage']);
	$message = preg_replace('/\n/', '<br />', $message);
	$message = mb_convert_encoding($message, "HTML-ENTITIES", 'UTF-8');
	
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';
	$headers[] = 'To: Mary <mary@example.com>, Kelly <kelly@example.com>';
	$headers[] = 'From: Birthday Reminder <' . $_POST['emailSender'] . '>';
	
	sendMail($_POST['emailSubject'], $recipients, $message, $_POST['emailSender'], $_POST['emailSenderName']);
	//mail(implode(",", $recipients), $_POST['emailSubject'], $message, implode("\r\n", $headers));
	
	$logInsert = (new Logs)->insert("email","success",null,"Email sent to <code>" . $_POST['emailRecipients'] . "</code>");
	
	echo "<div class=\"alert alert-primary\" role=\"alert\">E-Mail successfully sent to " . count($recipients) . " addresses</div>";
}

?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2"><i class="fas fa-envelope"></i> Emergency Email</h1>
	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="btn-group mr-2">
			<button type="button" class="btn btn-sm btn-outline-secondary">void</button>
			<button type="button" class="btn btn-sm btn-outline-secondary">void</button>
		</div>
		
		<button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"><span data-feather="calendar"></span>void</button>
	</div>
</div>

<div class="alert alert-danger" role="alert">
	<strong>WARNING!</strong> clicking 'Send E-Mail' will <u>immediately</u> send your email to all entered addresses
</div>
	
	<form role="form" id="sendGroupEmail" target="_self" method="post">
		<div class="form-group">
			<label for="emailSender">From Address</label>
			<input type="text" class="form-control" name="emailSender" placeholder="E-Mail Sender" value="covid19@seh.ox.ac.uk">
		</div>
		<div class="form-group">
			<label for="emailSenderName">From Name</label>
			<input type="text" class="form-control" name="emailSenderName" placeholder="E-Mail Sender Name" value="SEH COVID-19 UPDATE">
		</div>
		<div class="form-group">
			<label for="emailSubject">Subject</label>
			<input type="text" class="form-control" name="emailSubject" placeholder="E-Mail Subject" value="Test">
		</div>
		<div class="form-group">
			<label for="emailMessage">Message</label>
			<textarea class="form-control" rows="6" name="emailMessage" placeholder="E-Mail Message">Test</textarea>
		</div>
		<div class="form-group">
			<label for="emailRecipients">Recipients (one per line)</label>
			<textarea class="form-control" rows="6" name="emailRecipients" placeholder="E-Mail Recipients (one per line)">andrew.breakspear@seh.ox.ac.uk</textarea>
		</div>
		<div class="form-group">
			<input type="submit" class="btn btn-primary btn-lg btn-block" value="Send E-Mail">
		</div>
	</form>
</div>