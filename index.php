<?php
include_once("engine/initialise.php");

//log them out?
if (isset($_GET['logout'])) {
	if ($_GET['logout'] == "true") { //destroy the session
		$_SESSION = array();
		session_destroy();
		
		$message = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button><strong>Success!</strong> You have been logged out.</div>";
	}
}

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
			session_start();
			$_SESSION["username"] = $username;
            $_SESSION["userinfo"] = $adldap->user()->info($username);
			$redir = "Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/index.php";
			
			$log = new Logs;
			$log->notes			= $_SESSION["username"] . " logged on";
			$log->type			= "logon";
			$log->create();
			
			header($redir);
			exit;
		} else {
			$log = new Logs;
			$log->notes			= "Unsuccessful logon attempt";
			$log->prev_value	= $username;
			$log->type			= "logon";
			$log->create();
		}
	}
	
	$message = "<div class=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button><strong>Warning!</strong> Login attempt failed.</div>";
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include_once("views/html_head.php"); ?>

<body>
	<?php
	if (isset($message)) {
		echo $message;
	}
	
	$fileInclude = "nodes/logon.php";
	if (isset($_SESSION['username'])) {
		// we're logged in, work out what to include
		if (isset($_GET['m'])) {
			$fileInclude = "modules/" . $_GET['m'] . "/nodes/" . $_GET['n'];
		} elseif(isset($_GET['n'])) {
			$fileInclude = "nodes/" . $_GET['n'];
		} else {
			$fileInclude = "nodes/index.php";
		}
	} else {
		$fileInclude = "nodes/logon.php";
	}
	
	include_once("views/navigation.php");
	include_once("views/hero.php");
	?>
	<div class="container">
		<?php
		include_once($fileInclude);
		?>
		<?php include_once("views/footer.php"); ?>
	</div>
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.4.4/bootstrap-editable/js/bootstrap-editable.min.js"></script>
	<script src="js/moment.min.js"></script>
	<script src="js/ocsd.js"></script>
	
	<script src="js/typeahead.jquery.min.js"></script>
	<script src="js/handlebars.js"></script>
	<script src="js/bloodhound.min.js"></script>
</body>
</html>