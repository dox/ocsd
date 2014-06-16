<!DOCTYPE html>
<html lang="en">
<?php include_once("views/html_head.php"); ?>
<link href="css/404.css" rel="stylesheet">

<body>
<div class="site-wrapper">
	<div class="site-wrapper-inner">
		<div class="cover-container">
			<div class="masthead clearfix">
				<div class="inner">
					<h3 class="masthead-brand">OCSD</h3>
					<ul class="nav masthead-nav">
						<li class="active"><a href="index.php">Home</a></li>
						<!--<li><a href="#">Features</a></li>-->
						<!--<li><a href="#">Contact</a></li>-->
					</ul>
				</div>
			</div>
			
			<div class="inner cover">
				<h1 class="cover-heading">Oops!</h1>
				<p class="lead">There's been a problem.  Sorry about that.  If you want, let the Administrator know so he can fix it.</p>
				<p class="lead"><a href="mailto:<?php echo SITE_ADMIN_EMAIL; ?>" class="btn btn-lg btn-default">E-Mail Administrator</a></p>
			</div>
			
			<div class="mastfoot">
				<div class="inner">
					<p>&copy; <a href="http://twitter.com/doxykins">Andrew Breakspear</a> 2012-2013 <i>Version: <?php echo SITE_VERSION; ?></i></p>
				</div>
			</div>
		</div>
	</div>
</div>

</body>
</html>
