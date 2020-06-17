<?php
$ou = "DC=SEH,DC=ox,DC=ac,DC=uk";
$ldapClass = new LDAP();

//$admin_ldapconn = $ldapClass->ldap_connect();

if ($ldapClass) {
  $admin_bind = $ldapClass->ldap_bind();
  $username_filter = "(sAMAccountName=*)";
  $all_search_results = $ldapClass->ldap_search($ou, $username_filter);
  $all_entries = $ldapClass->ldap_get_entries($all_search_results);
  $allLDAPUsers = $ldapClass->all_users();
}

$persons = new Persons();
$personsAll = $persons->all();

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

    if ($admin_entries['count'] == 1) {
      //exists in LDAP
      if ($admin_entries[0]['useraccountcontrol'][0] == "512" || $admin_entries[0]['useraccountcontrol'][0] == "544") {
        // is active in LDAP
        $ldap_and_cud_tableArray[] = $output;
      } else {
        // is disabled in LDAP
        $disabledLDAP_tableArray[] = $output;
      }
    } else {
      $need_provisioning_tableArray[] = $output;
    }
}

foreach ($allLDAPUsers AS $ldapUser) {
  if (isset($ldapUser['samaccountname'][0]) && isset($ldapUser['mail'][0])) {
    $personSearch = $persons->search($ldapUser['mail'][0]);

    if (count($personSearch) == 1) {

    } else {
      $output  = "<tr>";
      $output .= "<td>" . $ldapUser['cn'][0] . "</td>";
      $output .= "<td class=\"" . $tdClass . "\">" . "<a href=\"index.php?n=ldap_unique&samaccountname=" . $ldapUser['samaccountname'][0] . "\">" . $ldapUser['samaccountname'][0] . "</a>" . "</td>";
      $output .= "<td>" . $ldapUser['samaccountname'][0] . "</td>";
      $output .= "<td>" . $ldapClass->useraccountcontrolbadge($ldapUser['useraccountcontrol'][0]) . "</td>";
      $output .= "<td>" . $ldapClass->pwdlastsetbadge($ldapUser['pwdlastset'][0]) . "</td>";
      $output .= "<td>" . $ldapUser['mail'][0] . "</td>";
      $output .= "<td>" . $ldapClass->actionsButton($ldapUser['samaccountname'][0]) . "</td>";
      $output .= "</tr>";

      if ($ldapUser['useraccountcontrol'][0] == "512") {
        $needRemovingFromLDAP_tableArray[] = $output;
      } else {
        $disabledLDAP_tableArray[] = $output;
      }
    }
  }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><i class="fas fa-user-friends"></i> LDAP Status</h1>
  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-group mr-2">
      <button type="button" class="btn btn-sm btn-outline-secondary">void</button>
      <button type="button" class="btn btn-sm btn-outline-secondary">void</button>
    </div>

    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"><span data-feather="calendar"></span>void</button>
  </div>
</div>

<nav>
	<div class="nav nav-tabs" id="nav-tab" role="tablist">
    <a class="nav-item nav-link active" id="nav-activeaccounts-tab" data-toggle="tab" href="#nav-activeaccounts" role="tab" aria-controls="nav-activeaccounts" aria-selected="false">Matched CUD/LDAP Accounts (<?php echo count($ldap_and_cud_tableArray); ?>)</a>

    <a class="nav-item nav-link" id="nav-provision-tab" data-toggle="tab" href="#nav-provision" role="tab" aria-controls="nav-provision" aria-selected="true">In CUD but not LDAP (<?php echo count($need_provisioning_tableArray); ?>)</a>
    <a class="nav-item nav-link" id="nav-remove-tab" data-toggle="tab" href="#nav-remove" role="tab" aria-controls="nav-remove" aria-selected="true">In LDAP but not CUD (<?php echo count($needRemovingFromLDAP_tableArray); ?>)</a>
    <a class="nav-item nav-link" id="nav-ldapdisabled-tab" data-toggle="tab" href="#nav-ldapdisabled" role="tab" aria-controls="nav-ldapdisabled" aria-selected="true">Disabled LDAP (<?php echo count($disabledLDAP_tableArray); ?>)</a>
	</div>
</nav>

<div class="tab-content" id="nav-tabContent">
  <div class="tab-pane fade show active" id="nav-activeaccounts" role="tabpanel" aria-labelledby="nav-activeaccounts-tab">
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
        foreach ($ldap_and_cud_tableArray AS $row) {
          echo $row;
        }
        ?>
      </tbody>
    </table>
  </div>
  <div class="tab-pane fade" id="nav-provision" role="tabpanel" aria-labelledby="nav-provision-tab">
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
        foreach ($need_provisioning_tableArray AS $row) {
          echo $row;
        }
        ?>
      </tbody>
    </table>
  </div>

  <div class="tab-pane fade" id="nav-remove" role="tabpanel" aria-labelledby="nav-remove-tab">
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
        foreach ($needRemovingFromLDAP_tableArray AS $row) {
          echo $row;
        }
        ?>
      </tbody>
    </table>
  </div>

  <div class="tab-pane fade" id="nav-ldapdisabled" role="tabpanel" aria-labelledby="nav-ldapdisabled-tab">
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
        foreach ($disabledLDAP_tableArray AS $row) {
          echo $row;
        }
        ?>
      </tbody>
    </table>
  </div>
</div>
