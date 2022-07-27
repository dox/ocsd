<?php
$ldapClass = new LDAP();





?>

<table class="table">
  <thead>
	<tr>
	  <th scope="col">Full Name</th>
	  <th scope="col">SSO</th>
	  <th scope="col">LDAP</th>
	  <th scope="col">Account Control</th>
	  <th scope="col">pwdlastset</th>
	  <th scope="col">Email</th>
	  <th scope="col">Actions</th>
	</tr>
  </thead>
  <tbody>
	<?php
	$needle = "CN=SCR,OU=SEH Groups,DC=SEH,DC=ox,DC=ac,DC=uk";
	
	foreach ($users AS $ldapUser) {
		
		
		$ldapUser = new LDAPPerson($ldapUser['samaccountname'][0], $ldapUser['mail'][0]);
		
		$scr = "";
		if (is_array($ldapUser->memberof) && in_array($needle, $ldapUser->memberof)) {
			$scr = " <span class=\"badge bg-secondary\">SCR</span>";
		}
		
	  $output  = "<tr>";
	  $output .= "<td>" . $ldapUser->cn . "</td>";
	  $output .= "<td>" . "<a href=\"index.php?n=persons_unique&cudid=" . $CUDPerson['cudid'] . "\">" . $CUDPerson['sso_username'] . "</a>" . "</td>";
	  $output .= "<td>" . "<a href=\"index.php?n=ldap_unique&samaccountname=" . $ldapUser->samaccountname . "\">" . $ldapUser->samaccountname . "</a>" . $scr . "</td>";
	  $output .= "<td>" . $ldapUser->useraccountcontrolbadge() . "</td>";
	  $output .= "<td>" . $ldapUser->pwdlastsetbadge() . "</td>";
	  $output .= "<td>" . makeEmail($ldapUser->mail) . "</td>";
	  $output .= "<td>" . $ldapUser->actionsButton($CUDPerson['cudid']) . "</td>";
	  $output .= "</tr>";
	  
	  echo $output;
	}
	?>
  </tbody>
</table>