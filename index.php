<?php include_once("views/html_head.php"); ?>

<?php
//you should look into using PECL filter or some form of filtering here for POST variables
if (isset($_POST["username"]) && isset($_POST["password"])) {
	$form_username = strtoupper($_POST["username"]); //remove case sensitivity on the username
	$form_password = $_POST["password"];

	if ($ldap_connection->auth()->attempt($form_username . "@seh.ox.ac.uk", $form_password, $stayAuthenticated = true)) {
	    // Successfully authenticated user.
			$ldap_user = $ldap_connection->query()->findBy('samaccountname', $form_username);

			$personsClass = new Persons;
			$CUDPerson = $personsClass->search($ldap_user['samaccountname'][0]);
			if (!count($CUDperson) == 1) {
				$CUDPerson = $personsClass->search($ldap_user['mail'][0], 2);
			}

			$_SESSION["cudid"] = $CUDPerson[0]['cudid'];
			$_SESSION["bodcard"] = $CUDPerson[0]['barcode7'];
			$_SESSION["username"] = strtoupper($ldap_user['samaccountname'][0]);
			$_SESSION["avatar_url"] = "photos/UAS_UniversityCard-" . $CUDPerson[0]['university_card_sysis'] . ".jpg";
			$_SESSION["email"] = $ldap_user['mail'][0];
			//$_SESSION["userinfo"] = $adldap->user()->info($username);

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

			$logInsert = (new Logs)->insert("logon","success",null,"LDAP logon success");

			header($redir);
			exit;
	} else {
		// Username or password is incorrect.
		$message = "<div class=\"alert alert-danger\" role=\"alert\"><strong>Warning!</strong> Login attempt failed.</div>";
		$logInsert = (new Logs)->insert("logon","error",null,"LDAP logon failed for <code>" . $form_username . "</code>");
	}
}

/*
// try to log in with cookie
if (!isset($_SESSION['username']) && !isset($_POST["oldform"])) {
	if (isset($_COOKIE['ocsd_username']) && isset($_COOKIE['ocsd_hash'])) {
		$sql  = "SELECT * FROM _sessions ";
		$sql .= " WHERE username = '" . $_SESSION["username"] . "'";
		$sql .= "  AND hash = '" . $hash . "'";

		$dbSession = $db->query($sql);

		if (count($dbSession) == 1) {
			echo "COOKIE LOGON!";

			$_SESSION["cudid"] = "unknown";
			$_SESSION["bodcard"] = "unknown";
			$_SESSION["username"] = strtoupper($_COOKIE['ocsd_username']);
			$_SESSION["avatar_url"] = "unknown";
			$_SESSION["email"] = "unknown";
			$_SESSION["userinfo"] = "unknown";
			$redir = "Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/index.php";

			$logInsert = (new Logs)->insert("logon","success",null,"COOKIE logon success");

			header($redir);
			exit;
		}
	}
*/
?>

<body>
	<?php
	if (isset($_SESSION['username'])) {
		include_once("views/navbar_top.php");
	}
	?>
	<div class="page">
		<?php
		if (isset($_SESSION['username'])) {
			if (!in_array(strtoupper($_SESSION["username"]), allowed_usernames) ) {
				echo "<br /><div class=\"alert alert-danger\" role=\"alert\">You do not have permission to use this system.</div>";
				die;
			}
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
			echo "<div class=\"content\">";
			echo "<div class=\"container-xl d-flex flex-column justify-content-center\">";
			include_once($node);
			echo "</div>";
			echo "</div>";
		}
		include_once("views/footer.php");
		?>
	</div>
</body>
</html>
