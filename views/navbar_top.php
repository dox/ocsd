<?php
$navbarArray['home'] = array(
	"title" => "Home",
	"icon" => "home",
	"link" => "./index.php"
);

$navbarArray['persons_all'] = array(
	"title" => "Persons",
	"icon" => "person",
	"sublinks" => array(
		array(
			"title" => "Suspended",
			"link" => "./index.php?n=persons_all&filter=suspended"
		),
		array(
			"title" => "Students",
			"link" => "./index.php?n=persons_all&filter=students"
		),
		array(
			"title" => "Staff",
			"link" => "./index.php?n=persons_all&filter=staff"
		),
		array(
			"title" => "All",
			"link" => "./index.php?n=persons_all&filter=all"
		)
	)
);

$navbarArray['photos'] = array(
	"title" => "Photo Reports",
	"icon" => "photo",
	"sublinks" => array(
		array(
			"title" => "All",
			"link" => "./report.php?n=photo_by_year"
		)
	)
);

$currentYear = date('Y');
$yearOutput = $currentYear;
$totalYears = 6;
$i = 1;
do {
	$navbarArray['photos']['sublinks'][] = array(
		"title" => " - " . $yearOutput,
		"link" => "./report.php?n=photo_by_year&cohort=" . $i
	);

	$yearOutput = $yearOutput - 1;
	$i++;
} while ($i <= $totalYears);

$navbarArray['ldap_all'] = array(
	"title" => "LDAP",
	"icon" => "ldap",
	"sublinks" => array(
		array(
			"title" => "<form action=\"./index.php?n=ldap_all&filter=search\" method=\"POST\" target=\"_self\"><input type=\"text\" class=\"form-control\" id=\"ldap_search\" name=\"ldap_search\" placeholder=\"Search LDAP...\" title=\"LDAP Search\" ></form>",
			"link" => "#"
		),
		array(
			"title" => "LDAP no CUD",
			"link" => "./index.php?n=ldap_all&filter=ldap-no-cud"
		),
		array(
			"title" => "CUD no LDAP",
			"link" => "./index.php?n=ldap_all&filter=cud-no-ldap"
		),
		array(
			"title" => "Expiring Soon",
			"link" => "./index.php?n=ldap_all&filter=expiring"
		),
		array(
			"title" => "Stale",
			"link" => "./index.php?n=ldap_all&filter=stale"
		),
		array(
			"title" => "Stale Workstations",
			"link" => "./index.php?n=ldap_all&filter=stale-workstations"
		),
		array(
			"title" => "All",
			"link" => "./index.php?n=ldap_all&filter=all"
		)
	)
);

$navbarArray['emergency_email'] = array(
	"title" => "Email",
	"icon" => "email",
	"link" => "./index.php?n=emergency_email"
);

$navbarArray['admin_logs'] = array(
	"title" => "Logs",
	"icon" => "logs",
	"link" => "./index.php?n=admin_logs"
);
?>

<?php
if (debug == true) {
	echo "<button type=\"button\" class=\"btn btn-sm btn-warning\">DEBUG ENABLED</button>";
}
?>

<nav class="py-2 bg-light border-bottom">
  <div class="container d-flex flex-wrap">
    <ul class="nav me-auto">
			<?php
			foreach ($navbarArray AS $key => $navBarLink) {
				$icon = "<svg width=\"1em\" height=\"1em\" class=\"me-2\"><use xlink:href=\"images/icons.svg#" . $navBarLink['icon'] . "\"/></svg>";

				if ($key == $_GET['n']) {
					$active = " active";
				} else {
					if (!isset($_GET['n']) && $key == "home") {
						$active = " active";
					} else {
						$active = "";
					}
				}

				if (is_array($navBarLink['sublinks'])) {
					$output  = "<li class=\"nav-item dropdown\">";
					$output .= "<a class=\"nav-link link-dark px-2 dropdown-toggle " . $active . "\" href=\"#navbar-base\" role=\"button\" data-bs-toggle=\"dropdown\" >";
					$output .= $icon;
					$output .= $navBarLink['title'];
					$output .= "</a>";

					$output .= "<ul class=\"dropdown-menu\">";
					foreach ($navBarLink['sublinks'] AS $sublink) {
						$output .= "<li >";
						$output .= "<a class=\"dropdown-item\" href=\"" . $sublink['link'] . "\" >" . $sublink['title'] . "</a>";
						$output .= "</li>";
					}
					$output .= "</ul>";
				} else {
					$output  = "<li class=\"nav-item\">";
					$output .= "<a class=\"nav-link link-dark px-2 " . $active . "\" href=\"" . $navBarLink['link'] . "\" >";
					$output .= $icon;
					$output .= $navBarLink['title'];
					$output .= "</a>";
					$output .= "</li>";
				}
				echo $output;
			}
			?>
    </ul>
    <!--<ul class="nav">
      <li class="nav-item"><a href="#" class="nav-link link-dark px-2">Login</a></li>
      <li class="nav-item"><a href="#" class="nav-link link-dark px-2">Sign up</a></li>
    </ul>-->

		<div class="nav-item dropdown me-2">
			<a href="#" class="nav-link link-dark" data-bs-toggle="dropdown" tabindex="-1">
				<svg width="1em" height="1em" class=""><use xlink:href="images/icons.svg#bell"/></svg>
			</a>

			<div class="dropdown-menu dropdown-menu-end dropdown-menu-card">
					<div class="card-body">
						This system is massivley still in development!  It's not even at beta yet.  So don't use it.  You've been warned...
					</div>
			</div>
		</div>

		<div class="nav-item dropdown">
			<a href="#" class="nav-link d-flex lh-1 text-reset " data-bs-toggle="dropdown" aria-label="Open user menu">
				<img class="avatar avatar-24 rounded-2" src="<?php echo $_SESSION['avatar_url'] ; ?>" />
				<div class="ps-2">
					<div><?php echo $_SESSION['username']; ?></div>
					<div class="mt-1 small text-muted text-end"><?php echo $_SESSION["user_type"]; ?></div>
				</div>
			</a>
			<div class="dropdown-menu dropdown-menu-start dropdown-menu-arrow">
				<a class="dropdown-item" href="./index.php?n=persons_unique&cudid=<?php echo $_SESSION['cudid'];?>">
					<svg width="1em" height="1em" class="me-2"><use xlink:href="images/icons.svg#person"/></svg> My CUD Profile
				</a>
				<a class="dropdown-item" href="./index.php?n=ldap_unique&samaccountname=<?php echo $_SESSION['username'];?>">
					<svg width="1em" height="1em" class="me-2"><use xlink:href="images/icons.svg#ldap"/></svg> My LDAP Record
				</a>
				<div class="dropdown-divider"></div>
				<a class="dropdown-item" href="./index.php?n=admin_settings">
					<svg width="1em" height="1em" class="me-2"><use xlink:href="images/icons.svg#ldap"/></svg> Admin. Settings
				</a>
				<div class="dropdown-divider"></div>
				<a class="dropdown-item" href="./index.php?n=admin_logon&logout=true">
					<svg width="1em" height="1em" class="me-2"><use xlink:href="images/icons.svg#signout"/></svg> Sign Out
				</a>
			</div>
		</div>

  </div>
</nav>
<header class="py-3 mb-4 border-bottom">
  <div class="container d-flex flex-wrap justify-content-center">
    <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none me-3">
			<svg width="3em" height="3em" class="text-primary me-3">
				<use xlink:href="images/icons.svg#ocsd-logo"/>
			</svg> <span class="fs-4">OCSD</span>
    </a>
		<form class="col-6 mb-3 mb-lg-0" action="./index.php?n=persons_all&filter=search" method="POST" target="_self">
			<input class="form-control typeahead" type="search" placeholder="Search CUD" name="navbar_search" id="navbar_search" aria-label="Search" autocomplete="off" spellcheck="false">
		</form>
  </div>
</header>

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
