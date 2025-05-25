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

$templateMessage = setting('ldap_password_warn_template');

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
			
			$recipients = [
				'to' => [
					'email' => $ldapUser->getEmail()
				]
			];
			
			sendMail('SEH Password expiry', $recipients, $finalMessage);
		}
	} else {
		// Password fine
	}
}
?>
