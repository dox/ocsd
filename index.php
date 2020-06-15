<?php include_once("views/html_head.php"); ?>

<?php
$allowedUsers[] = "BREAKSPEAR";
$allowedUsers[] = "TREHEARNE";
$allowedUsers[] = "PARFITT";
$allowedUsers[] = "ESTALL";
$allowedUsers[] = "WILLIS";
$allowedUsers[] = "BROOKS";
$allowedUsers[] = "BROOKS";
$allowedUsers[] = "MORRISON";
$allowedUsers[] = "JAMIESON";
$allowedUsers[] = "HACK";
$allowedUsers[] = "ABID";
$allowedUsers[] = "KNIGHT";
$allowedUsers[] = "BLOSSOM";
$allowedUsers[] = "COLES";
$allowedUsers[] = "PAGANI";
$allowedUsers[] = "NJOKI";
$allowedUsers[] = "BRICKELL";
$allowedUsers[] = "ALDEN";

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

			$_SESSION["username"] = $username;
            $_SESSION["userinfo"] = $adldap->user()->info($username);
			$redir = "Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/index.php";

			$logInsert = (new Logs)->insert("logon","success",null,"LDAP logon success");
			header($redir);
			exit;
		} else {
			$logInsert = (new Logs)->insert("logon","error",null,"<code>" . $username . "</code>LDAP logon failed");
		}
	}

	$message = "<div class=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button><strong>Warning!</strong> Login attempt failed.</div>";
}

?>

<body>
<?php include_once("views/navbar_top.php"); ?>

<div class="container-fluid">
	<div class="row">
		<?php include_once("views/navbar_side.php"); ?>

		<?php
		if (isset($_SESSION['username'])) {
			if (!in_array(strtoupper($_SESSION["username"]), $allowedUsers) ) {
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
			echo "<main role=\"main\" class=\"col-md-9 ml-sm-auto col-lg-10 px-md-4\">";
			include_once($node);
			echo "</main>";
		}
		?>
	</div>
</div>
</body>
</html>

<script>
$('#navbar_search').autocomplete({
	serviceUrl: 'api/persons.php',
	lookupLimit: 5,
	type: "POST",
	dataType: "json",
	paramName: "navbar_search",
	onSelect: function (suggestion) {
		window.location.href='index.php?n=persons_unique&cudid=' + suggestion.data;
	}
});
</script>
