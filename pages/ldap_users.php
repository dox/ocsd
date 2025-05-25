<?php
$pwdWarnAge = date('Y-m-d', strtotime(setting('ldap_password_warn_age') .  ' days ago'));
$pwdDisableAge = date('Y-m-d', strtotime(setting('ldap_password_disable_age') .  ' days ago'));
$accountExpiredAge = date('Y-m-d', strtotime(setting('ldap_expired_age') .  ' days ago'));

$allowedFilters = [
	'test' => [
		'samaccountname' => [
			'operator' => '=',
			'value' => 'breakspear'
		]
	],
	'ldap-no-cud' => [
		'objectClass' => [
			'operator' => '!=',
			'value' => 'computer'
		],
		'pwdLastSet' => [
			'operator' => '<=',
			'value' => convertDateToWinTime($accountExpiredAge)
		],
	],
	'expired' => [
		'objectClass' => [
			'operator' => '!=',
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
	'search' => [
		'objectClass' => [
			'operator' => '!=',
			'value' => 'group'
		],
		'cn' => [
			'operator' => '=',
			'value' => '*' . $_POST['ldap_search'] . '*'
		]
	],
	'group' => [
		'memberOf' => [
			'operator' => '=',
			'value' => $_GET['ldap_search']
		]
	]
];

$filter = $_GET['filter'] ?? null;

$ldapUsers = $ldap->findByFilters($allowedFilters[$filter]);

if ($filter == "ldap-no-cud") {
	$i = 0;
	foreach ($ldapUsers AS $ldapUser) {
		$lookups = array_filter([
			'sso_username'		=> $ldapUser['samaccountname'][0] ?? null,
			'MiFareID'          => $ldapUser['pager'][0] ?? null,
			'oxford_email'		=> $ldapUser['mail'][0] ?? null
		]);
		
		$person = new Person($lookups);
		if (!empty($person->cudid)) {
			//echo $person->FullName . " matched " .  $user['mail'][0] . " so UNSETTING<br />";
			unset($ldapUsers[$i]);
		} else {
			//echo "no match for " .  $user['mail'][0] . "<br />";
		}
		
		$i++;
	}
}

$data = array(
		'icon'		=> 'person-fill-lock',
		'title'		=>  count($ldapUsers) . autoPluralise(" User", " Users", count($ldapUsers)),
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
		foreach ($ldapUsers as $ldapUser) {
			$record = $ldap->findUser($ldapUser['samaccountname'][0]);
			
			if ($record) {
				$ldapUser = new LdapUserWrapper($record);
			} else {
				echo $ldapUser['samaccountname'][0];
				$ldapUser = null;
			}

			$lookups = array_filter([
				'sso_username'		=> $ldapUser->getSAMAccountName() ?? null,
				'MiFareID'          => $ldapUser->getPager() ?? null,
				'oxford_email'		=> $ldapUser->getEmail() ?? null
			]);
			
			$person = new Person($lookups);
			
			$pwdLastSet = null;
			if ($ldapUser->pwdlastset > 0) {
				$pwdLastSet = $ldapUser->pwdlastset->toDateTimeString();
			}
			
			$output  = "<th scope=\"row\">" . $person->ssoButton() . "</a></th>";
			$output .= "<td>" . $ldapUser->getLDAPButton() . "</td>";
			$output .= "<td>" . $ldapUser->getSN() . "</td>";
			$output .= "<td>" . $ldapUser->getGivenname() . "</td>";
			$output .= "<td>" . $ldapUser->getEmail() . "</td>";
			$output .= "<td>" . $ldapUser->passwordExpiryBadge() . "<span class=\"float-end\">" . $ldapUser->actionsButton() . "</span></td>";
			$output .= "</tr>";
			
			echo $output;
		}
		?>
	</tbody>
</table>
	
<div class="row row-cols-1 row-cols-md-4 g-4">
<?php
foreach ($personsAll as $person) {
	$person = new Person($person['cudid']);
	echo $person->card();
}
?>
</div>