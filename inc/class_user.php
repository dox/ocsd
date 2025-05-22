<?php
class User {
	private string $username;
	private Ldap $ldap;

	public function __construct(Ldap $ldap) {
		$this->ldap = $ldap;

		// Restore from session if present
		if (isset($_SESSION['username'])) {
			$this->username = $_SESSION['username'];
		}
	}

	public function authenticate(string $username, string $password): bool {
		if ($this->ldap->authenticate($username, $password)) {
			$this->username = $username;
			$_SESSION['username'] = $username;
			$_SESSION['logged_in'] = true;
			return true;
		} else {
			$_SESSION['logged_in'] = false;
			return false;
		}
	}

	public function isLoggedIn(): bool {
		return ($_SESSION['logged_in'] ?? false) === true;
	}

	public function getUsername(): ?string {
		return $this->username ?? null;
		//return $_SESSION['username'] ?? null;
	}

	public function logout(): void {
		$_SESSION = [];
		session_destroy();
	}
}
