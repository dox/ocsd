<?php
use LdapRecord\Models\Model;

class LdapComputerWrapper {
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
	
	public function getGivenname(): ?string {
		return $this->entry->getFirstAttribute('givenname');
	}
	
	public function getSN(): ?string {
		return $this->entry->getFirstAttribute('sn');
	}

	public function getDisplayName(): ?string {
		return $this->entry->getFirstAttribute('displayname');
	}

	public function getPasswordLastSet(): ?string {
		return $this->entry->getFirstAttribute('pwdlastset');
	}

	public function getLDAPButton(): ?string {
		$sam = $this->getSAMAccountName();

		if ($sam) {
			return sprintf(
				'<span class="btn btn-light position-relative">%s</span>',
				htmlspecialchars($sam)
			);
		}

		return null;
	}

	public function actionsButton(): string {
		$sam = $this->getSAMAccountName();

		if (!$sam) {
			return '';
		}

		return sprintf(
			'<span class="ldap-status" id="status-%1$s"></span><div class="btn-group" role="group"><div class="dropdown"><button type="button" class="btn btn-sm btn-outline-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Actions</button><ul class="dropdown-menu"><li><a class="dropdown-item text-danger ldap-delete-link" data-username="%1$s" href="#">Delete LDAP Computer</a></li></ul></div></div>',
			htmlspecialchars($sam)
		);
	}
}
