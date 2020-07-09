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
<div class="row">
	<div class="col-md-3">
		<h3 class="mb-4">Emergency Mail Service</h3>
		<div>
			<div class="list-group list-group-transparent mb-0">
				<a href="#" class="list-group-item list-group-item-action d-flex align-items-center active">
					<span class="icon mr-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><rect x="4" y="4" width="16" height="16" rx="2"></rect><path d="M4 13h3l3 3h4l3 -3h3"></path></svg>
					</span>Inbox <span class="ml-auto badge bg-blue">14</span>
				</a>
				<a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
					<span class="icon mr-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="10" y1="14" x2="21" y2="3"></line><path d="M21 3L14.5 21a.55 .55 0 0 1 -1 0L10 14L3 10.5a.55 .55 0 0 1 0 -1L21 3"></path></svg>
					</span>Sent Mail
				</a>
				<a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
					<span class="icon mr-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><polyline points="14 3 14 8 19 8"></polyline><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path></svg>
					</span>Drafts
				</a>
				<a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
					<span class="icon mr-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="4" y1="7" x2="20" y2="7"></line><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path></svg>
					</span>Trash
				</a>
			</div>
		</div>
	</div>
	<div class="col-md-9">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Compose new message</h3>
			</div>
			<div class="card-body">
				<form id="sendGroupEmail" target="_self" method="post">
					<div class="mb-2">
						<div class="row align-items-center">
							<label class="col-sm-2">To:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="emailRecipients">
							</div>
						</div>
					</div>
					<div class="mb-2">
						<div class="row align-items-center">
							<label class="col-sm-2">Subject:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="emailSubject">
							</div>
						</div>
					</div>
					<div class="mb-2">
						<div class="row align-items-center">
							<label class="col-sm-2">From:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="emailSender" value="covid19@seh.ox.ac.uk">
							</div>
						</div>
					</div>
					<div class="mb-2">
						<div class="row align-items-center">
							<label class="col-sm-2">From Name:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="emailSenderName" value="SEH COVID-19 UPDATE">
							</div>
						</div>
					</div>
					<textarea rows="10" class="form-control" name="emailMessage"></textarea>
					<div class="btn-list mt-4 text-right">
						<button type="button" class="btn btn-white btn-space">Cancel</button>
						<button type="submit" class="btn btn-primary btn-space">Send message</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
