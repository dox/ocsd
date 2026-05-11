<?php
$pwdWarnAge = date('Y-m-d', strtotime(setting('ldap_password_warn_age') .  ' days ago'));
$pwdDisableAge = date('Y-m-d', strtotime(setting('ldap_password_disable_age') .  ' days ago'));
$accountExpiredAge = date('Y-m-d', strtotime(setting('ldap_expired_age') .  ' days ago'));

$allowedFilters = [
	'expired' => [
		'objectClass' => [
			'operator' => '=',
			'value' => 'computer'
		],
		'pwdLastSet' => [
			'operator' => '<=',
			'value' => convertDateToWinTime($pwdDisableAge)
		],
		'useraccountcontrol' => [
			'operator' => '|',
			'value' => '512|544'
		]
	],
	'expiring' => [
		'objectClass' => [
			'operator' => '!=',
			'value' => 'computer'
		],
		'pwdLastSet' => [
			'operator' => '<=',
			'value' => convertDateToWinTime($pwdWarnAge)
		],
		'useraccountcontrol' => [
			'operator' => '|',
			'value' => '512|544'
		]
	],
	'expiring-workstations' => [
		'objectClass' => [
			'operator' => '=',
			'value' => 'computer'
		],
		'pwdLastSet' => [
			'operator' => '<=',
			'value' => convertDateToWinTime($accountExpiredAge)
		],
	]
];

$filter = $_GET['filter'] ?? null;

$ldapComputers = $ldap->findByFilters($allowedFilters[$filter]) ?: [];

$data = array(
		'icon'		=> 'person-fill-lock',
		'title'		=> count($ldapComputers) . " LDAP Computers",
		'subtitle'	=> 'Filter: ' . $filter
);
echo pageTitle($data);
?>

<table class="table">
	<thead>
		<tr>
			<th scope="col">SSO</th>
			<th scope="col">LDAP</th>
			<th scope="col">Last Name</th>
			<th scope="col">First Name</th>
			<th scope="col">Email</th>
			<th scope="col">Password Last Set</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($ldapComputers as $ldapComputer) {
			if (!isset($ldapComputer['samaccountname'][0])) {
				continue;
			}

			$record = $ldap->findComputer($ldapComputer['samaccountname'][0]);
			if (!$record) {
				continue;
			}

			$ldapComputer = new LdapComputerWrapper($record);
			$pwdLastSet = $ldapComputer->getPasswordLastSet();

			$output  = "<tr>";
			$output .= "<th scope=\"row\">" . htmlspecialchars($ldapComputer->getSAMAccountName() ?? '') . "</th>";
			$output .= "<td>" . ($ldapComputer->getLDAPButton() ?? '') . "</td>";
			$output .= "<td>" . htmlspecialchars($ldapComputer->getSN() ?? '') . "</td>";
			$output .= "<td>" . htmlspecialchars($ldapComputer->getGivenname() ?? '') . "</td>";
			$output .= "<td>" . htmlspecialchars($ldapComputer->getEmail() ?? '') . "</td>";
			$output .= "<td>" . htmlspecialchars($pwdLastSet ?? '') . "<span class=\"float-end\">" . $ldapComputer->actionsButton() . "</span></td>";
			$output .= "</tr>";

			echo $output;
		}
		?>
	</tbody>
</table>
