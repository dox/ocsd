<?php
// enable accounts when their suspension dates have ended

include_once("../inc/autoload.php");

$ldap = new Ldap();

$sql = "SELECT cudid
		FROM Suspensions
		WHERE
		  -- Calculate effective end date (either real or expected)
		  STR_TO_DATE(COALESCE(SuspendEndDt, SuspendExpEndDt), '%Y%m%d')
			BETWEEN DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND DATE_SUB(CURDATE(), INTERVAL 1 DAY)";

$personsUnsuspended = $db->get($sql);

foreach ($personsUnsuspended AS $cudid) {
	$cudPerson = new Person($cudid);
	
	$user = $ldap->findUser($cudPerson->getLDAPUsername());
	
	
	if ($cudPerson->suspensions()->isCurrentlySuspended()) {
		// Allow the auto-closedown of suspended accounts?
		// $ldap->disableAccount($user);
		continue;
	}
	
	$cudPerson->getLDAPUsername();
	
	if (!in_array($cudPerson->ldapRecordCache['useraccountcontrol'][0], array('512','66048'))) {
		echo "Re-enabling " . $cudPerson->FullName . " now suspension has ended<br />";
		
		$ldap = new Ldap();
		$user = $ldap->findUser($cudPerson->getLDAPUsername());
		$ldap->enableAccount($user);
	}
}
?>