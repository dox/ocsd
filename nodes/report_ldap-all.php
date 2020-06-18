<?php
$ou = "DC=SEH,DC=ox,DC=ac,DC=uk";
$ldapClass = new LDAP();
$person = new Person();

if ($ldapClass) {
  $admin_bind = $ldapClass->ldap_bind();
  $username_filter = "(sAMAccountName=*)";
  $all_search_results = $ldapClass->ldap_search($ou, $username_filter);
  $all_entries = $ldapClass->ldap_get_entries($all_search_results);
  $allLDAPUsers = $ldapClass->all_users(LDAP_BASE_DN, true);
}

foreach ($allLDAPUsers AS $ldapUser) {
  if (isset($ldapUser['samaccountname'][0]) && isset($ldapUser['mail'][0])) {
    //$personSearch = $person->search($ldapUser['mail'][0]);
    $personSearch = new Person($ldapUser['samaccountname'][0]);

    if (strtolower($personSearch->sso_username) == strtolower($ldapUser['samaccountname'][0])) {
      $tdClass = "";
    } else {
      $tdClass = "table-warning";
    }
    $output  = "<tr>";
    $output .= "<td>" . $ldapUser['cn'][0] . "</td>";
    $output .= "<td class=\"" . $tdClass . "\">" . "<a href=\"index.php?n=persons_unique&cudid=" . $personSearch->cudid . "\">" . $personSearch->sso_username . "</a>" . "</td>";
    $output .= "<td class=\"" . $tdClass . "\">" . "<a href=\"index.php?n=ldap_unique&samaccountname=" . $ldapUser['samaccountname'][0] . "\">" . $ldapUser['samaccountname'][0] . "</a>" . "</td>";
    $output .= "<td>" . $ldapClass->useraccountcontrolbadge($ldapUser['useraccountcontrol'][0]) . "</td>";
    $output .= "<td>" . $ldapClass->pwdlastsetbadge($ldapUser['pwdlastset'][0]) . "</td>";
    $output .= "<td>" . $ldapUser['mail'][0] . "</td>";
    $output .= "<td>" . $ldapClass->actionsButton($ldapUser['samaccountname'][0]) . "</td>";
    $output .= "</tr>";

    if ($ldapUser['useraccountcontrol'][0] == "512" || $ldapUser['useraccountcontrol'][0] == "544") {
      $tableOutput_enabled[] = $output;
    } else {
      $tableOutput_disabled[] = $output;
    }
  }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><i class="fas fa-user-friends"></i> LDAP (Enabled: <?php echo count($tableOutput_enabled);?> records, Disabled: <?php echo count($tableOutput_disabled);?> records)</h1>
  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-group mr-2">
      <button type="button" class="btn btn-sm btn-outline-secondary">void</button>
      <button type="button" class="btn btn-sm btn-outline-secondary">void</button>
    </div>

    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"><span data-feather="calendar"></span>void</button>
  </div>
</div>

<p>These are  records that exist in the local LDAP.</p>

<nav>
	<div class="nav nav-tabs" id="nav-tab" role="tablist">
    <a class="nav-item nav-link active" id="nav-enabled-tab" data-toggle="tab" href="#nav-enabled" role="tab" aria-controls="nav-enabled" aria-selected="false">Enabled LDAP Accounts (<?php echo count($tableOutput_enabled); ?>)</a>
    <a class="nav-item nav-link" id="nav-disabled-tab" data-toggle="tab" href="#nav-disabled" role="tab" aria-controls="nav-disabled" aria-selected="true">Disabled LDAP Accounts (<?php echo count($tableOutput_disabled); ?>)</a>
	</div>
</nav>

<div class="tab-content" id="nav-tabContent">
  <div class="tab-pane fade show active" id="nav-enabled" role="tabpanel" aria-labelledby="nav-enabled-tab">
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
  </div>
  <div class="tab-pane fade" id="nav-disabled" role="tabpanel" aria-labelledby="nav-disabled-tab">
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
        foreach ($tableOutput_disabled AS $row) {
          echo $row;
        }
        ?>
      </tbody>
    </table>
  </div>
</div>
