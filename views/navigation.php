<script src="js/typeahead.min.js"></script>
<script src="js/hogan-2.0.0.js"></script>
    
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
					<?php
					if (isset($_SESSION['username'])) {
						echo "<input type=\"text\" class=\"typeahead\" placeholder=\"Search\" autocomplete=\"off\" />";
					}
					?>
				</form>
				
				<?php
				if (isset($_SESSION['username'])) {
				?>
				<ul class="nav pull-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="index.php?m=reports&n=index.php">Reports</a></li>
						<li><a href="index.php?m=logs&n=index.php">Logs</a></li>
						<li><a href="index.php?m=epos&n=index.php">EPOS</a></li>
						<li class="divider"></li>
						<li class="nav-header">My Details</li>
						<li><a href="index.php?n=profile.php">My Profile</a></li>
						<li><a href="index.php?n=contact.php">Report Problem</a></li>
						<li><a href="index.php?n=logon.php&logout=true">Log Out</a></li>
					</ul>
					</li>
				</ul>
				<?php
				} else {
				?>
					<ul class="nav pull-right">
						<li><a href="index.php?n=logon.php">Log In</a></li>
					</ul>
				<?php
				}
				?>
			</div>
		</div>
	</div>
</div>


<style>
.typeahead-wrapper {
	display: block;
	margin: 50px 0;
}

.tt-dropdown-menu {
	background-color: #fff;
	border: 1px solid #000;
}

.tt-suggestion.tt-is-under-cursor {
	background-color: #ccc;
}

.triggered-events {
	float: right;
	width: 500px;
	height: 300px;
}
</style>

<script>
$('.typeahead').typeahead({
	name: 'twitter',
	prefetch: './api/studentsAll.php',
	template: '<p class="repo-language">{{name}}</p>',
	engine: Hogan
}).on('typeahead:selected', function($e) {
	var $typeahead = $(this);
	var studentUID = $typeahead[0].value;
	var url = 'index.php?m=students&n=user.php&studentid=' + studentUID;
	
	window.location = url;

});
</script>