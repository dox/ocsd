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

<div class="flex-fill d-flex flex-column justify-content-center">
	<div class="container-tight py-6">
		<div class="text-center mb-4">
			<img src="./images/logo.svg" height="36" alt="">
		</div>
		<form class="card card-md" id="loginForm" method="post" role="form">
			<div class="card-body">
				<h1 class="mb-5 text-center">Login Required</h1>
				<?php echo $message; ?>
				<div class="mb-3">
					<label for="username" class="sr-only">Username</label>
					<input type="text" class="form-control" placeholder="Username" id="username" name="username" value="<?php if (isset($_POST['username'])) { echo $username; } ?>" required autocomplete="off" autofocus>

				</div>
				<div class="mb-3">
					<label class="form-label">
						<?php
						if (pwd_reset_url) {
							echo "<span class=\"form-label-description\">";
							echo "<a href=\"" . pwd_reset_url . "\">Forgot Password</a>";
							echo "</span>";
						}
						?>
					</label>
					<label for="password" class="sr-only">Password</label>
					<input type="password" class="form-control" placeholder="Password" id="password" name="password" required>
				</div>
				<div class="mb-2">
					<label class="form-check">
						<input type="checkbox" class="form-check-input" id="remember" name="remember" />
						<span class="form-check-label">Remember me on this device</span>
					</label>
				</div>
				<div class="form-footer">
					<button type="submit" name="submit" value="submit" class="btn btn-primary btn-block">Sign in</button>
					<input type='hidden' name='oldform' value='1'>
				</div>
			</div>
		</form>
	</div>
</div>
