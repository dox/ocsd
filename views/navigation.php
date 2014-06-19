<script src="js/typeahead.jquery.min.js"></script>
<script src="js/handlebars.js"></script>
<script src="js/bloodhound.min.js"></script>

<div class="navbar navbar-default navbar-fixed-top" role="navigation">
	<div class="container">
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
			</form>
			
			<ul class="nav navbar-nav navbar-right">
				<li>
					<form class="navbar-form" action="index.php?n=contact.php" method="post">
						<a href="#" onclick="parentNode.submit();"><i class="fa fa-flag"></i></a>
						<input type="hidden" name="page" value="<?php echo curPageURL(); ?>"/>
					</form>
				</li>
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
.tt-query,
.tt-hint {
    width: 396px;
    height: 30px;
    padding: 8px 12px;
    font-size: 24px;
    line-height: 30px;
    border: 2px solid #ccc;
    border-radius: 8px;
    outline: none;
}

.tt-query {
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
}

.tt-hint {
    color: #999
}

.tt-dropdown-menu {
    width: 422px;
    margin-top: 12px;
    padding: 8px 0;
    background-color: #fff;
    border: 1px solid #ccc;
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 8px;
    box-shadow: 0 5px 10px rgba(0,0,0,.2);
}

.tt-suggestion {
    padding: 3px 20px;
    font-size: 18px;
    line-height: 24px;
}

.tt-suggestion.tt-is-under-cursor { /* UPDATE: newer versions use .tt-suggestion.tt-cursor */
    color: #fff;
    background-color: #0097cf;

}

.tt-suggestion p {
    margin: 0;
}</style>

<script>
var bestPictures = new Bloodhound({
	datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	prefetch: './api/studentsAll.php'
});

bestPictures.initialize();
 
$('#nav-search .typeahead').typeahead(null, {
	hint: true,
	highlight: true,
	minLength: 1,
	name: 'users-current',
	displayKey: 'value',
	source: bestPictures.ttAdapter(),
	templates: {
		empty: [
			'<div class="empty-message">',
			'<p>No students found</p>',
			'</div>'
		].join('\n'),
		suggestion: Handlebars.compile('<p><a href="index.php?m=students&n=user.php&studentid={{value}}">{{name}}</a></p>')
	}
});
</script>