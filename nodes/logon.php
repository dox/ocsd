<?php
//log them out?
if (isset($_GET['logout'])) {
	if ($_GET['logout'] == "yes") { //destroy the session
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
		}
	}
	
	$message = "<div class=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button><strong>Warning!</strong> Login attempt failed.</div>";
}
?>
<style type="text/css">
.form-signin {
	max-width: 300px;
	padding: 19px 29px 29px;
	margin: 0 auto 20px;
	background-color: #fff;
	border: 1px solid #e5e5e5;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	-webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
	-moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
	box-shadow: 0 1px 2px rgba(0,0,0,.05);
}

.form-signin .form-signin-heading .form-signin .checkbox {
	margin-bottom: 10px;
}

.form-signin input[type="text"] .form-signin input[type="password"] {
	font-size: 16px;
	height: auto;
	margin-bottom: 15px;
	padding: 7px 9px;
}
</style>

<div class="row">
	<div class="container">
		<form class="form-signin" id="loginForm" action="index.php?m=students&n=index.php" method="post">
		<h2 class="form-signin-heading">Logon Required</h2>
		
		<?php
		if (isset($message)) {
			echo $message;
		}
		?>
		
		<input type="text" class="input-block-level" placeholder="Username" id="username" name="username" value="<?php if (isset($_POST['username'])) { echo $username; } ?>">
		<input type="password" class="input-block-level" placeholder="Password" name="password" id="password">
		<button type="submit" name="submit" value="submit" class="btn btn-large btn-primary" >Sign in</button>
		<input type='hidden' name='oldform' value='1'>
		</form>
	</div>
</div>