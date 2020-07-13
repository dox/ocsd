<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

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

$templatesClass = new Templates();
$templatesAll = $templatesClass->all();

$logsClass = new Logs();
$ageLimitDays = 7;
$logs = $logsClass->allByType("email", $ageLimitDays);

?>

<?php
if (isset($_POST['emailRecipients']) && isset($_POST['emailSubject']) && isset($_POST['emailMessage'])) {
	$recipientsArray = explode("\n", $_POST['emailRecipients']);
	foreach ($recipientsArray AS $recipient) {
		$recipients[] = str_replace(' ', '', $recipient);
	}

	//$message = htmlspecialchars($_POST['emailMessage']);
	//$message = preg_replace('/\n/', '<br />', $message);
	//$message = mb_convert_encoding($message, "HTML-ENTITIES", 'UTF-8');

	sendMail($_POST['emailSubject'], $recipients, $_POST['emailMessage'], $_POST['emailSender'], $_POST['emailSenderName']);
	//mail(implode(",", $recipients), $_POST['emailSubject'], $message, implode("\r\n", $headers));

	$logInsert = (new Logs)->insert("email","success",null,"Email sent to <code>" . $_POST['emailRecipients'] . "</code>");

	echo "<div class=\"alert alert-primary\" role=\"alert\">E-Mail successfully sent to " . count($recipients) . " addresses</div>";
}

?>
<div class="row">
	<div class="col-md-3">
		<h3 class="mb-4">OCSD Email System</h3>
		<div>
			<div class="list-group list-group-transparent mb-0">
				<a href="index.php?n=emergency_email&tab=compose" class="list-group-item list-group-item-action d-flex align-items-center <?php if ($_GET['tab'] == "compose" || !isset($_GET['tab'])) { echo "active"; } ?>">
					<span class="icon mr-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"></path><path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"></path><line x1="16" y1="5" x2="19" y2="8"></line></svg>
					</span>Compose
				</a>
				<hr />
				<a href="index.php?n=emergency_email&tab=sent" class="list-group-item list-group-item-action d-flex align-items-center <?php if ($_GET['tab'] == "sent") { echo "active"; } ?>">
					<span class="icon mr-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="10" y1="14" x2="21" y2="3"></line><path d="M21 3L14.5 21a.55 .55 0 0 1 -1 0L10 14L3 10.5a.55 .55 0 0 1 0 -1L21 3"></path></svg>
					</span>Sent Mail <span class="ml-auto badge bg-grey"><?php echo count($logs); ?></span>
				</a>
				<a href="index.php?n=emergency_email&tab=drafts" class="list-group-item list-group-item-action d-flex align-items-center <?php if ($_GET['tab'] == "drafts") { echo "active"; } ?>">
					<span class="icon mr-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><polyline points="14 3 14 8 19 8"></polyline><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path></svg>
					</span>Drafts
				</a>
				<a href="index.php?n=emergency_email&tab=templates" class="list-group-item list-group-item-action d-flex align-items-center <?php if ($_GET['tab'] == "templates") { echo "active"; } ?>">
					<span class="icon mr-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><polyline points="14 3 14 8 19 8"></polyline><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path><line x1="9" y1="9" x2="10" y2="9"></line><line x1="9" y1="13" x2="15" y2="13"></line><line x1="9" y1="17" x2="15" y2="17"></line></svg>
					</span>Templates <span class="ml-auto badge bg-grey"><?php echo count($templatesAll); ?></span>
				</a>
				<a href="index.php?n=emergency_email&tab=trash" class="list-group-item list-group-item-action d-flex align-items-center <?php if ($_GET['tab'] == "trash") { echo "active"; } ?>">
					<span class="icon mr-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="4" y1="7" x2="20" y2="7"></line><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path></svg>
					</span>Trash
				</a>
			</div>
		</div>
	</div>
	<div class="col-md-9">
		<?php
		if (isset($_GET['tab'])) {
			$tab = $_GET['tab'];
		} else {
			$tab = "compose";
		}

		include("email_unique_tabs/" . $tab . ".php"); ?>


	</div>
</div>

<script>
$(document).ready(function() {
  $('.summernote').summernote();


});
</script>
