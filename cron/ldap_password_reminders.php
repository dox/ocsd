<?php
include_once("../inc/autoload.php");

$filter = array('objectClass' => [
	'operator' => '!=',
	'value' => 'computer'
],
'useraccountcontrol' => [
	'operator' => '|',
	'value' => '512|544'
]);
$ldapUsers = $ldap->findByFilters($filter);
$warnDays = setting('ldap_password_warn_age');       // e.g., 365
$disableDays = setting('ldap_password_disable_age'); // e.g., 395

cliOutput("Itterating through " . count($ldapUsers) . " LDAP users", "green");

$ldap = new Ldap();

$templateMessage = "
<p>Dear {{firstname}},</p>
<p>The password for your St Edmund Hall username <strong>{{username}}</strong> will expire in <strong>{{password_expiry_duration}}</strong>.</p>

<p>This password grants access to IT services such as the College website (<a href=\"https://www.seh.ox.ac.uk\" target=\"_blank\">www.seh.ox.ac.uk</a>), SEH WiFi, printing, EPOS, and more.</p>

<p>To avoid disruption to these services, please change your password before it expires by <a href=\"https://www.seh.ox.ac.uk/it/password/index.php?node=reset_by_password&username={{username}}\" target=\"_blank\">resseting your password here</a></p>

<p>If you need assistance, please contact the IT Office at <a href=\"mailto:help@seh.ox.ac.uk\">help@seh.ox.ac.uk</a>.</p>

<p>Kind regards,<br>
<strong>IT Office</strong><br>
St Edmund Hall
</p>";

foreach ($ldapUsers as $ldapUser) {
	$user = $ldap->findUser($ldapUser['samaccountname'][0]);
	
	if ($user) {
		$ldapUser = new LdapUserWrapper($user);
	} else {
		echo $ldapUser['samaccountname'][0];
		$ldapUser = null;
	}
	
	$daysSince = daysSince($ldapUser->getPasswordLastSet());
	
	if ($daysSince >= $disableDays) {
		// Password expired
		cliOutput($ldapUser->getSAMAccountName() . " password expired " . ($daysSince - $disableDays) . " ago", "red");
		
		$ldap->disableAccount($user);

	} elseif ($daysSince >= $warnDays) {
		// Password at warning stage
		
		$daysLeft = abs($daysSince - $disableDays);
		
		cliOutput($ldapUser->getSAMAccountName() . " password expires in " . $daysLeft . " days", "yellow");
		
		// only email on specific days left
		if (in_array($daysLeft, array(0, 1, 3, 7, 14, 30))) {
			$data = [
				'firstname' => $ldapUser->getGivenname(),
				'username' => $ldapUser->getSAMAccountName(),
				'password_expiry_duration' => $daysLeft . autoPluralise(' day', ' days', $daysLeft)
			];
			
			$finalMessage = renderTemplate($templateMessage, $data);
			
			sendMail('SEH Password expiry', $ldapUser->getEmail(), null, $finalMessage);
		}
	} else {
		// Password fine
	}
}
?>
