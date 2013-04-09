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
						echo "<input type=\"text\" class=\"twitter-typeahead\" placeholder=\"Search\" autocomplete=\"off\" />";
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
.twitter-typeahead .tt-query,
.twitter-typeahead .tt-hint {
	margin-bottom: 0;
}

.tt-dropdown-menu {
	min-width: 160px;
	margin-top: 2px;
	padding: 5px 0;
	background-color: #fff;
	border: 1px solid #ccc;
	border: 1px solid rgba(0,0,0,.2);
	*border-right-width: 2px;
	*border-bottom-width: 2px;
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	border-radius: 6px;
	-webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
	-moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
	box-shadow: 0 5px 10px rgba(0,0,0,.2);
	-webkit-background-clip: padding-box;
	-moz-background-clip: padding;
	background-clip: padding-box;
}

.tt-suggestion {
	display: block;
	padding: 3px 20px;
}

.tt-suggestion.tt-is-under-cursor {
	color: #fff;
	background-color: #0081c2;
	background-image: -moz-linear-gradient(top, #0088cc, #0077b3);
	background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0077b3));
	background-image: -webkit-linear-gradient(top, #0088cc, #0077b3);
	background-image: -o-linear-gradient(top, #0088cc, #0077b3);
	background-image: linear-gradient(to bottom, #0088cc, #0077b3);
	background-repeat: repeat-x;
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff0088cc', endColorstr='#ff0077b3', GradientType=0)
}

.tt-suggestion.tt-is-under-cursor a {
	color: #fff;
}

.tt-suggestion p {
	margin: 0;
}
</style>

<script>
$('.twitter-typeahead').typeahead({
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