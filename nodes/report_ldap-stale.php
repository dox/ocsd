<?php
$ou = "DC=SEH,DC=ox,DC=ac,DC=uk";
$ldapClass = new LDAP();
$person = new Person();

if ($ldapClass) {
  $admin_bind = $ldapClass->ldap_bind();
  $allLDAPUsers = $ldapClass->all_users(LDAP_BASE_DN, true);
}

foreach ($allLDAPUsers AS $ldapUser) {
  $lastpwdset = win_time_to_unix_time($ldapUser['pwdlastset'][0]);
  $ageLimit = date('U', strtotime("4 years ago"));

  //echo $lastlogon . " < " . $ageLimit . "<br />";
  if ($lastpwdset < $ageLimit) {
    $ldapPerson = new LDAPPerson($ldapUser['samaccountname'][0], $ldapUser['mail'][0]);
    $personSearch = new Person($ldapUser['samaccountname'][0]);

    if (strtolower($personSearch->sso_username) == strtolower($ldapPerson->samaccountname)) {
      $tdClass = "";
    } else {
      $tdClass = "table-warning";
    }
    $output  = "<tr>";
    $output .= "<td>" . $ldapPerson->cn . "</td>";
    $output .= "<td class=\"" . $tdClass . "\">" . "<a href=\"index.php?n=persons_unique&cudid=" . $personSearch->cudid . "\">" . $personSearch->sso_username . "</a>" . "</td>";
    $output .= "<td class=\"" . $tdClass . "\">" . "<a href=\"index.php?n=ldap_unique&samaccountname=" . $ldapPerson->samaccountname . "\">" . $ldapPerson->samaccountname . "</a>" . "</td>";
    $output .= "<td>" . $ldapPerson->useraccountcontrolbadge() . "</td>";
    $output .= "<td>" . $ldapPerson->pwdlastsetbadge() . "</td>";
    $output .= "<td>" . makeEmail($ldapPerson->mail) . "</td>";
    $output .= "<td>" . $ldapPerson->actionsButton() . "</td>";
    $output .= "</tr>";

    $tableOutput_enabled[] = $output;
  }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><i class="fas fa-user-friends"></i> Stale LDAP (<?php echo count($tableOutput_enabled);?> records)</h1>
  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-group mr-2">
      <button type="button" class="btn btn-sm btn-outline-secondary">void</button>
      <button type="button" class="btn btn-sm btn-outline-secondary">void</button>
    </div>

    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"><span data-feather="calendar"></span>void</button>
  </div>
</div>

<p>These are  records that exist in the local LDAP.</p>

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
    foreach ($tableOutput_enabled AS $row) {
      echo $row;
    }
    ?>
  </tbody>
</table>
