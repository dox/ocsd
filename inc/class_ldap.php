<?php

use LdapRecord\Container;
use LdapRecord\Connection;
use LdapRecord\Auth\BindException;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;
use LdapRecord\Models\ActiveDirectory\Computer as LdapComputer;
use LdapRecord\Models\ActiveDirectory\Group;

class Ldap {
	private Connection $connection;
	private string $lastError = '';

	public function __construct() {
		$this->connect();
	}

	private function connect(): void {
		if (empty(LDAP_BIND_PASSWORD)) {
			$this->lastError = 'LDAP bind password is empty.';
			return;
		}
		
		try {
			$this->connection = new Connection([
				'hosts'            => LDAP_SERVER,
				'base_dn'          => LDAP_BASE_DN,
				'username'         => LDAP_BIND_DN,
				'password'         => LDAP_BIND_PASSWORD,
				'port'             => LDAP_PORT,
				'use_ssl'          => false,
				'use_tls'          => LDAP_STARTTLS,
				'version'          => 3,
				'timeout'          => 5,
				'follow_referrals' => false,
			]);

			Container::setDefaultConnection('ldap');
			Container::addConnection($this->connection, 'ldap');

		} catch (BindException | \Exception $e) {
			$this->lastError = "Connection error: " . $e->getMessage();
		}
	}

	private function setError(string $message): void {
		$this->lastError = $message;
	}

	public function getLastError(): string {
		return $this->lastError;
	}

	public function authenticate(string $username, string $password): bool {
		global $log;

		$user = $this->findUser($username);
		
		if (!$user) {
			$this->logAttempt($log, $username, 'not found in LDAP.');
			$this->setError('Username not found or password is incorrect.');
			return false;
		}

		if (!$this->connection->auth()->attempt($user['distinguishedname'][0], $password, true)) {
			$this->logAttempt($log, $username, 'did not match password in LDAP.');
			$this->setError('Username not found or password is incorrect.');
			return false;
		}

		$groups = $user->getAttribute('memberof', []);
		if (array_intersect(array_map('strtolower', LDAP_ALLOWED_DN), array_map('strtolower', $groups))) {
			$_SESSION['logged_in'] = true;
			$_SESSION['username'] = $username;
			$this->logAttempt($log, $username, 'authenticated and logged in.', 'success');
			return true;
		} else {
			$this->logAttempt($log, $username, 'authenticated but was not in allowed group(s).');
			$this->setError('You do not have access to this service.');
			return false;
		}
	}

	private function logAttempt($log, $username, $desc, $result = 'warning') {
		$log->create([
			'type' => 'login',
			'result' => $result,
			'description' => "$username $desc"
		]);
	}

	public function findUser(string $username) {
		return LdapUser::where('samaccountname', '=', $username)->first() ?: false;
	}
	
	public function findComputer(string $username) {
		return LdapComputer::where('samaccountname', '=', $username)->first() ?: false;
	}

	public function findUserFromLookups(array $lookups) {
		foreach ($lookups as $field => $value) {
			if ($value) {
				$record = LdapUser::where($field, '=', $value)->first();
				if ($record) return $record;
			}
		}
		return false;
	}

	public function findByFilters(array $filters): ?array {
		try {
			$query = $this->connection->query();
			$ldapFilter = $this->buildFilter($filters);
			$query->rawFilter($ldapFilter);
			return $query->get() ?: null;
		} catch (\Exception $e) {
			$this->setError($e->getMessage());
			return null;
		}
	}
	
	public function disableAccount($user) {
		global $log;
		// Get current UAC (userAccountControl)
		$currentValue = (int) $user->getFirstAttribute('userAccountControl');
	
		// Set the DISABLED flag (bit 2 / 0x2 / value 0x0202 = 514)
		$disabledValue = $currentValue | 0x2;
	
		$user->setAttribute('userAccountControl', $disabledValue);
		$user->save();
		
		$logData = [
			'type' => 'ldap',
			'result'   => 'success',
			'description' => 'Disabled LDAP account: ' . $user
		];
		$log->create($logData);
	}
	
	public function enableAccount($user) {
		global $log;
		
		// Get current UAC
		$currentValue = (int) $user->getFirstAttribute('userAccountControl');
	
		// Unset the DISABLED flag
		$enabledValue = $currentValue & ~0x2;
	
		$user->setAttribute('userAccountControl', $enabledValue);
		$user->save();
		
		$logData = [
			'type' => 'ldap',
			'result'   => 'success',
			'description' => 'Enabled LDAP account: ' . $user
		];
		$log->create($logData);
	}
	
	public function create(array $attributes): bool {
		global $log;
	
		$user = (new LdapUser)->inside(LDAP_BASE_DN);
		
		$user->cn = $attributes['cn'];
		$user->samaccountname = $attributes['samaccountname'];
		$user->userprincipalname = $attributes['userprincipalname'];
		$user->displayname = $attributes['displayname'] ?? $attributes['cn'];
		$user->givenname = $attributes['givenname'] ?? null;
		$user->sn = $attributes['sn'] ?? null;
		$user->mail = $attributes['mail'] ?? null;
		$user->description = $attributes['description'] ?? null;
		$user->pager = $attributes['pager'] ?? null;
		$user->unicodePwd = $attributes['password'];
		
		$user->save;
		
		$user->refresh();
		
		// Enable the user.
		$user->userAccountControl = 512;
		
		try {
			$user->save();
			return true;
		} catch (\LdapRecord\LdapRecordException $e) {
			// Failed saving user.
		}

	}
	
	public function deleteAccount($user) {
		global $log;
	
		try {
			$user->delete();
	
			$logData = [
				'type' => 'ldap',
				'result' => 'warning',
				'description' => 'Deleted LDAP account: ' . $user
			];
		} catch (\Exception $e) {
			$logData = [
				'type' => 'ldap',
				'result' => 'danger',
				'description' => 'Failed to delete LDAP account: ' . $user . ': ' . $e->getMessage()
			];
		}
	
		$log->create($logData);
	}

	private function buildFilter(array $filters): string {
		$extraFilters = [];
	
		foreach ($filters as $attribute => $conditions) {
			if (strtoupper($attribute) === 'OR' && is_array($conditions)) {
				$orParts = [];
				foreach ($conditions as $cond) {
					$attr = $cond['attribute'];
					$operator = $cond['operator'] ?? '=';
					$value = $cond['value'];
	
					if ($operator === '>') {
						$operator = '>=';
						if (is_numeric($value)) $value += 1;
					} elseif ($operator === '<') {
						$operator = '<=';
						if (is_numeric($value)) $value -= 1;
					}
	
					$orParts[] = $this->formatLdapCondition($attr, $operator, $value);
				}
				$extraFilters[] = '(|' . implode('', $orParts) . ')';
			} else {
				// Regular single-attribute case
				$operator = $conditions['operator'] ?? '=';
				$value = $conditions['value'];
	
				if ($operator === '>') {
					$operator = '>=';
					if (is_numeric($value)) $value += 1;
				} elseif ($operator === '<') {
					$operator = '<=';
					if (is_numeric($value)) $value -= 1;
				}
	
				if ($operator === '|') {
					$parts = array_map(fn($val) => "($attribute=" . trim($val) . ")", explode('|', $value));
					$extraFilters[] = '(|' . implode('', $parts) . ')';
				} elseif ($operator === '!=') {
					$extraFilters[] = "(!($attribute=$value))";
				} else {
					$extraFilters[] = "($attribute$operator$value)";
				}
			}
		}
	
		return '(&' . implode('', $extraFilters) . ')';
	}
	
	private function formatLdapCondition(string $attribute, string $operator, $value): string {
		switch ($operator) {
			case '=':
				return "({$attribute}={$value})";
			case '!=':
				return "(!({$attribute}={$value}))";
			case '>=':
			case '<=':
				return "({$attribute}{$operator}{$value})";
			default:
				throw new \InvalidArgumentException("Unsupported operator: {$operator}");
		}
	}

	
	public function getGroupMembers(string $groupName): array {
		try {
			$group = Group::where('cn', '=', $groupName)->first();
	
			if (!$group) {
				$this->setError("Group '$groupName' not found.");
				return [];
			}
	
			// Fetch members using relationship
			$members = $group->members()->get();
	
			return $members->all(); // return as array
		} catch (\Exception $e) {
			$this->setError("Error retrieving group members: " . $e->getMessage());
			return [];
		}
	}
}
