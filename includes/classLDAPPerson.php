<?php
class LDAPPerson extends LDAP {
	function __construct($samaccountname = null, $mail = null) {
    global $ldap_connection;

		$ldap_entries = $ldap_connection->query()
			->where('samaccountname', '=', $samaccountname)
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

	public function pwdlastsetage () {
  	$pwdlastsetDate = $this->pwdlastsetdate();

  	$dateToday = date('U');
  	$pwdlastsetAgeInDays = round(($dateToday - $pwdlastsetDate)/60/60/24,0);

  	return $pwdlastsetAgeInDays;
  }

	public function pwdlastsetdate () {
  	$winSecs       = (int)($this->pwdlastset / 10000000); // divide by 10 000 000 to get seconds
  	$unixTimestamp = ($winSecs - 11644473600); // 1.1.1600 -> 1.1.1970 difference in seconds
  	$pwdlastsetDate = date('U', $unixTimestamp);

  	return $pwdlastsetDate;
  }

  public function pwdlastsetbadge () {
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

	public function useraccountcontrolbadge () {
  	if ($this->isEnabled()) {
  		$badgeClass = "bg-primary";
  	} elseif (in_array($this->useraccountcontrol, array("2", "16", "514", "546", "66050", "66082", "8388608"))) {
  		$badgeClass = "bg-danger";
  	} elseif (in_array($this->useraccountcontrol, array("66048"))) {
  		$badgeClass = "bg-warning";
  	} else {
  		$badgeClass = "bg-dark";
  	}

    $output  = "<a href=\"index.php?n=card_types\" class=\"text-decoration-none badge " . $badgeClass . "\">" . $this->useraccountcontrol . "</a>";

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

    if ($_SESSION["user_type"] == "Administrator") {
			if (isset($this->samaccountname) && !$this->isEnabled()) {
				if ($person->isSuspended() == false) {
					$output .= "<a class=\"dropdown-item ldap_enable_user_resetPwd\" id=\"" . $this->samaccountname . "\" href=\"#\">Enable Account (reset password)</a>";
					//$output .= "<a class=\"dropdown-item ldap_enable_user\" id=\"" . $this->samaccountname . "\" href=\"#\">Enable Account</a>";
				} else {
					$output .= "<a class=\"dropdown-item disabled\" href=\"#\"><s>CUD ID Suspended</s></a>";
				}
      }

			if (isset($this->samaccountname) && $this->isEnabled()) {
        $output .= "<a class=\"dropdown-item ldap_disable_user\" id=\"" . $this->samaccountname . "\" href=\"#\">Disable Account</a>";
      }

			if (isset($this->samaccountname) && !$this->isEnabled()) {
        $output .= "<a class=\"dropdown-item text-danger ldap_delete_user\" id=\"" . $this->samaccountname . "\" href=\"#\">Delete Account</a>";
      }

      if (!isset($this->samaccountname)) {
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
