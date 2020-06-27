<?php
$ou = "DC=SEH,DC=ox,DC=ac,DC=uk";
$ldapClass = new LDAP();

$filter = array('api_token' => api_token, 'filter' => 'all');
$personsJSON = api_decode("person", "read", $filter);
$personsAll = $personsJSON->body;

foreach ($personsAll AS $person) {
  $ldapPerson = new LDAPPerson($person->sso_username, $person->oxford_email);

  if (isset($ldapPerson->samaccountname)) {
    if (strtolower($person->sso_username) == strtolower($ldapPerson->samaccountname)) {
      $tdClass = "";
    } else {
      $tdClass = "table-warning";
    }
  } else {
    $tdClass = "";
  }

  $output  =  "<tr>";
  $output .= "<td>" . $person->FullName . "</td>";
  $output .= "<td>" . "<a href=\"index.php?n=persons_unique&cudid=" . $person->cudid . "\">" . $person->sso_username . "</a>" . "</td>";

  $output .= "<td class=\"" . $tdClass . "\">" . "<a href=\"index.php?n=ldap_unique&samaccountname=" . $ldapPerson->samaccountname . "\">" . $ldapPerson->samaccountname . "</a> " . "</td>";
  $output .= "<td>" . $ldapPerson->useraccountcontrolbadge($ldapPerson->useraccountcontrol) . "</td>";
  $output .= "<td>" . $ldapPWDLastSet . "</td>";
  $output .= "<td>" . makeEmail($person->oxford_email) . "</td>";
  $output .= "<td>" . $ldapPerson->actionsButton($person->sso_username) . "</td>";
  $output .= "</tr>";

  if (!isset($ldapPerson->samaccountname)) {
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
