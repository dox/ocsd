<?php
use LdapRecord\Models\Model;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;

class LdapUserWrapper {
	private Model $entry;

	public function __construct(Model $entry) {
		$this->entry = $entry;
	}

	public function getSAMAccountName(): ?string {
		return $this->entry->getFirstAttribute('samaccountname');
	}

	public function getEmail(): ?string {
		return $this->entry->getFirstAttribute('mail');
	}
	
	public function getPager(): ?string {
		return $this->entry->getFirstAttribute('pager');
	}

	public function getGivenname(): ?string {
		return $this->entry->getFirstAttribute('givenname');
	}
	
	public function getSN(): ?string {
		return $this->entry->getFirstAttribute('sn');
	}
	
	public function getDisplayName(): ?string {
		return $this->entry->getFirstAttribute('displayname');
	}

	public function getGroups(): array {
		return $this->entry->getAttribute('memberof', []);
	}

	public function getPasswordLastSet(): ?\DateTime {
		return $this->entry->getFirstAttribute('pwdlastset');
	}
	
	public function getUserAccountControl(): ?string {
		return $this->entry->getFirstAttribute('useraccountcontrol');
	}

	public function getRawEntry(): Model {
		return $this->entry;
	}
	
	public function getLDAPButton(): ?string {
		$sam = $this->getSAMAccountName();
		
		if ($sam) {
			$url = "index.php?page=ldap_user&samaccountname=" . urlencode($sam);

			return sprintf(
				'<a href="%s" class="btn btn-light position-relative">%s%s</a>',
				$url,
				htmlspecialchars($sam),
				$this->getUserAccountControlBadge()
			);
		}
		return null;
	}
	
	private function getUserAccountControlBadge(): string {
		$uac = $this->getUserAccountControl();
		
		if (in_array($uac, ['512', '544'])) {
			$class = "bg-success";
		} elseif (in_array($uac, ['2', '16', '514', '546', '66050', '66082', '8388608'])) {
			$class = "bg-danger";
		} elseif ($uac === '66048') {
			$class = "bg-warning";
		} else {
			$class = "bg-dark";
		}
		
		return sprintf(
			'<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill %s">%s</span>',
			$class,
			htmlspecialchars($uac)
		);
	}
	
	public function passwordExpiryBadge() {
		// How long ago the password was last changed
		$daysSinceChange = daysSince($this->getPasswordLastSet());
	
		// Policy thresholds
		$warnAfterDays   = setting('ldap_password_warn_age');       // e.g., 365
		$expireAfterDays = setting('ldap_password_disable_age');    // e.g., 395
	
		// How many days remain before expiry
		$daysRemaining = $expireAfterDays - $daysSinceChange;
	
		// Prepare output
		$class   = '';
		$message = '';
	
		if ($daysRemaining <= 1) {
			// Password already expired
			$class   = 'text-bg-danger';
			$message = 'Expired ' . abs($daysRemaining) . ' ' . autoPluralise("day", "days", $daysRemaining) . ' ago';
		} elseif ($daysSinceChange >= $warnAfterDays) {
			// Within the warning window
			$class   = 'text-bg-warning';
			$message = 'Expires in ' . $daysRemaining . ' ' . autoPluralise("day", "days", $daysRemaining);
		} else {
			// Still valid and well within policy
			$class   = 'text-bg-success';
			$message = $daysRemaining . ' ' . autoPluralise("day", "days", $daysRemaining) . ' left';
		}
	
		return "<span class=\"badge {$class}\">{$message}</span>";
	}
	
	public function actionsButton() {
		$output  = "<span class=\"ldap-status\" id=\"status-" . $this->getSAMAccountName() . "\"></span>";
		$output .= "<div class=\"btn-group\" role=\"group\">";
		$output .= "<div class=\"dropdown\">";
		$output .= "<button type=\"button\" class=\"btn btn-sm btn-outline-info dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">Actions</button>";
		
		$output .= "<ul class=\"dropdown-menu\">";
		
		$mailTo = $this->oxford_email;
		
		if (!empty($this->alt_email)) {
			$mailTo .= "?cc=" . $this->alt_email;
		}
		
		$output .= "<li><a class=\"dropdown-item\" href=\"mailto:" . $mailTo . "\">Email</a></li>";
		
		if (in_array($this->getUserAccountControl(), array('512','66048'))) {
			$output .= "<li><a class=\"dropdown-item ldap-toggle-link\" data-username=\"" . $this->getSAMAccountName() . "\" data-action=\"disable\" href=\"#\">Disable " . $this->getSAMAccountName() . " LDAP Account</a></li>";
		} else {
			$output .= "<li><a class=\"dropdown-item ldap-toggle-link\" data-username=\"" . $this->getSAMAccountName() . "\" data-action=\"enable\" href=\"#\">Enable " . $this->getSAMAccountName() . " LDAP Account</a></li>";
			$output .= "<li><a class=\"dropdown-item text-danger ldap-delete-link\" data-username=\"" . $this->getSAMAccountName() . "\" data-action=\"disable\" href=\"#\">Delete LDAP Account</a></li>";

		}
		
		$output .= "</ul>";
		$output .= "</div>";
		$output .= "</div>";
		
		return $output;
	}
}
