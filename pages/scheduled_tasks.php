<?php
$data = array(
		'icon'		=> 'list-task',
		'title'		=> 'Scheduled Tasks',
		'subtitle'	=> 'These tasks are normally triggered via cron'
);
echo pageTitle($data);

$tasks = array(
	'test' => [
		'name' => 'Test',
		'description' => 'This does nothing - it is purely for testing purposes',
		'url' => './cron/test.php',
		'last_run' => '2024-05-01 12:23:34'
	],
	'ldap_sync' => [
		'name' => 'LDAP Sync',
		'description' => 'Updates fields in matching LDAP/CUD records from the current values in CUD',
		'url' => './cron/ldap_sync.php',
		'last_run' => '2024-05-01 12:23:34'
	],
	'ldap_passwords' => [
		'name' => 'LDAP Passwords Reminders',
		'description' => 'Emails LDAP users nearing their password expiry date, and disabled those who exceed it',
		'url' => './cron/some_url.php',
		'last_run' => '2024-05-01 12:23:34'
	],
	'accurate_sync' => [
		'name' => 'Accurate/Mercury Sync',
		'description' => 'Populates the Accurate/Mercury staging table with current CUD data',
		'url' => './cron/accurate_sync.php',
		'last_run' => '2024-05-01 12:23:34'
	],
	'iplicit_syn' => [
		'name' => 'iPlicit Sync',
		'description' => 'Compares each iPlicit/CUD record and submits only required updates (via API) to iPlicit',
		'url' => './cron/iplicit_syng.php',
		'last_run' => '2024-05-01 12:23:34'
	],
	'salto_sync' => [
		'name' => 'SALTO Sync',
		'description' => 'Populates the SALTO staging table with current CUD data',
		'url' => './cron/salto_sync.php',
		'last_run' => '2024-05-01 12:23:34'
	],
	'kx_sync' => [
		'name' => 'Kx Sync',
		'description' => 'Populates the Kx staging table with current CUD data',
		'url' => './cron/salto_sync.php',
		'last_run' => '2024-05-01 12:23:34'
	]
);

?>
<table class="table">
	<thead>
		<tr>
			<th scope="col">Task</th>
			<th scope="col">Last Run</th>
			<th scope="col"></th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($tasks AS $task) {
			$output  = "<tr>";
			$output .= "<th scope=\"row\">" . $task['name'] . popover('info', 'Description', $task['description']) . "</th>";
			$output .= "<td>" . $task['last_run'] . "</td>";
			$output .= "<td><a class=\"btn btn-sm btn-primary run-now-button\" href=\"#\" data-url=\"" . $task['url'] . "\" role=\"button\">Run Now</a></td>";
			$output .= "</tr>";
			
			echo $output;
		}
		?>
	</tbody>
</table>

<script>
document.addEventListener('DOMContentLoaded', function () {
	document.querySelectorAll('.run-now-button').forEach(function(button) {
		button.addEventListener('click', function(event) {
			event.preventDefault();

			const url = button.getAttribute('data-url');
			if (!url) return;

			if (!confirm('Are you sure?')) return;

			// Show spinner
			const originalText = button.innerHTML;
			button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Running...';
			button.disabled = true;

			fetch(url, {
				method: 'GET'
			})
			.then(response => {
				if (!response.ok) throw new Error('Network response was not ok');
				return response.text(); // or response.json() if JSON
			})
			.then(data => {
				button.innerHTML = 'Complete';
			})
			.catch(error => {
				console.error('Error:', error);
				button.innerHTML = 'Error';
			})
			.finally(() => {
				setTimeout(() => {
					button.innerHTML = originalText;
					button.disabled = false;
				}, 2000);
			});
		});
	});
});
</script>