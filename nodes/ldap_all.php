<?php
$ldapClass = new LDAP();


if (isset($_GET['filter'])) {
  if ($_GET['filter'] == "ldap-no-cud") {
    $personsClass = new Persons;
    $preCheckedUsers = $ldapClass->all_users_enabled();
    $filterDescription = "These are enabled records that exist in the local LDAP, but are not matched against a valid CUD record.";
    foreach ($preCheckedUsers AS $user) {
      $person = $personsClass->search($user['mail'][0], 1);
      if (!isset($person[0]['cudid'])) {
        $person = $personsClass->search($user['samaccountname'][0], 1);
        if (!isset($person[0]['cudid'])) {
          $users[] = $user;
        }
      }
    }
  } elseif ($_GET['filter'] == "cud-no-ldap") {
    $filterDescription = "These are enabled records that exist in CUD, but are not matched against a valid local LDAP record.";
    $personsClass = new Persons();
    $persons = $personsClass->all();

    foreach ($persons AS $person) {
      $ldap_entries = $ldap_connection->query()
  			->where('samaccountname', '=', $person['sso_username'])
  			->orWhere('mail', '=', $person['oxford_email'])
  			->get();

      if (count($ldap_entries) != 1) {
        $users[] = $person;
      }
    }
  } elseif ($_GET['filter'] == "expiring") {
    $users = $ldapClass->expiring_users();
    $filterDescription = "These are enabled records that exist in the local LDAP, but are due to expire as they have not reset their password in over " . pwd_warn_age . " days.";
  } elseif ($_GET['filter'] == "stale") {
    $users = $ldapClass->stale_users(LDAP_BASE_DN, true);
    $filterDescription = "These are all records that exist in the local LDAP, but have not had their password reset in " . (pwd_warn_age*3) . " days.";
  } elseif ($_GET['filter'] == "stale-workstations") {
    $users = $ldapClass->stale_workstations(LDAP_BASE_DN, true);
    $filterDescription = "These are all records for workstations that exist in the local LDAP, but have not had any activity in " . (pwd_warn_age*3) . " days.";
  } elseif ($_GET['filter'] == "all") {
    $filterDescription = "These are all records that exist in the local LDAP.";
    $users = $ldapClass->all_users_enabled();
  } elseif ($_GET['filter'] == "search") {
    $filterDescription = "These are all records that exist in the local LDAP that match the search term '" . $_POST['ldap_search'] . "'.";
    $users = $ldapClass->search_users($_POST['ldap_search']);
    $logInsert = (new Logs)->insert("ldap","success",null,"LDAP Search for <code>" . $_POST['ldap_search'] . "</code> returned " . count($users) . " results (not all of the results were displayable users)");
  } elseif ($_GET['filter'] == "group") {
    $filterDescription = "These are all records that exist in the local LDAP that are in the group '" . $_GET['cn'] . "'.";
    $group = LdapRecord\Models\ActiveDirectory\Group::find($_GET['cn']);
    $users = $group->members()->get();
    //$logInsert = (new Logs)->insert("ldap","success",null,"LDAP Search for <code>" . $_POST['ldap_search'] . "</code> returned " . count($users) . " results (not all of the results were displayable users)");


  //$members = $group->members()->get();
    //printArray($usersForOutput);
  }
}

foreach ($users AS $user) {
  $ldapUser = new LDAPPerson($user['samaccountname'][0]);
  $personsClass = new Persons;

  if (isset($user['samaccountname'][0])) {
    $CUDPerson = $personsClass->search($ldapUser->samaccountname, 2);
    if (count($CUDperson) != 1) {
      $CUDPerson = $personsClass->search($ldapUser->mail, 2);
    }

    if (count($CUDPerson) == 1) {
      $CUDPerson = $CUDPerson[0];
    } else {
      $CUDPerson = "";
    }

    $output  = "<tr>";
    $output .= "<td>" . $ldapUser->cn . "</td>";
    $output .= "<td class=\"" . $tdClass . "\">" . "<a href=\"index.php?n=persons_unique&cudid=" . $CUDPerson['cudid'] . "\">" . $CUDPerson['sso_username'] . "</a>" . "</td>";
    $output .= "<td class=\"" . $tdClass . "\">" . "<a href=\"index.php?n=ldap_unique&samaccountname=" . $ldapUser->samaccountname . "\">" . $ldapUser->samaccountname . "</a>" . "</td>";
    $output .= "<td>" . $ldapUser->useraccountcontrolbadge() . "</td>";
    $output .= "<td>" . $ldapUser->pwdlastsetbadge() . "</td>";
    $output .= "<td>" . makeEmail($ldapUser->mail) . "</td>";
    $output .= "<td>" . $ldapUser->actionsButton($CUDPerson['cudid']) . "</td>";
    $output .= "</tr>";

    $tableOutput[] = $output;
  } else {
    if ($_GET['filter'] == "cud-no-ldap") {
      $sql  = "SELECT * FROM Applications";
      $sql .= " WHERE cudid = '" . $user['cudid'] . "'";
      $dbOutput = $db->query($sql)->fetchAll();

      if ($dbOutput[0]['Stage'] == "Applicant") {
        $badge = " <span class=\"badge bg-orange float-right\">Applicant</span>";
      } else {
        $badge = "";
      }
      $output  = "<tr>";
      $output .= "<td>" . $user['FullName'] . $badge . "</td>";
      $output .= "<td>" . "<a href=\"index.php?n=persons_unique&cudid=" . $user['cudid'] . "\">" . $user['sso_username'] . "</a>" . "</td>";
      $output .= "<td>" . "</td>";
      $output .= "<td>" . "" . "</td>";
      $output .= "<td>" . "" . "</td>";
      $output .= "<td>" . makeEmail($user['oxford_email']) . "</td>";
      $output .= "<td>" . $ldapUser->actionsButton($user['cudid']) . "</td>";
      $output .= "</tr>";

      $tableOutput[] = $output;
    }
  }
}

$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"images/icons.svg#bell\"/></svg> Test Button", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteMealModal\"");
$icons[] = array("class" => "btn-warning", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"images/icons.svg#email\"/></svg> Test Button", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteMealModal\"");

$title = count($users) . autoPluralise(" LDAP Record", " LDAP Records", count($users));
echo displayTitle($title, "Filter: " . $_GET['filter'], $icons);
?>

<p><?php echo $filterDescription; ?></p>

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
