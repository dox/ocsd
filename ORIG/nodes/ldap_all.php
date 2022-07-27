<?php





foreach ($users AS $user) {
  $ldapUser = new LDAPPerson($user['samaccountname'][0]);
  $personsClass = new Persons;

  if (isset($user['samaccountname'][0])) {
    $CUDPerson = $personsClass->search($ldapUser->samaccountname, 2);
    if (isset($CUDperson->samaccountname)) {
      $CUDPerson = $personsClass->search($ldapUser->mail, 2);
    }

    if (isset($CUDPerson->samaccountname)) {
      $CUDPerson = $CUDPerson[0];
    } else {
      $CUDPerson = array();
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

    $tableOutput[] = $output;
  } else {
    if ($_GET['filter'] == "cud-no-ldap") {
      $sql  = "SELECT * FROM Applications";
      $sql .= " WHERE cudid = '" . $user['cudid'] . "'";
      $dbOutput = $db->query($sql)->fetchAll();

      if ($dbOutput[0]['Stage'] == "Applicant") {
        $badge = " <span class=\"badge bg-warning float-end\">Applicant</span>";
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


