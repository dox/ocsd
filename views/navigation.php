<script src="js/typeahead.jquery.min.js"></script>
<script src="js/bloodhound.min.js"></script>

<div class="navbar navbar-default navbar-fixed-top" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>

			<a class="navbar-brand" href="index.php"><?php echo SITE_SHORT_NAME; ?></a>
		</div>
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
				<li class="active"><a href="index.php">Home</a></li>
				<li><a href="index.php?m=students&n=index.php">Users</a></li>
				<li><a href="index.php?n=contact.php">Contact</a></li>
			</ul>
			<form class="navbar-form navbar-left" role="search">
				<div class="form-group">
					<!--<input type="text" class="form-control" placeholder="Search">-->
					<div id="nav-search">
					<?php
					if (isset($_SESSION['username'])) {
						echo "<input type=\"text\" class=\"form-control typeahead\" placeholder=\"Search\" autocomplete=\"off\" />";
					}
					?>
					</div>
				</div>
				<button type="submit" class="btn btn-default">SEARCH NOT WORKING</button>
			</form>
			
			<ul class="nav navbar-nav navbar-right">
				<li><a href="#">Link</a></li>
				<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="index.php?m=reports&n=index.php">Reports</a></li>
						<li><a href="index.php?m=awards&n=list.php">Awards</a></li>
						<li><a href="index.php?m=logs&n=index.php">Logs</a></li>
						<li class="divider"></li>
						<li><a href="index.php?n=profile.php">My Profile</a></li>
						<li><a href="index.php?n=contact.php">Report Problem</a></li>
						<li><a href="index.php?n=logon.php&logout=true">Log Out</a></li>
					</ul>
				</li>
			</ul>
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

var bestPictures = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  prefetch: './api/studentsAll.php',
  remote: './api/studentsAll.php'
});
 
bestPictures.initialize();
 
$('#nav-search .typeahead').typeahead(null, {
	hint: true,
	highlight: true,
//	minLength: 1,
	name: 'users-current',
	displayKey: 'name',
	source: bestPictures.ttAdapter(),
	templates: {
		empty: [
			'<div class="empty-message">',
			'unable to find any Best Picture winners that match the current query',
			'</div>'
		].join('\n')
	}
});




/*
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
*/
</script>