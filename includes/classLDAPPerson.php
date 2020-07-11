<?php
class LDAPPerson extends LDAP {
	function __construct($samaccountname = null, $mail = null) {
    global $ldap_connection;

    if (isset($samaccountname) && isset($mail)) {
      $filter = "(|(sAMAccountName=" . $samaccountname . ")(mail=" . $mail . "))";
    } else {
      $filter = "(sAMAccountName=" . $samaccountname . ")";
    }

    $ldap_bind = $ldap_connection->ldap_bind();
  	$ldap_search_results = $ldap_connection->ldap_search(LDAP_BASE_DN, $filter);
  	$ldap_entries = $ldap_connection->ldap_get_entries($ldap_search_results);

    if ($ldap_entries['count'] == 1) {
      $ldapUser = $ldap_entries[0];

      $keys = array_keys($ldapUser);

      foreach ($keys AS $key) {
        //printArray($value);
        if (!is_numeric($key)) {
          if ($key == "dn") {
						$this->$key = $ldapUser[$key];
					} elseif ($key == "memberof" || $key == "objectclass") {
						foreach ($ldapUser[$key] AS $ldapKey => $memberOfElement) {
							if (is_numeric($ldapKey)) {
								$this->$key[] = $memberOfElement;
							}
						}
					} else {
            $this->$key = $ldapUser[$key][0];
          }
        }
      }
    }
  }

  public function emailAddress() {
    if (isset($this->mail)) {
      return makeEmail($this->mail);
    }
  }

  public function pwdlastsetage () {
  	$winSecs       = (int)($this->pwdlastset / 10000000); // divide by 10 000 000 to get seconds
  	$unixTimestamp = ($winSecs - 11644473600); // 1.1.1600 -> 1.1.1970 difference in seconds
  	$pwdlastsetDate = date('U', $unixTimestamp);
  	$dateToday = date('U');
  	$pwdlastsetAgeInDays = round(($dateToday - $pwdlastsetDate)/60/60/24,0);

  	return $pwdlastsetAgeInDays;
  }

  public function pwdlastsetbadge () {
  	$pwdlastsetAgeInDays = $this->pwdlastsetage();

  	if ($pwdlastsetAgeInDays <= pwd_warn_age) {
  		$badgeClass = "bg-green";
  		$flagName = "Password OK aged " . $pwdlastsetAgeInDays . autoPluralise(" day", " days", $pwdlastsetAgeInDays);
  	} elseif ($pwdlastsetAgeInDays >= pwd_warn_age && $pwdlastsetAgeInDays <= pwd_max_age) {
  		$badgeClass = "bg-yellow";
  		$flagName = "Password expiring in " . (pwd_max_age - $pwdlastsetAgeInDays) . autoPluralise(" day", " days", (pwd_max_age - $pwdlastsetAgeInDays));
  	} elseif ($pwdlastsetAgeInDays > pwd_max_age) {
  		$badgeClass = "bg-red";
  		$flagName = "Password EXPIRED aged " . $pwdlastsetAgeInDays . autoPluralise(" day", " days", $pwdlastsetAgeInDays);
  	} else {
  		$badgeClass = "bg-gray";
  		$flagName = "Password UNKNOWN aged " . $pwdlastsetAgeInDays . autoPluralise(" day", " days", $pwdlastsetAgeInDays);
  	}

  	$output = "<span class=\"badge " . $badgeClass . "\">" . $flagName . "</span>";

  	return $output;
  }

  public function useraccountcontrolbadge () {
  	if (in_array($this->useraccountcontrol, array("512", "544"))) {
  		$badgeClass = "bg-green";
  	} elseif (in_array($this->useraccountcontrol, array("2", "16", "514", "546", "8388608"))) {
  		$badgeClass = "bg-red";
  	} else {
  		$badgeClass = "bg-gray";
  	}

    $output  = "<a href=\"index.php?n=card_types\" class=\"badge " . $badgeClass . "\">" . $this->useraccountcontrol . "</a>";

  	return $output;
  }

  public function actionsButton($cudid = null) {
    $output  = "<div class=\"dropdown\">";
    $output .= "<button class=\"btn btn-sm btn-secondary dropdown-toggle\" type=\"button\" id=\"dropdownMenuButton\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">LDAP Actions</button>";
    $output .= "<div class=\"dropdown-menu\" aria-labelledby=\"dropdownMenuButton\">";

    if (in_array($_SESSION['username'], admin_usernames)){
      if (isset($this->samaccountname) && !in_array($this->useraccountcontrol, array("512", "544"))) {
        $output .= "<a class=\"dropdown-item ldap_enable_user\" id=\"" . $this->samaccountname . "\" href=\"#\">Enable Account</a>";
      }

      if (isset($this->samaccountname) && in_array($this->useraccountcontrol, array("512", "544"))) {
        $output .= "<a class=\"dropdown-item ldap_disable_user\" id=\"" . $this->samaccountname . "\" href=\"#\">Disable Account</a>";
      }


      if ($cudid != null) {
				$person = new Person($cudid);

        $output .= "<a class=\"dropdown-item ldap_provision_user\" id=\"" . $person->cudid . "\" href=\"#\">Provison Silently</a>";
				if (isset($person->oxford_email)) {
					$output .= "<a class=\"dropdown-item ldap_provision_user provision_with_email\" id=\"" . $person->cudid . "\" href=\"#\">Provision and Send Welcome Email</a>";
				}
      }
    }

    $output .= "</div>";
    $output .= "</div>";

    return $output;
  }
} //end of class LDAPPerson
?>
