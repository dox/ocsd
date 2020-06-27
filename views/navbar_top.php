<?php
if (isset($_SESSION['username']) && !isset($_GET['logout'])) {
?>
<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
	<!--<img src="../images/logo.svg" style="max-height:45px;" class="rounded mx-auto d-block">-->
	<a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="index.php">OCSD</a>
	<button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<form class="w-100" action="index.php?n=search_results" method="POST" target="_self">
		<input class="form-control form-control-dark typeahead" type="text" name="navbar_search" id="navbar_search" placeholder="Search" aria-label="Search" autocomplete="off" spellcheck="false">
	</form>
	<ul class="navbar-nav px-3">
		<li class="nav-item text-nowrap"><a class="nav-link" href="index.php?n=admin_logon&logout=true">Sign out</a></li>
	</ul>
</nav>
<?php
if (debug == true) {
	echo "<nav class=\"navbar fixed-bottom navbar-light bg-warning\">";
	echo "<strong>DEBUG ENABLED</strong>";
	echo "</nav>";
}
?>

<?php
}
?>

<script>
$('#navbar_search').autocomplete({
	serviceUrl: 'api/person/navbar_search.php',
	lookupLimit: 5,
	type: "POST",
	dataType: "json",
	params: {
		"api_token": "<?php echo api_token; ?>",
  },
	paramName: "navbar_search",
	onSelect: function (suggestion) {
		window.location.href='index.php?n=persons_unique&cudid=' + suggestion.data;
	}
});
</script>
