<?php
if (isset($_GET['logout'])) {
	if ($_GET['logout'] == "true" && isset($_SESSION["username"])) { //destroy the session
		$message = "<div class=\"alert alert-success\" role=\"alert\"><strong>Success!</strong> You have been logged out.</div>";
		$logInsert = (new Logs)->insert("logoff","success",null,"Logoff success");

		$_SESSION = array();
		session_destroy();
	}
}
?>

<form class="form-signin" id="loginForm" method="post" role="main">
	<h1 class="text-primary text-center"><svg width="2em" height="2em"><use xlink:href="images/icons.svg#ocsd-logo"/></svg></h1>
	<h1 class="h3 mb-3 fw-normal">Please sign in</h1>
	<?php echo $message; ?>

	<label for="username" class="visually-hidden">Username</label>
	<input type="text" id="username" name="username" class="form-control" value="<?php if (isset($_POST['username'])) { echo $username; } ?>" placeholder="Username" required autofocus>
	<label for="password" class="visually-hidden">Password</label>
	<input type="password" id="password" name="password" class="form-control" placeholder="Password" required>

	<label class="form-label float-end">
		<?php
		if (pwd_reset_url) {
			echo "<span class=\"form-label-description\">";
			echo "<a href=\"" . pwd_reset_url . "\" class=\"text-muted\">Forgot Password?</a>";
			echo "</span>";
		}
		?>
	</label>

	<button class="w-100 btn btn-lg btn-primary" name="submit" value="submit" type="submit">Sign in</button>
	<input type='hidden' name='oldform' value='1'>
</form>



<style>


.form-signin {
	width: 100%;
	max-width: 330px;
	padding: 15px;
	margin: auto;
}
.form-signin .form-control {
	position: relative;
	box-sizing: border-box;
	height: auto;
	padding: 10px;
	font-size: 16px;
}
.form-signin .form-control:focus {
	z-index: 2;
}
.form-signin input[type="text"] {
	margin-bottom: -1px;
	border-bottom-right-radius: 0;
	border-bottom-left-radius: 0;
}
.form-signin input[type="password"] {
	margin-bottom: 10px;
	border-top-left-radius: 0;
	border-top-right-radius: 0;
}
</style>
