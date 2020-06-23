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
  public $lastlogon;
  public $pwdlastset;
  public $useraccountcontrol;
  public $memberof;

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

  public function ldap_bind() {
  	$ldapbind = ldap_bind($this->ldapconn, LDAP_BIND_DN, LDAP_BIND_PASSWORD);

  	if ($ldapbind) {
  		if (debug) {
  			echo "<div class=\"alert alert-success\" role=\"alert\">";
  			echo "<kbd>ldap_bind</kbd> with <code>" . LDAP_BIND_DN . "</code> is <code>" . $ldapbind . "</code>";
  			echo "</div>";
  		}
  	} else {
  		if (debug) {
  			echo "<div class=\"alert alert-danger\" role=\"alert\">";
  			echo "<kbd>ldap_bind</kbd> with <code>" . LDAP_BIND_DN . "</code> is <code>" . $ldapbind . "</code>";
  			echo "</div>";
  		}
  	}

  	return $ldapbind;
  }

  public function ldap_search($ldap_search_ou, $ldap_search_filter) {
  	$ldapsearch = ldap_search($this->ldapconn, $ldap_search_ou, $ldap_search_filter, LDAP_VALUES);

  	if ($ldapsearch) {
  		if (debug) {
  			echo "<div class=\"alert alert-success\" role=\"alert\">";
  			echo "<kbd>ldap_search</kbd> in <code>" . $ldap_search_ou . "</code> with filter <code>" . $ldap_search_filter . "</code> and values <code>" . implode(LDAP_VALUES,", ") . "</code> is <code>" . $ldapsearch . "</code>";
  			echo "</div>";

  			echo "<div class=\"alert alert-success\" role=\"alert\">";
  			echo "<kbd>ldap_count_entries</kbd> returned  <code>" . ldap_count_entries($this->ldapconn, $ldapsearch)  . "</code> entries";
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

  public function ldap_list($ldap_search_ou, $ldap_search_filter) {
  	$ldapsearch = ldap_list($this->ldapconn, $ldap_search_ou, $ldap_search_filter, LDAP_VALUES);

  	if ($ldapsearch) {
  		if (debug) {
  			echo "<div class=\"alert alert-success\" role=\"alert\">";
  			echo "<kbd>ldap_list</kbd> in <code>" . $ldap_search_ou . "</code> with filter <code>" . $ldap_search_filter . "</code> and values <code>" . implode(LDAP_VALUES,", ") . "</code> is <code>" . $ldapsearch . "</code>";
  			echo "</div>";

  			echo "<div class=\"alert alert-success\" role=\"alert\">";
  			echo "<kbd>ldap_list</kbd> returned  <code>" . ldap_count_entries($this->ldapconn, $ldapsearch)  . "</code> entries";
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

  public function ldap_get_entries($ldap_search_results) {
  	$ldapentries = ldap_get_entries($this->ldapconn, $ldap_search_results);

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

  public function list_ou($baseDN = LDAP_BASE_DN) {
    $ouFilter = "(objectClass=organizationalUnit)";
    $ou_search_results = ldap_search($this->ldapconn, $baseDN, $ouFilter, LDAP_VALUES);
    $ouArrayResults = ldap_get_entries($this->ldapconn, $ou_search_results);
    for ($i=0; $i < $ouArrayResults["count"]; $i++) {
        $ouArray[] = $ouArrayResults[$i]["dn"];
    }

    return $ouArray;
  }

  public function all_users($baseDN = LDAP_BASE_DN, $includeDisabled = false) {
    $ous = $this->list_ou();

    foreach ($ous AS $ou) {
      if ($includeDisabled == true) {
        $allByOUFilter = "(sAMAccountName=*)";
      } else {
        $allByOUFilter = "(&(sAMAccountName=*)(useraccountcontrol=512))";
      }

      $all_by_ou_search_results = $this->ldap_list($ou, $allByOUFilter);
      $all_by_ou_entries = $this->ldap_get_entries($all_by_ou_search_results);

      //printArray($all_by_ou_entries);

      foreach ($all_by_ou_entries AS $user) {
        $users[] = $user;
      }
    }

    return $users;
  }

  public function randomPassword() {
  	$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
  	$pass = array(); //remember to declare $pass as an array
  	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache

  	for ($i = 0; $i < 12; $i++) {
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
  	$ldapmodreplace = ldap_mod_replace($this->ldapconn, $userDN, $actionsArray) or die(ldap_error($ldapconn));

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
?>
