<?php include_once("views/html_head.php"); ?>

<?php
//you should look into using PECL filter or some form of filtering here for POST variables
if (isset($_POST["username"]) && isset($_POST["password"])) {
	$form_username = strtoupper($_POST["username"]); //remove case sensitivity on the username
	$form_password = $_POST["password"];

	$user = $ldap_connection->query()
	->where('samaccountname', '=', $form_username)
	->first();

	$userGroups = $user['memberof'];
	$allowed = array(LDAP_ALLOWED_DN);
	$difference = array_intersect(
		array_map('strtolower', $userGroups),
		array_map('strtolower', $allowed)
	);

	if (count($difference) > 0) {
    // Our user is a member of one of the allowed groups.
    // Continue with authentication.
    if ($ldap_connection->auth()->attempt($user['distinguishedname'][0], $form_password, $stayAuthenticated = true)) {
			// User has been successfully authenticated.
			$personsClass = new Persons;
			$CUDPerson = $personsClass->search($user['samaccountname'][0]);
			if (!count($CUDperson) == 1) {
				$CUDPerson = $personsClass->search($user['mail'][0], 2);
			}

			$_SESSION["cudid"] = $CUDPerson[0]['cudid'];
			$_SESSION["bodcard"] = $CUDPerson[0]['barcode7'];
			$_SESSION["username"] = strtoupper($user['samaccountname'][0]);
			$_SESSION["avatar_url"] = "photos/UAS_UniversityCard-" . $CUDPerson[0]['university_card_sysis'] . ".jpg";
			$_SESSION["email"] = $ldap_user['mail'][0];
			$_SESSION["groups"] = $userGroups;

			if (in_array(LDAP_ADMIN_DN, $_SESSION['groups'])) {
				$_SESSION["user_type"] = 'Administrator';
			} else {
				$_SESSION["user_type"] = 'OCSD User';
			}

			if (isset($_POST['remember'])) {
				$hash = crypt($_POST['form_password'], salt);
				$date_created = date('Y-m-d H:i:s');
				$cookie_duration = time()+3600; // seconds

				setcookie("ocsd_username", $_SESSION["username"], $cookie_duration);
				setcookie("ocsd_hash", $hash, $cookie_duration);

				$sql  = "INSERT INTO _sessions ";
				$sql .= " (username, hash, date_created)";
				$sql .= " VALUES ('" . $_SESSION["username"] . "', '" . $hash . "', '" . $date_created . "') ";
				$sql .= " ON DUPLICATE KEY UPDATE";
				$sql .= " username='" . $_SESSION["username"] . "', hash='" . $hash . "', date_created='" . $date_created . "';";

				$db->query($sql);
			}

			$redir = "Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/index.php";

			$logInsert = (new Logs)->insert("logon","success",null,"LDAP logon success for {ldap:" . $user['samaccountname'][0] . "}");

			header($redir);
			exit;
    } else {
			// Username or password is incorrect.
			$message = "<div class=\"alert alert-danger\" role=\"alert\"><strong>Warning!</strong> Login attempt failed.</div>";
			$logInsert = (new Logs)->insert("logon","error",null,"LDAP logon failed for <code>" . $form_username . "</code>");
    }
	} else {
		$message = "<div class=\"alert alert-danger\" role=\"alert\"><strong>Warning!</strong> Login attempt failed.  User {ldap:" . $user['samaccountname'][0] . "} not in group</div>";
	}
}
?>

<body>
	<?php
	if (isset($_SESSION['username'])) {
		include_once("views/navbar_top.php");
	}

	if (isset($_SESSION['username'])) {
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
		include_once("views/footer.php");
	}
	?>
</body>
</html>
