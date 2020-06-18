<?php
$ou = "DC=SEH,DC=ox,DC=ac,DC=uk";
$ldapClass = new LDAP();

$persons = new Persons();
$personsAll = $persons->all();

if ($ldapClass) {
  $admin_bind = $ldapClass->ldap_bind();
  $username_filter = "(sAMAccountName=*)";
  $all_search_results = $ldapClass->ldap_search($ou, $username_filter);
  $all_entries = $ldapClass->ldap_get_entries($all_search_results);
  $allLDAPUsers = $ldapClass->all_users(LDAP_BASE_DN, true);
}

foreach ($personsAll AS $person) {
  	$filter = "(|(sAMAccountName=" . $person['sso_username'] . ")(mail=" . $person['oxford_email'] . "))";
  	$admin_search_results = $ldapClass->ldap_search($ou, $filter);
  	$admin_entries = $ldapClass->ldap_get_entries($admin_search_results);

  	if ($admin_entries['count'] == 1) {
      if (strtolower($person['sso_username']) == strtolower($admin_entries[0]['samaccountname'][0])) {
        $tdClass = "";
      } else {
        $tdClass = "table-warning";
      }
    } else {
    }

    $output  = "<tr>";
    $output .= "<td>" . $person['FullName'] . "</td>";
    $output .= "<td>" . "<a href=\"index.php?n=persons_unique&cudid=" . $person['cudid'] . "\">" . $person['sso_username'] . "</a>" . "</td>";

    $output .= "<td class=\"" . $tdClass . "\">" . "<a href=\"index.php?n=ldap_unique&samaccountname=" . $admin_entries[0]['samaccountname'][0] . "\">" . $admin_entries[0]['samaccountname'][0] . "</a> " . "</td>";
    $output .= "<td>" . $ldapClass->useraccountcontrolbadge($admin_entries[0]['useraccountcontrol'][0]) . "</td>";
    $output .= "<td>" . $ldapPWDLastSet . "</td>";
    $output .= "<td>" . $person['oxford_email'] . "</td>";
    $output .= "<td>" . $ldapClass->actionsButton($person['sso_username']) . "</td>";
    $output .= "</tr>";

    if ($admin_entries['count'] != 1) {
      $tableOutput[] = $output;
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><i class="fas fa-user-friends"></i> CUD no LDAP (<?php echo count($tableOutput);?> records)</h1>
  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-group mr-2">
      <button type="button" class="btn btn-sm btn-outline-secondary">void</button>
      <button type="button" class="btn btn-sm btn-outline-secondary">void</button>
    </div>

    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"><span data-feather="calendar"></span>void</button>
  </div>
</div>

<p>These are records that exist on CUD, but are not matched against an account in the local LDAP.</p>

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
