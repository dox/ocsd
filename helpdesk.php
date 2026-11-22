<?php
/**
 * Standalone interview helpdesk exercise.
 * Set the destination email address below before using the page.
 */
declare(strict_types=1);

const HELPDESK_EMAIL_TO = 'andrew.breakspear@seh.ox.ac.uk';
const HELPDESK_STATE_FILE = __DIR__ . '/helpdesk-state.json';

function h(string $value): string
{
	return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function initialTicket(): array
{
	return [
		'ticket' => 'ITTS-2048',
		'subject' => 'Laptop connected to Wi-Fi but cannot access the internet',
		'reporter' => 'Morgan Ellis',
		'reporter_email' => 'morgan.ellis@seh.ox.ac.uk',
		'department' => 'Development & Relations Office',
		'priority' => 'Normal',
		'status' => 'Open',
		'created' => 'Today, 09:18',
		'description' => 'I’ve brought my laptop in from home. I’ve joined the Wi-Fi but cannot access the internet.',
		'notes' => '',
		'resolution' => '',
		'candidate' => '',
		'revision' => 0,
		'updated_at' => '',
	];
}

function readTicket(): array
{
	if (!is_file(HELPDESK_STATE_FILE)) {
		return initialTicket();
	}
	$data = json_decode((string)file_get_contents(HELPDESK_STATE_FILE), true);
	return is_array($data) ? array_merge(initialTicket(), $data) : initialTicket();
}

function saveTicket(array $ticket): bool
{
	$file = @fopen(HELPDESK_STATE_FILE, 'c+');
	if (!$file) return false;
	$ok = false;
	if (flock($file, LOCK_EX)) {
		ftruncate($file, 0);
		rewind($file);
		$ok = fwrite($file, json_encode($ticket, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
		fflush($file);
		flock($file, LOCK_UN);
	}
	fclose($file);
	return $ok;
}

function sendTicketEmail(array $ticket): bool
{
	if (!filter_var(HELPDESK_EMAIL_TO, FILTER_VALIDATE_EMAIL) || !function_exists('mail')) return false;
	$subject = 'Helpdesk interview · ' . $ticket['ticket'] . ' · ' . $ticket['status'];
	$plain = "HELPDESK INTERVIEW UPDATE\n" . str_repeat('=', 30) . "\n\n" .
		"Candidate: " . ($ticket['candidate'] !== '' ? $ticket['candidate'] : 'Not provided') . "\n" .
		"Ticket: {$ticket['ticket']}\nSubject: {$ticket['subject']}\nStatus: {$ticket['status']}\nPriority: {$ticket['priority']}\n\n" .
		"REPORTER\n{$ticket['reporter']} ({$ticket['reporter_email']})\n{$ticket['department']}\n\n" .
		"INTERNAL NOTES — support team only\n" . ($ticket['notes'] !== '' ? $ticket['notes'] : 'No internal notes provided.') . "\n\n" .
		"MESSAGE TO THE USER\n" . ($ticket['resolution'] !== '' ? $ticket['resolution'] : 'No user message provided.') . "\n\n" .
		"Submitted: {$ticket['updated_at']}\n";
	$e = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	$paragraph = static function (string $value) use ($e): string {
		return nl2br($e($value !== '' ? $value : 'No response provided.'), false);
	};
	$statusColour = $ticket['status'] === 'Resolved' ? '#237348' : ($ticket['status'] === 'In progress' ? '#286aa0' : '#94620d');
	$html = '<!doctype html><html><body style="margin:0;padding:24px;background:#f3f6fa;font-family:Arial,Helvetica,sans-serif;color:#243247">' .
		'<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:680px;margin:0 auto;background:#fff;border:1px solid #e0e7ef;border-radius:10px;border-collapse:separate;overflow:hidden">' .
		'<tr><td style="background:#12243a;padding:20px 24px;color:#fff"><div style="font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#b9cadb">St Edmund Hall IT Helpdesk</div><div style="font-size:21px;font-weight:bold;margin-top:6px">Interview ticket update</div></td></tr>' .
		'<tr><td style="padding:22px 24px 10px"><div style="font-size:13px;color:#6d7a8c">Candidate</div><div style="font-size:17px;font-weight:bold;margin-top:4px">' . $e($ticket['candidate'] !== '' ? $ticket['candidate'] : 'Name not provided') . '</div></td></tr>' .
		'<tr><td style="padding:10px 24px 20px"><table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f7f9fc;border-radius:8px"><tr>' .
		'<td style="padding:14px 16px"><div style="font-size:11px;color:#738196;text-transform:uppercase;letter-spacing:.7px">Ticket</div><div style="font-weight:bold;margin-top:5px">' . $e($ticket['ticket']) . '</div></td>' .
		'<td style="padding:14px 16px"><div style="font-size:11px;color:#738196;text-transform:uppercase;letter-spacing:.7px">Status</div><div style="font-weight:bold;color:' . $statusColour . ';margin-top:5px">' . $e($ticket['status']) . '</div></td>' .
		'<td style="padding:14px 16px"><div style="font-size:11px;color:#738196;text-transform:uppercase;letter-spacing:.7px">Priority</div><div style="font-weight:bold;margin-top:5px">' . $e($ticket['priority']) . '</div></td>' .
		'</tr></table></td></tr>' .
		'<tr><td style="padding:0 24px 20px"><div style="font-size:11px;color:#738196;text-transform:uppercase;letter-spacing:.7px;margin-bottom:6px">Subject</div><div style="font-size:16px;font-weight:bold">' . $e($ticket['subject']) . '</div><div style="font-size:13px;color:#586a80;margin-top:8px">Reported by <strong>' . $e($ticket['reporter']) . '</strong> · ' . $e($ticket['reporter_email']) . '<br>' . $e($ticket['department']) . '</div></td></tr>' .
		'<tr><td style="padding:0 24px 18px"><div style="border-top:1px solid #e8edf3;padding-top:17px"><div style="font-size:12px;font-weight:bold;letter-spacing:.6px;text-transform:uppercase;color:#33445b;margin-bottom:9px">Internal notes <span style="font-weight:normal;color:#8a5360">· Support team only</span></div><div style="font-size:14px;line-height:1.65;color:#34445a">' . $paragraph($ticket['notes']) . '</div></div></td></tr>' .
		'<tr><td style="padding:0 24px 22px"><div style="background:#f1f8f8;border-left:3px solid #147f8e;padding:15px 17px;border-radius:4px"><div style="font-size:12px;font-weight:bold;letter-spacing:.6px;text-transform:uppercase;color:#176875;margin-bottom:9px">Message to the user</div><div style="font-size:14px;line-height:1.65;color:#34445a">' . $paragraph($ticket['resolution']) . '</div></div></td></tr>' .
		'<tr><td style="padding:14px 24px;border-top:1px solid #e8edf3;color:#8290a2;font-size:11px">Submitted ' . $e($ticket['updated_at']) . '</td></tr>' .
		'</table></body></html>';
	$boundary = 'helpdesk_' . bin2hex(random_bytes(12));
	$headers = "From: " . HELPDESK_EMAIL_TO . "\r\n" .
		"MIME-Version: 1.0\r\n" .
		"Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
	$body = "--{$boundary}\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n{$plain}\r\n" .
		"--{$boundary}\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n{$html}\r\n" .
		"--{$boundary}--\r\n";
	return mail(HELPDESK_EMAIL_TO, $subject, $body, $headers);
}

session_start();
if (empty($_SESSION['helpdesk_csrf'])) $_SESSION['helpdesk_csrf'] = bin2hex(random_bytes(32));
$message = '';
$messageType = 'success';
$ticket = readTicket();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$token = (string)($_POST['csrf'] ?? '');
	if (!hash_equals($_SESSION['helpdesk_csrf'], $token)) {
		http_response_code(400);
		$message = 'Your session expired. Refresh the page and try again.';
		$messageType = 'danger';
	} else {
		$action = (string)($_POST['action'] ?? 'save');
		if ($action === 'reset') {
			$resetCode = trim((string)($_POST['reset_code'] ?? ''));
			if (strcasecmp($resetCode, 'teddy') !== 0) {
				$message = 'Incorrect reset code. The ticket has not been reset.';
				$messageType = 'danger';
			} else {
				$oldRevision = (int)($ticket['revision'] ?? 0);
				$ticket = initialTicket();
				$ticket['revision'] = $oldRevision + 1;
				if (saveTicket($ticket)) {
					$message = 'Ticket reset. It is ready for the next candidate.';
				} else {
					$message = 'Could not reset the ticket. Check that this folder is writable by PHP.';
					$messageType = 'danger';
				}
			}
		} else {
			$submittedRevision = (int)($_POST['revision'] ?? 0);
			if ($submittedRevision !== (int)($ticket['revision'] ?? 0)) {
				$message = 'The ticket was reset while this page was open. Refresh to start with the current ticket.';
				$messageType = 'warning';
			} else {
			$ticket['notes'] = mb_substr(trim((string)($_POST['notes'] ?? '')), 0, 6000);
			$ticket['resolution'] = mb_substr(trim((string)($_POST['resolution'] ?? '')), 0, 4000);
			$ticket['candidate'] = mb_substr(trim((string)($_POST['candidate'] ?? '')), 0, 120);
			$ticket['revision'] = (int)($ticket['revision'] ?? 0);
			$status = (string)($_POST['status'] ?? 'Open');
			$ticket['status'] = in_array($status, ['Open', 'In progress', 'Resolved'], true) ? $status : 'Open';
			$ticket['updated_at'] = date('Y-m-d H:i:s T');
			if (!saveTicket($ticket)) {
				$message = 'Could not save the ticket. Check that this folder is writable by PHP.';
				$messageType = 'danger';
			} elseif ($action === 'email') {
				if (sendTicketEmail($ticket)) {
					$message = 'Update saved and emailed to the interview team.';
				} else {
					$message = 'Update saved, but the email could not be sent. Check the email address and server mail configuration.';
					$messageType = 'warning';
				}
			} else {
				$message = 'Progress saved.';
			}
			}
		}
	}
}
$ticket = readTicket();
$statusClass = $ticket['status'] === 'Resolved' ? 'resolved' : ($ticket['status'] === 'In progress' ? 'progress' : 'open');
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#12243a">
	<title>Helpdesk | Interview exercise</title>
	<style>
		:root{font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;color:#243247;background:#f3f6fa;font-synthesis:none}
		*{box-sizing:border-box}body{margin:0;background:#f3f6fa}.topbar{height:68px;background:#12243a;color:#fff;display:flex;align-items:center;padding:0 max(24px,calc((100vw - 1160px)/2));gap:13px;box-shadow:0 2px 8px #13243a20}.brand-icon{height:34px;width:34px;border-radius:10px;background:#20a7a0;display:grid;place-items:center;font-weight:800}.brand{font-weight:700;letter-spacing:.01em;color:inherit;text-decoration:none}.brand:hover,.brand:focus{color:#d9f2f3;text-decoration:none}.top-tag{margin-left:auto;color:#c8d3e1;font-size:13px}.layout{max-width:1160px;margin:36px auto;padding:0 24px;display:grid;grid-template-columns:minmax(0,1fr) 300px;gap:24px}.eyebrow{text-transform:uppercase;letter-spacing:.12em;color:#66758a;font-weight:700;font-size:11px;margin:0 0 8px}.heading{margin:0 0 6px;font-size:27px;color:#182a40}.intro{color:#66758a;margin:0 0 23px}.card{background:#fff;border:1px solid #e0e7ef;border-radius:12px;box-shadow:0 4px 14px #20355008;overflow:hidden}.ticket-head{padding:22px 24px;border-bottom:1px solid #e8edf3;display:flex;justify-content:space-between;gap:18px;align-items:flex-start}.ticket-id{font-size:13px;color:#6d7a8c;font-weight:650}.ticket-title{font-size:20px;margin:5px 0 0;color:#203149}.badge{display:inline-flex;align-items:center;gap:7px;border-radius:99px;padding:7px 11px;font-size:12px;font-weight:700;white-space:nowrap}.badge:before{content:"";width:7px;height:7px;border-radius:50%;background:currentColor}.open{color:#9a6611;background:#fff4dc}.progress{color:#286aa0;background:#eaf4ff}.resolved{color:#247647;background:#e8f7ed}.reporter{padding:18px 24px;display:flex;flex-wrap:wrap;gap:10px 28px;border-bottom:1px solid #e8edf3;color:#637187;font-size:13px}.reporter strong{color:#34445a;font-weight:650}.section{padding:22px 24px}.section-label{font-size:12px;color:#66758a;text-transform:uppercase;letter-spacing:.08em;font-weight:750;margin:0 0 11px}.description{line-height:1.65;color:#34445a;margin:0}.form-area{border-top:1px solid #e8edf3;background:#fbfcfe;padding:22px 24px}.field{margin-bottom:19px}label{display:block;font-size:13px;font-weight:700;color:#33445b;margin:0 0 7px}.hint{font-weight:400;color:#8390a1;margin-left:5px}textarea,select,.form-control{font:inherit;width:100%;height:44px;line-height:20px;border:1px solid #d7e0ea;border-radius:8px;background:#fff;color:#25354b;padding:11px 12px;outline:none}textarea{height:auto;resize:vertical;min-height:123px;line-height:1.5}#resolution{min-height:86px}textarea:focus,select:focus,.form-control:focus{border-color:#258caa;box-shadow:0 0 0 3px #258caa22}.form-row{display:grid;grid-template-columns:1fr 1fr;gap:18px}.actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap}.button{border:0;border-radius:8px;padding:11px 16px;font:inherit;font-weight:700;cursor:pointer}.primary{background:#147f8e;color:#fff}.primary:hover{background:#106c79}.secondary{background:#eaf0f6;color:#34465e}.clear{background:transparent;color:#a33f45;border:1px solid #e4c6c9}.save-state{font-size:12px;color:#728094;margin-left:auto}.sidebar{display:grid;gap:16px;align-content:start}.side-card{padding:19px}.side-title{margin:0 0 14px;font-size:14px;color:#24364e}.side-copy{color:#6b788c;font-size:13px;line-height:1.55;margin:0}.steps{display:grid;gap:13px}.step{display:flex;gap:11px;color:#596a80;font-size:13px;line-height:1.45}.num{width:23px;height:23px;flex:none;border-radius:50%;display:grid;place-items:center;background:#e7f2f4;color:#147f8e;font-weight:800;font-size:11px}.notice{padding:13px 15px;border-radius:9px;font-size:13px;margin-bottom:18px}.notice.success{background:#e8f7ed;color:#286b43}.notice.warning{background:#fff5df;color:#795816}.notice.danger{background:#fdebec;color:#8b2f36}.autosave{display:flex;gap:8px;align-items:center;color:#6f7e91;font-size:12px;margin-top:12px}.dot{width:7px;height:7px;border-radius:50%;background:#33a06f}.dot.saving{background:#d59a31}.footer-note{text-align:center;color:#8793a3;font-size:12px;margin:22px 0 0}
		@media(max-width:800px){.layout{grid-template-columns:1fr;margin-top:25px}.sidebar{grid-template-columns:1fr 1fr}.top-tag{display:none}}@media(max-width:560px){.layout{padding:0 14px}.ticket-head,.section,.reporter,.form-area{padding-left:17px;padding-right:17px}.ticket-head{align-items:flex-start}.ticket-title{font-size:17px}.form-row,.sidebar{grid-template-columns:1fr}.save-state{width:100%;margin:2px 0 0}}
	</style>
</head>
<body>
	<header class="topbar"><div class="brand-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16"><path d="M8.05 9.6c.336.24.504-.24.554-.627.04-.534.198-.815.847-1.26.673-.475 1.049-1.09 1.049-1.986 0-1.325-.92-2.227-2.262-2.227-1.02 0-1.792.492-2.1 1.29A1.7 1.7 0 0 0 6 5.48c0 .393.203.64.545.64.272 0 .455-.147.564-.51.158-.592.525-.915 1.074-.915.61 0 1.03.446 1.03 1.084 0 .563-.208.885-.822 1.325-.619.433-.926.914-.926 1.64v.111c0 .428.208.745.585.745"></path>
		<path d="m10.273 2.513-.921-.944.715-.698.622.637.89-.011a2.89 2.89 0 0 1 2.924 2.924l-.01.89.636.622a2.89 2.89 0 0 1 0 4.134l-.637.622.011.89a2.89 2.89 0 0 1-2.924 2.924l-.89-.01-.622.636a2.89 2.89 0 0 1-4.134 0l-.622-.637-.89.011a2.89 2.89 0 0 1-2.924-2.924l.01-.89-.636-.622a2.89 2.89 0 0 1 0-4.134l.637-.622-.011-.89a2.89 2.89 0 0 1 2.924-2.924l.89.01.622-.636a2.89 2.89 0 0 1 4.134 0l-.715.698a1.89 1.89 0 0 0-2.704 0l-.92.944-1.32-.016a1.89 1.89 0 0 0-1.911 1.912l.016 1.318-.944.921a1.89 1.89 0 0 0 0 2.704l.944.92-.016 1.32a1.89 1.89 0 0 0 1.912 1.911l1.318-.016.921.944a1.89 1.89 0 0 0 2.704 0l.92-.944 1.32.016a1.89 1.89 0 0 0 1.911-1.912l-.016-1.318.944-.921a1.89 1.89 0 0 0 0-2.704l-.944-.92.016-1.32a1.89 1.89 0 0 0-1.912-1.911z"></path>
		<path d="M7.001 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0"></path></svg></div><a class="brand" href="helpdesk.php" aria-label="St Edmund Hall Helpdesk home">St Edmund Hall Helpdesk</a><div class="top-tag">Candidate support exercise</div></header>
	<main class="layout">
		<div>
			<p class="eyebrow">Technical test</p><h1 class="heading">Resolve this support ticket</h1><p class="intro">Please work as you normally would in a real support situation. Investigate the report, resolve the problem if you can, and confirm whether it is fixed.</p>
			<?php if ($message !== ''): ?><div class="notice <?= h($messageType) ?>" role="status"><?= h($message) ?></div><?php endif; ?>
			<article class="card">
				<div class="ticket-head"><div><div class="ticket-id">Ticket <?= h($ticket['ticket']) ?> <span aria-hidden="true">·</span> Created <?= h($ticket['created']) ?></div><h2 class="ticket-title"><?= h($ticket['subject']) ?></h2></div><span class="badge <?= h($statusClass) ?>"><?= h($ticket['status']) ?></span></div>
				<div class="reporter"><span>Reported by <strong><?= h($ticket['reporter']) ?></strong></span><span><?= h($ticket['reporter_email']) ?></span><span><?= h($ticket['department']) ?></span><span>Priority <strong><?= h($ticket['priority']) ?></strong></span></div>
				<div class="section"><p class="section-label">Problem description</p><p class="description">“<?= h($ticket['description']) ?>”</p></div>
				<form class="form-area" method="post" id="ticket-form">
					<input type="hidden" name="csrf" value="<?= h($_SESSION['helpdesk_csrf']) ?>">
					<input type="hidden" name="revision" value="<?= (int)$ticket['revision'] ?>">
					<div class="field"><label for="notes">Internal notes <span class="hint">For the support team only · What did you do?</span></label><textarea id="notes" name="notes" maxlength="6000" placeholder="Record your diagnostic steps, what you found, and what you did to resolve the problem. These notes are internal and will not be sent to the user…"><?= h($ticket['notes']) ?></textarea></div>
					<div class="field"><label for="resolution">Message to the user <span class="hint">Customer-facing · Explain the outcome and confirm the fix</span></label><textarea id="resolution" name="resolution" maxlength="4000" placeholder="Write the message you would send to the person who reported the issue…"><?= h($ticket['resolution']) ?></textarea></div>
					<div class="form-row"><div class="field"><label for="status">Ticket status</label><select id="status" name="status"><option <?= $ticket['status']==='Open'?'selected':'' ?>>Open</option><option <?= $ticket['status']==='In progress'?'selected':'' ?>>In progress</option><option <?= $ticket['status']==='Resolved'?'selected':'' ?>>Resolved</option></select></div><div class="field"><label for="candidate">Candidate name <span class="hint">optional, for the email</span></label><input class="form-control" id="candidate" name="candidate" type="text" maxlength="120" placeholder="Your name" value="<?= h($ticket['candidate']) ?>"></div></div>
					<div class="actions"><button class="button primary" type="submit" name="action" value="email">Save update &amp; send email</button><button class="button secondary" type="submit" name="action" value="save">Save progress</button><button class="button clear" type="submit" name="action" value="reset" formnovalidate onclick="const code = prompt('Enter the reset code to clear this ticket for the next candidate:'); if (code === null) return false; const input = document.getElementById('reset-code'); input.value = code; return confirm('Clear the saved ticket and reset it for the next candidate?')">Reset for next candidate</button><input type="hidden" id="reset-code" name="reset_code" value=""><span class="save-state" id="save-state">All changes saved</span></div>
					<div class="autosave"><span class="dot" id="save-dot"></span><span id="save-copy">Your work is saved automatically as you type.</span></div>
					<p class="side-copy" style="margin-top:16px">Be prepared to explain what the issue was, how you diagnosed it, and what you did to resolve it. There is no need to rush; we are assessing your method and reasoning, not just speed. If you are unsure, explain what you would check next. When you believe it is resolved, please let us know.</p>
				</form>
			</article>
			<p class="footer-note">Interview exercise · 2026 · Developed by Andrew Breakspear.</p>
		</div>
		<aside class="sidebar">
			<section class="card side-card"><h2 class="side-title">Your task</h2><div class="steps"><div class="step"><span class="num">1</span><span>Investigate the report and identify the cause of the internet access problem.</span></div><div class="step"><span class="num">2</span><span>Resolve the problem if you can, then confirm whether internet access is working.</span></div><div class="step"><span class="num">3</span><span>Add internal notes describing your checks and actions, then write a separate message to the user explaining the outcome.</span></div><div class="step"><span class="num">4</span><span>Choose the appropriate ticket status and click <strong>Save update &amp; send email</strong> to submit your work.</span></div></div></section>
			<section class="card side-card"><h2 class="side-title">What we’re interested in</h2><p class="side-copy">Be ready to explain what the issue was, the steps you took to diagnose it, and what you did to resolve it.<p>
				<p class="side-copy">We’re interested in your method and reasoning, not just speed.</p></section>
			<section class="card side-card"><h2 class="side-title">Autosave is on</h2><p class="side-copy">Your notes and status are saved as you type.<p>
				<p class="side-copy">You can close this page and return later on this device or another one.</p></section>
		</aside>
	</main>
	<script>
	(() => {
		const form = document.getElementById('ticket-form');
		const state = document.getElementById('save-state');
		const dot = document.getElementById('save-dot');
		const copy = document.getElementById('save-copy');
		const fields = ['notes', 'resolution', 'status', 'candidate'];
		let timer;
		let lastSaved = JSON.stringify(Object.fromEntries(fields.map(id => [id, document.getElementById(id).value])));
		async function saveDraft() {
			const values = Object.fromEntries(fields.map(id => [id, document.getElementById(id).value]));
			const signature = JSON.stringify(values);
			if (signature === lastSaved) return;
			state.textContent = 'Saving…'; dot.classList.add('saving'); copy.textContent = 'Saving your latest changes…';
			const body = new FormData(form); body.set('action', 'save');
			try {
				const response = await fetch(location.href, {method:'POST', body, headers:{'X-Requested-With':'fetch'}});
				if (!response.ok) throw new Error('save failed');
				lastSaved = signature; state.textContent = 'All changes saved'; copy.textContent = 'Your work is saved automatically as you type.';
			} catch (error) { state.textContent = 'Save issue'; copy.textContent = 'Could not save right now. Your text remains on this page; try Save progress.'; }
			dot.classList.remove('saving');
		}
		fields.forEach(id => document.getElementById(id).addEventListener(id === 'status' ? 'change' : 'input', () => {state.textContent='Unsaved changes';copy.textContent='Saving your latest changes…';clearTimeout(timer);timer=setTimeout(saveDraft, 500);}));
		form.addEventListener('submit', event => { if (event.submitter && event.submitter.value === 'reset') clearTimeout(timer); });
	})();
	</script>
</body>
</html>
