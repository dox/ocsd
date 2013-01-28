<div class="row">
	<div class="span12">
		<div class="page-header">
			<h1>My Profile <small> LDAP Details</small></h1>
		</div>
	</div>
	<div class="span12">
		<p>Username: <?php echo $_SESSION['userinfo'][0]['samaccountname'][0]; ?></p>
		<p>Display Name: <?php echo $_SESSION['userinfo'][0]['displayname'][0]; ?></p>
		<p>Member Of:
		<?php
		foreach ($_SESSION['userinfo'][0]['memberof'] AS $group) {
			printArray($group);
		}
		?></p>
		<p>Department: <?php echo $_SESSION['userinfo'][0]['department'][0]; ?></p>
		<p>Primary Group: <?php echo $_SESSION['userinfo'][0]['primarygroupid'][0]; ?></p>
		<p>Account Status: <?php echo $_SESSION['userinfo'][0]['samaccountname'][0]; ?></p>
	</div>
</div>


<?
//printArray($adldap);
//printArray($_SESSION);
?>