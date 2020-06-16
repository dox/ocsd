<?php
function useraccountcontrolbadge ($flagValue = null) {
	$userAccountControlFlags = array(
		16777216 => "TRUSTED_TO_AUTH_FOR_DELEGATION",
		8388608 => "PASSWORD_EXPIRED",
		4194304 => "DONT_REQ_PREAUTH",
		2097152 => "USE_DES_KEY_ONLY",
		1048576 => "NOT_DELEGATED",
		524288 => "TRUSTED_FOR_DELEGATION",
		262144 => "SMARTCARD_REQUIRED",
		131072 => "MNS_LOGON_ACCOUNT",
		65536 => "DONT_EXPIRE_PASSWORD",
		8192 => "SERVER_TRUST_ACCOUNT",
		4096 => "WORKSTATION_TRUST_ACCOUNT",
		2048 => "INTERDOMAIN_TRUST_ACCOUNT",
		546 => "DISABLED, Password Not Required",
		544 => "ENABLED, Password Not Required",
		514 => "DISABLED_ACCOUNT",
		512 => "NORMAL_ACCOUNT",
		256 => "TEMP_DUPLICATE_ACCOUNT",
		128 => "ENCRYPTED_TEXT_PWD_ALLOWED",
		64 => "PASSWD_CANT_CHANGE",
		32 => "PASSWD_NOTREQD",
		16 => "LOCKOUT",
		8 => "HOMEDIR_REQUIRED",
		2 => "ACCOUNTDISABLE",
		1 => "SCRIPT"
	);

	if (array_key_exists($flagValue, $userAccountControlFlags)) {
		if ($flagValue == 512 ) {
			$badgeClass = "badge-success";
		} elseif ($flagValue == 514 ) {
			$badgeClass = "badge-danger";
		} else {
			$badgeClass = "badge-secondary";
		}
		$flagName = $userAccountControlFlags[$flagValue];
	} else {
		$badgeClass = "badge-secondary";
		$flagName = "unknown " . $flagValue;
	}

	$output = "<span class=\"badge " . $badgeClass . "\">" . $flagName . "</span>";

	return $output;
}

function pwdlastsetage ($pwdlastset = null) {
	$winSecs       = (int)($pwdlastset / 10000000); // divide by 10 000 000 to get seconds
	$unixTimestamp = ($winSecs - 11644473600); // 1.1.1600 -> 1.1.1970 difference in seconds
	$pwdlastsetDate = date('U', $unixTimestamp);
	$dateToday = date('U');
	$pwdlastsetAgeInDays = round(($dateToday - $pwdlastsetDate)/60/60/24,0);

	return $pwdlastsetAgeInDays;
}

function pwdlastsetbadge ($pwdlastset = null) {
	$pwdlastsetAgeInDays = pwdlastsetage($pwdlastset);

	if ($pwdlastsetAgeInDays <= pwd_warn_age) {
		$badgeClass = "badge-success";
		$flagName = "Password OK aged " . $pwdlastsetAgeInDays . " days";
	} elseif ($pwdlastsetAgeInDays > pwd_warn_age && $pwdlastsetAgeInDays < pwd_max_age) {
		$badgeClass = "badge-warning";
		$flagName = "Password expiring in " . $pwdlastsetAgeInDays . " days";
	} elseif ($pwdlastsetAgeInDays > pwd_max_age) {
		$badgeClass = "badge-danger";
		$flagName = "Password EXPIRED aged " . $pwdlastsetAgeInDays . " days";
	} else {
		$badgeClass = "badge-secondary";
		$flagName = "Password UNKNOWN aged " . $pwdlastsetAgeInDays . " days";
	}



	$output = "<span class=\"badge " . $badgeClass . "\">" . $flagName . "</span>";

	return $output;
}

function randomPassword() {
	$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	$pass = array(); //remember to declare $pass as an array
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache

	for ($i = 0; $i < 12; $i++) {
		$n = rand(0, $alphaLength);
		$pass[] = $alphabet[$n];
	}

	return implode($pass); //turn the array into a string
}

function ss_ldap_connect() {
	$ldapconn = ldap_connect(LDAP_SERVER);

	ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
	if (LDAP_STARTTLS == true) {
		ldap_start_tls($ldapconn);
	}

	if (debug) {
		echo "<div class=\"alert alert-success\" role=\"alert\">";
		echo "<kbd>ldap_connect</kbd> to <code>" . LDAP_SERVER . "</code>";
		echo "</div>";
	}

	return $ldapconn;
}

function ss_ldap_bind($ldap_conn, $ldap_bind_dn, $ldap_bind_password) {
	$ldapbind = ldap_bind($ldap_conn, $ldap_bind_dn, $ldap_bind_password);

	if ($ldapbind) {
		if (debug) {
			echo "<div class=\"alert alert-success\" role=\"alert\">";
			echo "<kbd>ldap_bind</kbd> with <code>" . $ldap_bind_dn . "</code> is <code>" . $ldapbind . "</code>";
			echo "</div>";
		}
	} else {
		if (debug) {
			echo "<div class=\"alert alert-danger\" role=\"alert\">";
			echo "<kbd>ldap_bind</kbd> with <code>" . $ldap_bind_dn . "</code> is <code>" . $ldapbind . "</code>";
			echo "</div>";
		}
	}

	return $ldapbind;
}

function ss_ldap_search($ldap_conn, $ldap_search_ou, $ldap_search_filter) {
	$ldapsearch = ldap_search($ldap_conn, $ldap_search_ou, $ldap_search_filter, LDAP_VALUES);

	if ($ldapsearch) {
		if (debug) {
			echo "<div class=\"alert alert-success\" role=\"alert\">";
			echo "<kbd>ldap_search</kbd> in <code>" . $ldap_search_ou . "</code> with filter <code>" . $ldap_search_filter . "</code> and values <code>" . implode(LDAP_VALUES,", ") . "</code> is <code>" . $ldapsearch . "</code>";
			echo "</div>";

			echo "<div class=\"alert alert-success\" role=\"alert\">";
			echo "<kbd>ldap_count_entries</kbd> returned  <code>" . ldap_count_entries($ldap_conn, $ldapsearch)  . "</code> entries";
			echo "</div>";
		}
	} else {
		if (debug) {
			echo "<div class=\"alert alert-danger\" role=\"alert\">";
			echo "<kbd>ldap_search</kbd> in <code>" . $ldap_search_ou . "</code> with filter <code>" . $ldap_search_filter . "</code> is <code>" . $ldapsearch . "</code>";
			echo "</div>";
		}
	}

	return $ldapsearch;
}

function ss_ldap_get_entries($ldap_conn, $ldap_search_results) {
	$ldapentries = ldap_get_entries($ldap_conn, $ldap_search_results);

	if ($ldapentries) {
		if (debug) {
			echo "<div class=\"alert alert-success\" role=\"alert\">";
			echo "<kbd>ldap_get_entries</kbd> in <code>" . $ldap_search_results . "</code>";
			echo "</div>";
		}
	} else {
		if (debug) {
			echo "<div class=\"alert alert-danger\" role=\"alert\">";
			echo "<kbd>ldap_get_entries</kbd> in <code>" . $ldap_search_results . "</code>";
			echo "</div>";
		}
	}

	return $ldapentries;
}

function ss_ldap_mod_replace($ldap_conn, $userDN, $actionsArray) {
	$ldapmodreplace = ldap_mod_replace($ldap_conn, $userDN, $actionsArray) or die(ldap_error($ldap_conn));

	if (debug) {
		if ($ldapmodreplace) {
			echo "<div class=\"alert alert-success\" role=\"alert\">";
			echo "<kbd>ldap_mod_replace</kbd> for user <code>" . $userDN . "</code> with values <code>" . implode(", ",array_keys($actionsArray)) . "</code>";
			echo "</div>";
		} else {
			echo "<div class=\"alert alert-danger\" role=\"alert\">";
			echo "<kbd>ldap_mod_replace</kbd> for user <code>" . $userDN . "</code> with values <code>" . implode(", ",array_keys($actionsArray)) . "</code>";
			echo "</div>";
		}
	}
	return $ldapmodreplace;
}
?>
