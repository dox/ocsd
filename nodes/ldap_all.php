<?php
$ldapClass = new LDAP();
if ($ldapClass) {
  $admin_bind = $ldapClass->ldap_bind();
}

if (isset($_GET['filter'])) {
	if ($_GET['filter'] == "ldap-no-cud") {
    $users = $ldapClass->all_users(LDAP_BASE_DN, false);
    $filterDescription = "These are enabled records that exist in the local LDAP, but are not matched against a valid CUD record.";
    foreach ($users AS $user) {
      $personsClass = new Persons;
      //printArray($user);
      $person = $personsClass->search($user['mail'][0], 1);

      if (count($person) == 1) {
      } else {
        $person = $personsClass->search($user['samaccountname'][0], 1);
        if (count($person) == 1) {
        } else {
          $usersForOutput[] = $user;
        }
      }
    }
	} elseif ($_GET['filter'] == "cud-no-ldap") {
    $filterDescription = "These are enabled records that exist in CUD, but are not matched against a valid local LDAP record.";
    $personsClass = new Persons();
    $persons = $personsClass->all(LDAP_BASE_DN, false);

    foreach ($persons AS $person) {
      $ldapUser = new LDAPPerson($person['sso_username'], $person['oxford_email']);
      //printArray($ldapUser);
      if (!isset($ldapUser->samaccountname)) {
        $usersForOutput[] = $person;
      }
    }
	} elseif ($_GET['filter'] == "expiring") {
    $users = $ldapClass->expiring_users(LDAP_BASE_DN);
    $filterDescription = "These are enabled records that exist in the local LDAP, but are due to expire as they have not reset their password in over " . pwd_warn_age . " days.";
    foreach ($users AS $user) {
      $usersForOutput[] = $user;
    }
	} elseif ($_GET['filter'] == "stale") {
    $users = $ldapClass->stale_users(LDAP_BASE_DN, true);
    $filterDescription = "These are all records that exist in the local LDAP, but have not had their password reset in " . (pwd_warn_age*2) . " days.";
    foreach ($users AS $user) {
      //lookup user in CUD
      $usersForOutput[] = $user;
    }
	} elseif ($_GET['filter'] == "all") {
    $filterDescription = "These are all records that exist in the local LDAP.";
    $users = $ldapClass->all_users(LDAP_BASE_DN, true);
    foreach ($users AS $user) {
      $usersForOutput[] = $user;
    }
	} elseif ($_GET['filter'] == "search") {
    $filterDescription = "These are all records that exist in the local LDAP that match the search term '" . $_POST['ldap_search'] . "'.";
    $users = $ldapClass->search_users(LDAP_BASE_DN, true, $_POST['ldap_search']);
    $logInsert = (new Logs)->insert("ldap","success",null,"LDAP Search for <code>" . $_POST['ldap_search'] . "</code> returned " . count($users) . " results (not all of the results were displayable users)");
    foreach ($users AS $user) {
      $usersForOutput[] = $user;
    }
	}
}

foreach ($usersForOutput AS $user) {
  $ldapUser = new LDAPPerson($user['samaccountname'][0]);

  if (isset($ldapUser->samaccountname)) {
    $output  = "<tr>";
    $output .= "<td>" . $ldapUser->cn . "</td>";
    $output .= "<td class=\"" . $tdClass . "\">" . "<a href=\"index.php?n=person_unique&cudid=" . $cudid . "\">" . $sso_username . "</a>" . "</td>";
    $output .= "<td class=\"" . $tdClass . "\">" . "<a href=\"index.php?n=ldap_unique&samaccountname=" . $ldapUser->samaccountname . "\">" . $ldapUser->samaccountname . "</a>" . "</td>";
    $output .= "<td>" . $ldapUser->useraccountcontrolbadge() . "</td>";
    $output .= "<td>" . $ldapUser->pwdlastsetbadge() . "</td>";
    $output .= "<td>" . makeEmail($ldapUser->mail) . "</td>";
    $output .= "<td>" . $ldapUser->actionsButton() . "</td>";
    $output .= "</tr>";

    $tableOutput[] = $output;
  } else {
    if ($_GET['filter'] == "cud-no-ldap") {
      $output  = "<tr>";
      $output .= "<td>" . $user['FullName'] . "</td>";
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
?>
<div class="content">
	<div class="container-xl">
		<!-- Page title -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col-auto">
					<!-- Page pre-title -->
					<div class="page-pretitle">
						Filter: <?php echo $_GET['filter']; ?>
					</div>
					<h2 class="page-title">
						<span id="ldap_count"><?php echo count($tableOutput); ?></span> LDAP <?php echo autoPluralise(" Record", " Records", count($tableOutput)); ?>
					</h2>
				</div>

				<div class="row">
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
        </div>
      </div>
    </div>
  </div>
</div>
