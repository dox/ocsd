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
			
			$_SESSION["username"] = $username;
            $_SESSION["userinfo"] = $adldap->user()->info($username);
			$redir = "Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/index.php";
			
			$logSQLInsert = Array ("type" => "LOGON", "description" => $_SESSION["username"] . " logged on with LDAP");
			$id = $db->insert ('_logs', $logSQLInsert);
			
			header($redir);
			exit;
		} else {
			$logSQLInsert = Array ("type" => "LOGON FAIL", "description" => $username . " attempted to log on with LDAP");
			$id = $db->insert ('_logs', $logSQLInsert);
		}
	}
	
	$message = "<div class=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button><strong>Warning!</strong> Login attempt failed.</div>";
}

?>

<body>
	<div class="bw">
		<div class="dh">
			<?php include_once("views/navigation.php"); ?>
			<div class="et bmj">
				<?php
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
				
				include_once($node); ?>
			</div>
		</div>
	</div>
	
	<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="/js/chart.js"></script>
	<script src="/js/tablesorter.min.js"></script>
	<script src="/js/toolkit.js"></script>
	<script src="/js/application.js"></script>
	
	<script>
		// execute/clear BS loaders for docs
		$(function(){while(window.BS&&window.BS.loader&&window.BS.loader.length){(window.BS.loader.pop())()}})
	</script>
</body>
</html>