<?php
class LDAP {
  public $ldapconn;
  public $ou;
  public $sn;
  public $cn;
  public $dn;
  public $description;
  public $samaccountname;
  public $givenname;
  public $mail;
  public $pager;
  public $lastlogon;
  public $pwdlastset;
  public $useraccountcontrol;
  public $memberof;
  public $objectclass;


	function __construct() {
    $this->ldapconn = ldap_connect(LDAP_SERVER);

    ldap_set_option ($this->ldapconn, LDAP_OPT_REFERRALS, 0);
    ldap_set_option($this->ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

  	if (LDAP_STARTTLS == true) {
  		ldap_start_tls($this->ldapconn);
  	}

  	if (debug) {
  		echo "<div class=\"alert alert-success\" role=\"alert\">";
  		echo "<kbd>ldap_connect</kbd> to <code>" . LDAP_SERVER . "</code>";
  		echo "</div>";
  	}
    return $this->ldapconn;
  }

  public function all_users_enabled() {
    global $ldap_connection;

    $records = $ldap_connection->query()->select('samaccountname', 'mail', 'pager')->orFilter(function (LdapRecord\Query\Builder $q) {
      $q->where('useraccountcontrol', '=', '512')
      ->where('useraccountcontrol', '=', '544');
    })->get();


    return $records;
  }

  public function all_users_disabled() {
    global $ldap_connection;

    $records = $ldap_connection->query()->select('samaccountname', 'mail')->andFilter(function (LdapRecord\Query\Builder $q) {
      $q->where('useraccountcontrol', '!', '512')
      ->where('useraccountcontrol', '!', '544');
    })->get();

    return $records;
  }

  public function search_users($searchTerm = null) {
    global $ldap_connection;

    $records = $ldap_connection->query()
      ->where('samaccountname', '=', $searchTerm)
      ->orWhere('cn', 'contains', $searchTerm)
      ->get();

    return $records;
  }

  public function stale_users($baseDN = LDAP_BASE_DN, $includeDisabled = false) {
    global $ldap_connection;

    $date = (strtotime(pwd_warn_age*3 . " days ago") + 11644473600)*10000000;

    /*$filters = [
      'pwdlastset<=' . $date,
      'objectclass = top'
    ];

    $records = $ldap_connection->query()->select('samaccountname')->rawFilter($filters)->paginate(1000);
*/
    $records = $ldap_connection->query()->select('samaccountname')->where([
      ['pwdlastset', '<=', $date],
      ['objectclass', '!=', 'computer'],
    ])->paginate(1000);

    return $records;
  }

  public function stale_workstations($baseDN = LDAP_BASE_DN, $includeDisabled = false) {
    global $ldap_connection;

    $date = (strtotime(pwd_warn_age*3 . " days ago") + 11644473600)*10000000;

    /*$filters = [
      'pwdlastset<=' . $date,
      'objectclass = top'
    ];

    $records = $ldap_connection->query()->select('samaccountname')->rawFilter($filters)->paginate(1000);
*/
    $records = $ldap_connection->query()->select('samaccountname')->where([
      ['pwdlastset', '<=', $date],
      ['objectclass', '=', 'computer'],
    ])->paginate(1000);

    return $records;
  }

  public function expiring_users() {
    global $ldap_connection;

    $date = (strtotime(pwd_warn_age . " days ago") + 11644473600)*10000000;

    $filters = [
      '(pwdlastset<=' . $date . ')',
      '(|(useraccountcontrol=512)(useraccountcontrol=544))'
    ];

    $records = $ldap_connection->query()->rawFilter($filters)->get();

    return $records;
  }

  public function randomPassword($length = 12) {
  	$alphabet = 'abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ23456789£%@+(=),.';
  	$pass = array(); //remember to declare $pass as an array
  	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache

  	for ($i = 0; $i < $length; $i++) {
  		$n = rand(0, $alphaLength);
  		$pass[] = $alphabet[$n];
  	}

  	return implode($pass); //turn the array into a string
  }

  public function userAccountControlFlags() {
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

    return $userAccountControlFlags;
  }

  public function ldap_mod_replace($userDN, $actionsArray) {
    $object = LdapRecord\Models\Entry::find($userDN);

    foreach ($actionsArray AS $key => $value) {
      $object->$key = $value;
    }
    $object->save();

    $logInsert = (new Logs)->insert("ldap","warning",null,"LDAP record updated with " . implode(", ",array_keys($actionsArray)) . " for user " . $userDN);

  	return false;
  }

  public function enableUser($resetPassword = false) {
    $object = LdapRecord\Models\ActiveDirectory\User::find($this->dn);

    $object->useraccountcontrol = 512;

    if ($resetPassword == true) {
      $object->unicodepwd = $this->randomPassword();
    }

    $object->save();

    $logInsert = (new Logs)->insert("ldap","warning",null,"Enable user account <code>" . $this->samaccountname . "</code>");

  	return false;
  }

  public function disableUser() {
    $object = LdapRecord\Models\ActiveDirectory\User::find($this->dn);

    $object->useraccountcontrol = 514;
    $object->unicodepwd = $this->randomPassword();
    $object->save();

    $logInsert = (new Logs)->insert("ldap","warning",null,"Disable user account <code>" . $this->samaccountname . "</code>");

  	return false;
  }

  public function deleteUser() {
    $object = LdapRecord\Models\ActiveDirectory\User::find($this->dn);

    $object->delete();

    $logInsert = (new Logs)->insert("ldap","warning",null,"Deleted user account <code>" . $this->samaccountname . "</code>");

  	return false;
  }

  public function ldap_add($userDN, $actionsArray) {
  	$ldapadd = ldap_add($this->ldapconn, $userDN, $actionsArray) or die(ldap_error($ldapconn));

  	if (debug) {
  		if ($ldapadd) {
  			echo "<div class=\"alert alert-success\" role=\"alert\">";
  			echo "<kbd>ldap_add</kbd> for user <code>" . $userDN . "</code> with values <code>" . implode(", ",array_keys($actionsArray)) . "</code>";
  			echo "</div>";
  		} else {
  			echo "<div class=\"alert alert-danger\" role=\"alert\">";
  			echo "<kbd>ldap_add</kbd> for user <code>" . $userDN . "</code> with values <code>" . implode(", ",array_keys($actionsArray)) . "</code>";
  			echo "</div>";
  		}
  	}
  	return $ldapadd;
  }
} //end of class LDAP

function w32timeToTime($inputTime = null) {
  $winSecs = (int)($inputTime / 10000000); // divide by 10 000 000 to get seconds
  $unixTimestamp = ($winSecs - 11644473600); // 1.1.1600 -> 1.1.1970 difference in seconds

  return ($unixTimestamp);
}
?>
