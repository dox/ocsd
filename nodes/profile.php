<div class="page-header">
	<h1>My Profile <small> LDAP Details</small></h1>
</div>

<p>Username: <?php echo $_SESSION['userinfo'][0]['samaccountname'][0]; ?></p>
<p>Display Name: <?php echo $_SESSION['userinfo'][0]['displayname'][0]; ?></p>
<p>E-Mail: <a href="mailto:<?php echo $_SESSION['userinfo'][0]['mail'][0]; ?>"><?php echo $_SESSION['userinfo'][0]['mail'][0]; ?></a></p>
<p>Department: <?php echo $_SESSION['userinfo'][0]['department'][0]; ?></p>
<p>DN: <?php echo $_SESSION['userinfo'][0]['dn']; ?></p>

<h2>Member Of</h2>
<ul class="list-group">
	<?php					
	foreach ($_SESSION['userinfo'][0]['memberof'] AS $groupName) {
		if (substr($groupName, 0, 3) == "CN=") {
			$firstCommaLoc = strpos($groupName, ",");
			//echo $firstCommaLoc;
			echo "<li class=\"list-group-item\">" . substr($groupName, 3, $firstCommaLoc -3) . "</li>";
			//printArray($groupName);
		}					
		//printArray($groupName);
	}						
	?>						
</ul>