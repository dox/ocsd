<?php include_once("views/html_head.php"); ?>

<?php
//you should look into using PECL filter or some form of filtering here for POST variables
if (isset($_POST["username"])) {
	$username = strtoupper($_POST["username"]); //remove case sensitivity on the username
	$password = $_POST["password"];
}

if (isset($_POST["oldform"])) { //prevent null bind
	if ($username != NULL && $password != NULL){
        try {
		    $adldap = new adLDAP();
        }
        catch (adLDAPException $e) {
            echo $e;
            exit();
        }

		//authenticate the user
		if ($adldap->authenticate($username, $password)){
			//establish your session and redirect

			$ldapArray = $adldap->user()->info($username);
	    $ldapArray = $ldapArray[0];

			$personsClass = new Persons;
		  $CUDPerson = $personsClass->search($ldapArray['samaccountname'][0]);
		  if (!count($CUDperson) == 1) {
		    $CUDPerson = $personsClass->search($ldapArray['mail'][0], 2);
		  }

			$_SESSION["cudid"] = $CUDPerson[0]['cudid'];
			$_SESSION["bodcard"] = $CUDPerson[0]['barcode7'];
			$_SESSION["username"] = strtoupper($ldapArray['samaccountname'][0]);
			$_SESSION["avatar_url"] = "photos/UAS_UniversityCard-" . $CUDPerson[0]['university_card_sysis'] . ".jpg";
			$_SESSION["email"] = $ldapArray['mail'][0];
      $_SESSION["userinfo"] = $adldap->user()->info($username);
			$redir = "Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/index.php";

			$logInsert = (new Logs)->insert("logon","success",null,"LDAP logon success");
			header($redir);
			exit;
		} else {
			$message = "<div class=\"alert alert-danger\" role=\"alert\"><strong>Warning!</strong> Login attempt failed.</div>";
			$logInsert = (new Logs)->insert("logon","error",null,"LDAP logon failed for <code>" . $username . "</code>");
		}
	}

}

?>

<body>
<?php
if (isset($_SESSION['username'])) {
	include_once("views/navbar_top.php");
}
?>
<div class="page">
		<?php //include_once("views/navbar_side.php"); ?>

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
