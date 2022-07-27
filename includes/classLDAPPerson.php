<?php
class LDAPPerson extends LDAP {
	function __construct($samaccountname = null, $mail = null) {
    global $ldap_connection;

		$ldap_entries = $ldap_connection->query()
			->where('samaccountname', '=', $samaccountname)
			->orWhere('cn', '=', $samaccountname)
			->orWhere('mail', '=', $mail)
			->get();

    if (count($ldap_entries) == 1) {
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
  
  public function ldapButton() {
    $url = "index.php?n=ldap_unique&samaccountname=" . $this->samaccountname;
    
    $output  = "<a href=\"" . $url . "\" class=\"btn btn-light position-relative\">";
    $output .= $this->samaccountname;
    $output .= $this->useraccountcontrolbadge();
    $output .= "</a>";
    
    return $output;
  }

	public function pwdlastsetage() {
  	$pwdlastsetDate = $this->pwdlastsetdate();

  	$dateToday = date('U');
  	$pwdlastsetAgeInDays = round(($dateToday - $pwdlastsetDate)/60/60/24,0);

  	return $pwdlastsetAgeInDays;
  }

	public function pwdlastsetdate() {
  	$winSecs       = (int)($this->pwdlastset / 10000000); // divide by 10 000 000 to get seconds
  	$unixTimestamp = ($winSecs - 11644473600); // 1.1.1600 -> 1.1.1970 difference in seconds
  	$pwdlastsetDate = date('U', $unixTimestamp);

  	return $pwdlastsetDate;
  }

  public function pwdlastsetbadge() {
  	$pwdlastsetAgeInDays = $this->pwdlastsetage();

  	if ($pwdlastsetAgeInDays <= pwd_warn_age) {
  		$badgeClass = "bg-primary";
  		$flagName = "Password OK aged " . howLongAgo($this->pwdlastsetdate());
  	} elseif ($pwdlastsetAgeInDays >= pwd_warn_age && $pwdlastsetAgeInDays <= pwd_max_age) {
  		$badgeClass = "bg-warning";
  		$flagName = "Password expiring in " . (pwd_max_age - $pwdlastsetAgeInDays) . autoPluralise(" day", " days", (pwd_max_age - $pwdlastsetAgeInDays));
  	} elseif ($pwdlastsetAgeInDays > pwd_max_age) {
  		$badgeClass = "bg-danger";
  		$flagName = "Password EXPIRED " . howLongAgo($this->pwdlastsetdate());
  	} else {
  		$badgeClass = "bg-dark";
  		$flagName = "Password UNKNOWN aged " . $pwdlastsetAgeInDays . autoPluralise(" day", " days", $pwdlastsetAgeInDays);
  	}

  	$output = "<span class=\"badge " . $badgeClass . "\">" . $flagName . "</span>";

  	return $output;
  }

	public function useraccountcontrolbadge() {
  	if (in_array($this->useraccountcontrol, array("512", "544"))) {
  		$badgeClass = "bg-success";
  	} elseif (in_array($this->useraccountcontrol, array("2", "16", "514", "546", "66050", "66082", "8388608"))) {
  		$badgeClass = "bg-danger";
  	} elseif (in_array($this->useraccountcontrol, array("66048"))) {
  		$badgeClass = "bg-warning";
  	} else {
  		$badgeClass = "bg-dark";
  	}
    
    $output  = "<span class=\"position-absolute top-0 start-100 translate-middle badge rounded-pill " . $badgeClass . "\">";
    $output .= $this->useraccountcontrol;
    $output .= "</span>";

  	return $output;
  }

	public function isEnabled () {
		$enabledValues = array("512", "544");

  	if (in_array($this->useraccountcontrol, $enabledValues)) {
  		return true;
  	} else {
			return false;
		}
  }

  public function actionsButton($cudid = null, $class = "btn-sm btn-secondary") {
		global $db;

		$person = new Person($cudid);

    $output  = "<div class=\"dropdown\">";
    $output .= "<button class=\"btn " . $class . " dropdown-toggle\" type=\"button\" id=\"dropdownMenuButton\" data-bs-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">LDAP Actions</button>";
    $output .= "<div class=\"dropdown-menu\" aria-labelledby=\"dropdownMenuButton\">";

		if (isset($this->mail)) {
			$output .= "<a class=\"dropdown-item\" href=\"mailto:" . $this->mail . "\">Email</a>";
		}
    
    //if ($_SESSION["user_type"] == "Administrator") {
			if (isset($this->samaccountname) && !$this->isEnabled()) {
				if ($person->isSuspended() == false) {
          $output .= "<a class=\"dropdown-item\" href=\"#\" onclick=\"ldap_toggle_user(this, '" . $this->samaccountname . "', 'enable')\">Enable Account</a>";
				} else {
					$output .= "<a class=\"dropdown-item disabled\" href=\"#\"><s>CUD ID Suspended</s></a>";
				}
      }

			if (isset($this->samaccountname) && $this->isEnabled()) {
        $output .= "<a class=\"dropdown-item\" href=\"#\" onclick=\"ldap_toggle_user(this, '" . $this->samaccountname . "', 'disable')\">Disable Account</a>";
      }

			if (isset($this->samaccountname) && !$this->isEnabled()) {
        $output .= "<a class=\"dropdown-item text-danger\" href=\"#\" onclick=\"ldap_delete_user(this, '" . $this->samaccountname . "')\">Delete Account</a>";
      }

      if (!isset($this->samaccountname)) {
        $output .= "<a class=\"dropdown-item\" href=\"#\" onclick=\"ldap_provision_user(this, '" . $person->cudid . "', 'disable')\">Provison Silently</a>";
				if (isset($person->oxford_email)) {
					$output .= "<a class=\"dropdown-item\" href=\"#\" onclick=\"ldap_provision_user(this, '" . $person->cudid . "', 'enable')\">Provision and Send Welcome Email</a>";
				}
      }
    //}

    $output .= "</div>";
    $output .= "</div>";

    return $output;
  }
} //end of class LDAPPerson
?>
