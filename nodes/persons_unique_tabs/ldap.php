<?php
$ldapPerson = new LDAPPerson($person->sso_username, $person->oxford_email);

if (isset($ldapPerson->samaccountname)) {
?>
<div class="card mb-3">
	<div class="card-body">
		<h3 class="card-title">LDAP Record Match</h3>
		<?php
		if (isset($ldapPerson->description)) {
			echo "<p>Description: " . $ldapPerson->description . "</p>";
		}
		?>

		<?php echo $ldapPerson->pwdlastsetbadge() . " " . $ldapPerson->useraccountcontrolbadge(); ?>


		</div>
		<!-- Card footer -->
		<div class="card-footer">
				<?php echo $ldapPerson->actionsButton($person->cudid, "btn btn-primary float-end"); ?>
				<a href="./index.php?n=ldap_unique&samaccountname=<?php echo $ldapPerson->samaccountname; ?>" class="btn btn-link"><?php echo $ldapPerson->samaccountname; ?></a>
		</div>
	</div>



<?php
}
?>
