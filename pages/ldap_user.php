<?php
$ldapUser = $ldap->findUser(filter_var($_GET['samaccountname'], FILTER_SANITIZE_STRING));

$data = array(
		'icon'		=> 'person-fill-lock',
		'title'		=> $ldapUser['cn'][0],
		'subtitle'	=> 'Username: ' . $ldapUser['samaccountname'][0]
);
echo pageTitle($data);

$lookups = array_filter([
	'sso_username'		=> $ldapUser['samaccountname'][0] ?? null,
	'MiFareID'          => $ldapUser['pager'][0] ?? null,
	'oxford_email'		=> $ldapUser['mail'][0] ?? null
]);

$person = new Person($lookups);
if (!empty($person->cudid)) {
	$url = "";
	
	$output  = "<div class=\"card mb-3\">";
	$output .= "<div class=\"card-body text-center\">";
	$output .= "<a href=\"index.php?page=cud_person&cudid=" . $person->cudid . "\">";
	$output .= "<h2>LINKED CUD RECORD FOUND</h2>";
	$output .= "</a>";
	$output .= "</div>";
	$output .= "</div>";
	
	echo $output;
}
?>

<div class="row">
	<div class="col-md-6 col-xl-3">
		<div class="card mb-3">
			<div class="card-status-bottom bg-primary"></div>
			<div class="card-body">
				<div class="float-left mr-3">
					<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="7" r="4"></circle><path d="M5.5 21v-2a4 4 0 0 1 4 -4h5a4 4 0 0 1 4 4v2"></path></svg>
				</div>
				<div class="lh-sm">
					<div class="strong"><?php echo $ldapUser['samaccountname'][0]; ?></div>
					<div class="text-muted">samAccountName</div>
				</div>
			</div>
		</div>
	</div>
	
	<?php
	switch ($ldapUser['useraccountcontrol'][0]) {
		case "514":
		case "546":
			$class = "text-bg-danger";
		break;
		case "512":
			$class = "text-bg-success";
		break;
		default:
			$class = "text-bg-warning";
		break;
	}
  ?>
  <div class="col-md-6 col-xl-3">
	<div class="card mb-3 <?php echo $class; ?>">
	  <div class="card-body">
		<div class="float-left mr-3">
		  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><rect x="5" y="11" width="14" height="10" rx="2"></rect><circle cx="12" cy="16" r="1"></circle><path d="M8 11v-4a4 4 0 0 1 8 0v4"></path></svg>
		</div>
		<div class="lh-sm">
		  <div class="strong"><?php echo $ldapUser['useraccountcontrol'][0]; ?></div>
		  <div class="text-muted">userAccountControl</div>
		</div>
	  </div>
	</div>
  </div>

  <?php
  /*if ($ldapPerson->pwdlastsetage() <= pwd_warn_age) {
	$class = "card-status-bottom bg-success";
  } elseif ($ldapPerson->pwdlastsetage() >= pwd_warn_age && $ldapPerson->pwdlastsetage() <= pwd_max_age) {
	$class = "card-status-bottom bg-warning";
  } elseif ($ldapPerson->pwdlastsetage() > pwd_max_age) {
	$class = "card-status-bottom bg-danger";
  }*/
  ?>
  <div class="col-md-6 col-xl-3">
	<div class="card mb-3">
	  <div class="<?php echo $class; ?>"></div>
	  <div class="card-body">
		<div class="float-left mr-3">
		  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="8" cy="15" r="4"></circle><line x1="10.85" y1="12.15" x2="19" y2="4"></line><line x1="18" y1="5" x2="20" y2="7"></line><line x1="15" y1="8" x2="17" y2="10"></line></svg>
		</div>
		<div class="lh-sm">
			<?php
			if ($ldapUser->pwdlastset > 0) {
				$pwdLastSet = $ldapUser->pwdlastset->toDateTimeString();
			}
			?>
		  <div class="strong"><?php echo $pwdLastSet . " <em>(" . timeAgo(strtotime($pwdLastSet)); ?>)</em></div>
		  <div class="text-muted">pwdLastSet</div>
		</div>
	  </div>
	</div>
  </div>

  <div class="col-md-6 col-xl-3">
	<div class="card mb-3">
	  <div class="card-status-bottom bg-primary"></div>
	  <div class="card-body">
		<div class="float-left mr-3">
		  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="12" r="9"></circle><line x1="9" y1="15" x2="15" y2="9"></line></svg>
		</div>
		<div class="lh-sm">
			<?php
			if ($ldapUser->lastlogon > 0) {
				$lastlogon = $ldapUser->lastlogon->toDateTimeString();
			}
			?>
		  <div class="strong"><?php echo $lastlogon . " <em>(" . timeAgo(strtotime($lastlogon)); ?>)</em></div>
		  <div class="text-muted">lastLogon</div>
		</div>
	  </div>
	</div>
  </div>
</div>

<div class="row">
  <?php
  if (isset($ldapUser['description'][0])) {
	echo "<div class=\"card\">";
	echo "<div class=\"card-body text-center\">";
	echo "<h2>" . $ldapUser['description'][0] . "</h2>";
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
		foreach ($ldapUser['memberof'] AS $memberOf) {
		  $url = "./index.php?page=ldap_users&filter=group&ldap_search=" . urlencode($memberOf);
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
		foreach ($ldapUser['objectclass'] AS $objectClass) {
		  echo "<li>" . $objectClass . "</li>";
		}
		echo "</ul>";
		?>
	  </div>
	</div>
  </div>
</div>

<hr />

<div class="row">
  <?php
  printArray($ldapUser);
  ?>
</div>