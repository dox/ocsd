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

foreach ($ldapUsers as $ldapUser) {
	$ldapUser = $ldap->findUser($ldapUser['samaccountname'][0]);
	$updates = array();
	
	$lookups = array_filter([
		'sso_username'		=> $ldapUser['samaccountname'][0] ?? null,
		'MiFareID'          => $ldapUser['pager'][0] ?? null,
		'oxford_email'		=> $ldapUser['mail'][0] ?? null
	]);
	$cudPerson = new Person($lookups);
	
	if (!empty($cudPerson->cudid)) {
		
		$fieldsToCheck = [
			'pager'     => 'MiFareID',
			'mail'      => 'oxford_email',
			'givenname' => 'firstname',
			'sn'        => 'lastname',
			//'cn' 		=> 'FullName'
		];
		
		foreach ($fieldsToCheck as $ldapField => $cudField) {
			$ldapVal = $ldapUser[$ldapField][0] ?? '';
			$cudVal = $cudPerson->$cudField ?? '';
		
			if ($ldapVal !== $cudVal) {
				//echo $ldapVal . " != " . $cudVal . "\n";
				$ldapUser->setAttribute($ldapField, $cudVal);
				$updates[$ldapField] = $cudVal;
			}
		}
		
		if (!empty($updates)) {
			$db->upsertByName('cron_ldap_sync', date('c'));
			
			cliOutput("Updating " . $ldapUser['samaccountname'][0] . ": " . implode(', ', array_keys($updates)) . " to " . implode(', ', $updates), "green");
			$logData = [
				'category' => 'cron',
				'result'   => 'success',
				'ldap'   => $ldapUser['samaccountname'][0],
				'description' => "Updating " . $ldapUser['samaccountname'][0] . ": " . implode(', ', array_keys($updates)) . " to " . implode(', ', $updates)
			];
			$log->create($logData);
			
			$ldapUser->save();
		}
	}
}
?>