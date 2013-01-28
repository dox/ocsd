<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			</a>
			<a class="brand" href="index.php"><?php echo SITE_SHORT_NAME; ?></a>
			<div class="nav-collapse collapse">
				<ul class="nav">
					<li class="active"><a href="index.php">Home</a></li>
					<li><a href="index.php?m=students&n=index.php">Users</a></li>
					<li><a href="index.php?n=contact.php">Contact</a></li>
				</ul>
		
				<form class="navbar-search pull-left">
					<input type="text" class="search-query" id="searchAhead" placeholder="Search" />
				</form>
				<ul class="nav pull-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="index.php?m=reports&n=index.php">Reports</a></li>
						<li><a href="index.php?m=epos&n=index.php">EPOS</a></li>
						<li><a href="index.php?n=profile.php">My Profile</a></li>
						<li class="divider"></li>
						<li class="nav-header">Nav header</li>
						<li><a href="index.php?n=contact.php">Report Problem</a></li>
						<li><a href="#">Log Out</a></li>
					</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>