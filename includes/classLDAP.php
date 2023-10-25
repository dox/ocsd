<?php
class LDAP {
	function __construct() {
		//$ldap_connection
		//$this->ldapconn = ldap_connect(LDAP_SERVER);
	
		ldap_set_option ($ldap_connection, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
	
		  if (LDAP_STARTTLS == true) {
			  //ldap_start_tls($ldap_connection);
		  }
	
		  if (debug) {
			  echo "<div class=\"alert alert-success\" role=\"alert\">";
			  echo "<kbd>ldap_connect</kbd> to <code>" . implode(", ", LDAP_SERVER) . "</code>";
			  echo "</div>";
		  }
		return $this->ldapconn;
	  }
	  
	  public function all_users_enabled() {
			global $ldap_connection;
		
			$records = $ldap_connection->query()->select('samaccountname', 'mail', 'pager', 'givenname', 'sn', 'cn')->orFilter(function (LdapRecord\Query\Builder $q) {
			  $q->where('useraccountcontrol', '=', '512')
			  ->where('useraccountcontrol', '=', '544');
			})->paginate(10000);
			
			return $records;
		}
		
		public function all_users_disabled() {
			global $ldap_connection;
		
			$records = $ldap_connection->query()->select('samaccountname', 'mail', 'givenname', 'sn', 'cn')->andFilter(function (LdapRecord\Query\Builder $q) {
			  $q->where('useraccountcontrol', '!', '512')
			  ->where('useraccountcontrol', '!', '544');
			})->get();
		
			return $records;
		  }
		
		public function all_users_in_group($cn) {
			  global $ldap_connection;
			  
			  
			  $group = \LdapRecord\Models\ActiveDirectory\Group::find($cn);
			$members = $group->members()->get();
			
			  return $members;
		  }
	 
	  
	  
	  public function search($search = null) {
		  global $ldap_connection;
		  
		  
		  
		  $results = $ldap_connection->query()->where('samaccountname', 'contains', $search)
		  ->orWhere('cn', 'contains', $search)
		  ->orWhere('mail', 'contains', $search)
		  ->get();
		  
		  if (debug) {
			  echo "<div class=\"alert alert-success\" role=\"alert\">";
			  echo "<kbd>ldap_search</kbd> for <code>" . $search . "</code> returned <code>" . count($results) . "</code> results";
			  echo "</div>";
		  }
		  
		  return $results;
		}
		
		public function all_users() {		  
			  $records = \LdapRecord\Models\ActiveDirectory\User::get();
		  
			  return $records;
		  }
	  
	  public function usersWithoutCUD() {
			$allUsers = $this->all_users_enabled();
			
			$returnUsers = array();
			
			$personsClass = new Persons();
			
			foreach ($allUsers AS $ldapUser) {
				$CUDPerson = $personsClass->search($ldapUser['samaccountname'][0], 2);
				//printArray($CUDPerson);
				if (!isset($CUDPerson[0]['cudid'])) {
					$returnUsers[] = $ldapUser;
				}
			}
			
			if (debug) {
				  echo "<div class=\"alert alert-success\" role=\"alert\">";
				  echo "<kbd>usersWithoutCUD</kbd> returned <code>" . count($returnUsers) . "</code> results";
				  echo "</div>";
			  }
			
			return $returnUsers;
		}
		
		public function usersWithoutLDAP() {
			$personsClass = new Persons();
			
			$allUsers = $personsClass->all();
			
			$returnUsers = array();
			
			foreach ($allUsers AS $CUDuser) {
				$ldapUser = new LDAPPerson($CUDuser['sso_username'], $CUDuser['oxford_email']);
				$ldapUser = (array)$ldapUser;
				
				if (!isset($ldapUser['samaccountname']) && isset($CUDuser['sso_username'])) {
					$returnUsers[] = $CUDuser['sso_username'];
				}
			}
			
			return $returnUsers;
		}
	  
	  public function stale_users($baseDN = LDAP_BASE_DN, $includeDisabled = false) {
		  global $ldap_connection;
	  
		  $date = (strtotime(pwd_warn_age*3 . " days ago") + 11644473600)*10000000;
	  
		  $records = $ldap_connection->query()->select('samaccountname')->where([
			['pwdlastset', '<=', $date],
			['objectclass', '!=', 'computer'],
		  ])->paginate(1000);
	  
		  return $records;
	}
	
	public function stale_workstations($baseDN = LDAP_BASE_DN, $includeDisabled = false) {
		global $ldap_connection;
	
		$date = (strtotime(pwd_warn_age*3 . " days ago") + 11644473600)*10000000;
	
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
		$object = LdapRecord\Models\ActiveDirectory\Entry::find($this->dn);
	
		$object->delete();
	
		$logInsert = (new Logs)->insert("ldap","warning",null,"Deleted from LDAP <code>" . $this->samaccountname . "</code>");
	
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
}
?>