<?php
$ldapPerson = new LDAPPerson($_GET['samaccountname']);

$logsClass = new Logs();

if (isset($ldapPerson->samaccountname)) {
  $logInsert = (new Logs)->insert("ldap","success",null,$ldapPerson->cn . " ({ldap:" . $ldapPerson->samaccountname . "}) LDAP record viewed");

  $personsClass = new Persons;
  $CUDPerson = $personsClass->search($ldapPerson->samaccountname, 2);
  if (!count($CUDperson) == 1) {
    $CUDPerson = $personsClass->search($ldapPerson->mail, 2);
  }

  $logs = $logsClass->allByUser($CUDPerson->cudid, $ldapPerson->samaccountname);

  $icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"images/icons.svg#bell\"/></svg> Test Button", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteMealModal\"");
  $icons[] = array("class" => "btn-warning", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"images/icons.svg#email\"/></svg> Test Button", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteMealModal\"");

  $title = count($users) . autoPluralise(" LDAP Record", " LDAP Records", count($users));
  echo displayTitle($ldapPerson->cn, "Filter: " . $_GET['samaccountname'], $icons);
?>
<div class="card">
  <div class="card-body text-center">
    <h2><?php echo $ldapPerson->dn; ?></h2>
  </div>
</div>

<?php
if ($CUDPerson) {
  $output  = "<div class=\"card\">";
  $output .= "<div class=\"card-body text-center\">";
  $output .= "<a href=\"./index.php?n=persons_unique&cudid=" . $CUDPerson[0]['cudid'] . "\">";
  $output .= "<h2>LINKED CUD RECORD FOUND</h2>";
  $output .= "</a>";
  $output .= "</div>";
  $output .= "</div>";

  echo $output;
}
?>

<div class="row">
  <div class="col-md-6 col-xl-3">
    <div class="card">
      <div class="card-status-bottom bg-primary"></div>
      <div class="card-body">
        <div class="float-left mr-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="7" r="4"></circle><path d="M5.5 21v-2a4 4 0 0 1 4 -4h5a4 4 0 0 1 4 4v2"></path></svg>
        </div>
        <div class="lh-sm">
          <div class="strong"><?php echo $ldapPerson->samaccountname; ?></div>
          <div class="text-muted">samAccountName</div>
        </div>
      </div>
    </div>
  </div>

  <?php
  if (in_array($ldapPerson->useraccountcontrol, array("512, 514"))) {
    $class = "card-status-bottom bg-success";
  } else {
    $class = "card-status-bottom bg-danger";
  }
  ?>
  <div class="col-md-6 col-xl-3">
    <div class="card">
      <div class="<?php echo $class; ?>"></div>
      <div class="card-body">
        <div class="float-left mr-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><rect x="5" y="11" width="14" height="10" rx="2"></rect><circle cx="12" cy="16" r="1"></circle><path d="M8 11v-4a4 4 0 0 1 8 0v4"></path></svg>
        </div>
        <div class="lh-sm">
          <div class="strong"><?php echo $ldapPerson->useraccountcontrol; ?></div>
          <div class="text-muted">userAccountControl</div>
        </div>
      </div>
    </div>
  </div>

  <?php
  if ($ldapPerson->pwdlastsetage() <= pwd_warn_age) {
    $class = "card-status-bottom bg-success";
  } elseif ($ldapPerson->pwdlastsetage() >= pwd_warn_age && $ldapPerson->pwdlastsetage() <= pwd_max_age) {
    $class = "card-status-bottom bg-warning";
  } elseif ($ldapPerson->pwdlastsetage() > pwd_max_age) {
    $class = "card-status-bottom bg-danger";
  }
  ?>
  <div class="col-md-6 col-xl-3">
    <div class="card">
      <div class="<?php echo $class; ?>"></div>
      <div class="card-body">
        <div class="float-left mr-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="8" cy="15" r="4"></circle><line x1="10.85" y1="12.15" x2="19" y2="4"></line><line x1="18" y1="5" x2="20" y2="7"></line><line x1="15" y1="8" x2="17" y2="10"></line></svg>
        </div>
        <div class="lh-sm">
          <div class="strong"><?php echo date('Y-m-d', w32timeToTime($ldapPerson->pwdlastset)) . " <em>(" . howLongAgo($ldapPerson->pwdlastsetdate()); ?>)</em></div>
          <div class="text-muted">pwdLastSet</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3">
    <div class="card">
      <div class="card-status-bottom bg-primary"></div>
      <div class="card-body">
        <div class="float-left mr-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="12" r="9"></circle><line x1="9" y1="15" x2="15" y2="9"></line></svg>
        </div>
        <div class="lh-sm">
          <div class="strong"><?php echo date('Y-m-d', w32timeToTime($ldapPerson->lastlogon)) . " <em>(" . howLongAgo(w32timeToTime($ldapPerson->lastlogon)); ?>)</em></div>
          <div class="text-muted">lastLogon</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <?php
  if (isset($ldapPerson->description)) {
    echo "<div class=\"card\">";
    echo "<div class=\"card-body text-center\">";
    echo "<h2>" . $ldapPerson->description . "</h2>";
    echo "</div>";
    echo "</div>";
  }
  ?>
</div>

<div class="row">
  <div class="col-md-6 col-xl-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Member Of</h3>
      </div>

      <div class="card-body">
        <?php
        echo "<ul>";
        foreach ($ldapPerson->memberof AS $memberOf) {
          $url = "./index.php?n=ldap_all&filter=group&cn=" . urlencode($memberOf);
          echo "<li><a href=\"" . $url . "\">" . $memberOf . "</a></li>";
        }
        echo "</ul>";
        ?>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Object Class</h3>
      </div>

      <div class="card-body">
        <?php
        echo "<ul>";
        foreach ($ldapPerson->objectclass AS $objectClass) {
          echo "<li>" . $objectClass . "</li>";
        }
        echo "</ul>";
        ?>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <?php
  echo $logsClass->makeTable($logs);
  ?>
</div>
<?php } ?>
