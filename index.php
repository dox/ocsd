<?php include_once("views/html_head.php"); ?>

<?php

if (isset($_POST["username"]) && isset($_POST["password"])) {
	$usernameClean = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
	$passwordClean = filter_var($_POST["password"], FILTER_SANITIZE_STRING);
	
	$user = $ldap_connection->query()
		->where('samaccountname', '=', $usernameClean)
		->get();
	$user = $user[0];
	
	
	if (isset($user['samaccountname'][0])) {
		// Get the groups from the user.
		$userGroups = $user['memberof'];
		
		// Set up our allowed groups.
		$allowed = LDAP_ALLOWED_DN;
		
		// Normalize the group distinguished names and determine if
		// the user is a member of any of the allowed groups:
		$difference = array_intersect(
			array_map('strtolower', $userGroups),
			array_map('strtolower', $allowed)
		);
		
		if (count($difference) > 0) {
			// Our user is a member of one of the allowed groups.
			// Continue with authentication.
			$personsClass = new Persons();
			$CUDPerson = $personsClass->search($user['samaccountname'][0], 2);
			if (!isset($CUDperson[0]['cudid'])) {
				$CUDPerson = $personsClass->search($user['mail'][0], 2);
			}
			
			if ($ldap_connection->auth()->attempt($user['distinguishedname'][0], $passwordClean)) {
				// User has been successfully authenticated.
				
				$logInsert = (new Logs)->insert("ldap","success",null,"User <code>" . $user['distinguishedname'][0] . "</code> authenticated, and has access",strtoupper($user['samaccountname'][0]));
				
				$_SESSION["authenticated"] = true;
				$_SESSION["cudid"] = $CUDPerson[0]['cudid'];
				$_SESSION["bodcard"] = $CUDPerson[0]['barcode7'];
				$_SESSION["username"] = strtoupper($user['samaccountname'][0]);
				$_SESSION["avatar_url"] = "photos/UAS_UniversityCard-" . $CUDPerson[0]['university_card_sysis'] . ".jpg";
				$_SESSION["email"] = $user['mail'][0];
				$_SESSION["groups"] = $userGroups;
				
				// check if user is in LDAP_ADMIN_DN, if so, make them an admin
				if (in_array(LDAP_ADMIN_DN, $user['memberof'])) {
					$_SESSION["user_type"] = "Administrator";
				}
			} else {
				// Username or password is incorrect.
				echo "You do not have access to this resource.  Please contact the IT Office";
				$logInsert = (new Logs)->insert("ldap","error",null,"User <code>" . $user['distinguishedname'][0] . "</code> authenticated, but does not have access",strtoupper($user['samaccountname'][0]));
			}
		}
	} else {
		echo "Wrong username/password";
		$logInsert = (new Logs)->insert("ldap","error",null,"User <code>" . $usernameClean . "</code> failed to authenticate");
	}
}
?>

<body>
	<?php
	if ($_SESSION['authenticated'] == true) {
		include_once("views/navbar_top.php");
		
		if (isset($_GET['n'])) {
			$node = "nodes/" . $_GET['n'] . ".php";

			if (!file_exists($node)) {
				$node = "nodes/404.php";
			}
		} elseif (!isset($_GET['n'])) {
			$node = "nodes/index.php";
		} else {
			$node = "nodes/404.php";
		}
	} else {
		$node = "nodes/admin_logon.php";
	}

	if ($node == "nodes/admin_logon.php" || $_GET['n'] == "admin_logon") {
		include_once($node);
	} else {
		echo "<div class=\"container\" role=\"main\">";
		//echo "<div class=\"container-xl d-flex flex-column justify-content-center\">";
		include_once($node);
		//echo "</div>";
		echo "</div>";
	}

	if ($_GET['n'] != 'admin_logon') {
		echo "<div class=\"container\" role=\"main\">";
		include_once("views/footer.php");
		echo "</div>";
	}
	?>
</body>
</html>