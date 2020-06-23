<?php
$ou = "DC=SEH,DC=ox,DC=ac,DC=uk";
$ldapClass = new LDAP();
$person = new Person();

if ($ldapClass) {
  $admin_bind = $ldapClass->ldap_bind();
  $username_filter = "(sAMAccountName=*)";
  $all_search_results = $ldapClass->ldap_search($ou, $username_filter);
  $all_entries = $ldapClass->ldap_get_entries($all_search_results);
  $allLDAPUsers = $ldapClass->all_users();
}

foreach ($allLDAPUsers AS $ldapUser) {
  $ldapPerson = new LDAPPerson($ldapUser['samaccountname'][0]);

  if (isset($ldapUser['samaccountname'][0]) && isset($ldapUser['mail'][0])) {
    $personSearch = $person->search($ldapUser['mail'][0]);
    //$personSearch = new Person($ldapUser['samaccountname'][0]);

    if (count($personSearch) != 1) {
      if (strtolower($personSearch->sso_username) == strtolower($ldapPerson->samaccountname)) {
        $tdClass = "";
      } else {
        $tdClass = "table-warning";
      }

      $output  = "<tr>";
      $output .= "<td>" . $ldapUser['cn'][0] . "</td>";
      $output .= "<td class=\"" . $tdClass . "\">" . "<a href=\"index.php?n=person_unique&cudid=" . $personSearch->cudid . "\">" . $personSearch->cudid . "</a>" . "</td>";
      $output .= "<td class=\"" . $tdClass . "\">" . "<a href=\"index.php?n=ldap_unique&samaccountname=" . $ldapPerson->samaccountname . "\">" . $ldapPerson->samaccountname . "</a>" . "</td>";
      $output .= "<td>" . $ldapPerson->useraccountcontrolbadge() . "</td>";
      $output .= "<td>" . $ldapPerson->pwdlastsetbadge() . "</td>";
      $output .= "<td>" . $ldapPerson->emailAddress() . "</td>";
      $output .= "<td>" . $ldapPerson->actionsButton() . "</td>";
      $output .= "</tr>";

      if ($ldapUser['useraccountcontrol'][0] == "512") {
        $tableOutput[] = $output;
      } else {
        $disabledLDAP_tableArray[] = $output;
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
