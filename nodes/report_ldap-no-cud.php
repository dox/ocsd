<?php
$ou = "DC=SEH,DC=ox,DC=ac,DC=uk";
$ldapClass = new LDAP();

if ($ldapClass) {
  $admin_bind = $ldapClass->ldap_bind();
  $allLDAPUsers = $ldapClass->all_users();
}

foreach ($allLDAPUsers AS $ldapUser) {


  if (isset($ldapUser['samaccountname'][0]) && isset($ldapUser['mail'][0])) {
    $filter = array('api_token' => api_token, 'filter' => 'search', 'searchterm' => $ldapUser['mail'][0], 'searchlimit' => '1');
    $personsJSON = api_decode("person", "read", $filter);

    if ($personsJSON->count == 1) {
    	$personJSON = $personsJSON->body[0];
    } else {
      $personJSON = "";
    }

    if ($personsJSON->count != 1) {
      if (strtolower($personJSON->sso_username) == strtolower($ldapUser['samaccountname'][0])) {
        $tdClass = "";
      } else {
        $tdClass = "table-warning";
      }

      $output  = "<tr>";
      $output .= "<td>" . $ldapUser['cn'][0] . "</td>";
      $output .= "<td class=\"" . $tdClass . "\">" . "<a href=\"index.php?n=person_unique&cudid=" . $personJSON->cudid . "\">" . $personJSON->sso_username . "</a>" . "</td>";
      $output .= "<td class=\"" . $tdClass . "\">" . "<a href=\"index.php?n=ldap_unique&samaccountname=" . $ldapUser['samaccountname'][0] . "\">" . $ldapUser['samaccountname'][0] . "</a>" . "</td>";
      $output .= "<td>" . "useraccountcontrolbadge()" . "</td>";
      $output .= "<td>" . "pwdlastsetbadge()" . "</td>";
      $output .= "<td>" . makeEmail($ldapUser['mail'][0]) . "</td>";
      $output .= "<td>" . "actionsButton()" . "</td>";
      $output .= "</tr>";

      if (in_array($ldapUser['useraccountcontrol'][0], array("512", "544"))) {
        $tableOutput[] = $output;
      }
    }
  }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><i class="fas fa-user-friends"></i> LDAP no CUD (<?php echo count($tableOutput);?> records)</h1>
  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-group mr-2">
      <button type="button" class="btn btn-sm btn-outline-secondary">void</button>
      <button type="button" class="btn btn-sm btn-outline-secondary">void</button>
    </div>

    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"><span data-feather="calendar"></span>void</button>
  </div>
</div>

<p>These are enabled records that exist in the local LDAP, but are not matched against a valid CUD record.</p>

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
    foreach ($tableOutput AS $row) {
      echo $row;
    }
    ?>
  </tbody>
</table>
