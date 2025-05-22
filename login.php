<?php
require_once 'inc/autoload.php';
?>
<!doctype html>
<html lang="en">
	<head>
		<?php include_once("inc/html_head.php"); ?>
	</head>
	<body>
	<div class="container text-center">
		<div class="row justify-content-center pt-5">
			<div class="col-12 col-sm-8 col-md-6 col-lg-4 mx-auto">
				<?php
				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					
					if ($user->authenticate($_POST['username'], $_POST['password'])) {
						header('Location: index.php');
						exit;
					} else {
						echo "<div class=\"alert alert-warning\" role=\"alert\">" . $ldap->getLastError() . "</div>";
					}
				}
				?>
				
				<form class="form-signin" method="post">
					<span class="text-primary"><?php echo icon('ocsd', '5em'); ?></span>
					<h1 class="h3 mb-3 fw-normal"><?php echo site_name; ?></h1>
					
					<div class="form-floating">
						<input type="text" class="form-control" id="username" name="username" required>
						<label for="username">Username</label>
					</div>
					<div class="form-floating">
						<input type="password" class="form-control" id="password" name="password" required>
						<label for="floatingPassword">Password</label>
					</div>
					<div class="form-floating text-end">
						<?php
						if (reset_url) {
							echo "<span class=\"form-label-description\">";
							echo "<a href=\"" . reset_url . "\" class=\"text-muted\">Forgot Password?</a>";
							echo "</span>";
						}
						?>
					</div>
					<button class="btn btn-primary w-100 py-2 my-3" type="submit">Sign in</button>
				</form>
			</div>
			<?php include_once("inc/view_footer.php"); ?>
		</div>
	</div>
	<script src="js/main.js"></script>
	</body>
</html>