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

<nav class="navbar navbar-expand-lg bg-body-tertiary rounded" aria-label="Eleventh navbar example">
	<div class="container">
		<a class="navbar-brand" href="index.php"><svg class="me-2 text-primary" width="2em" height="2em" role="img" aria-label="OCSD"><use xlink:href="images/icons.svg#ocsd-logo"/></svg></a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample09" aria-controls="navbarsExample09" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		
		<div class="collapse navbar-collapse" id="navbarsExample09">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
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
						$output .= "<a class=\"nav-link px-2 dropdown-toggle " . $active . "\" href=\"#navbar-base\" role=\"button\" data-bs-toggle=\"dropdown\" >";
						$output .= $icon;
						$output .= $navBarLink['title'];
						$output .= "</a>";
						
						$output .= "<ul class=\"dropdown-menu\">";
						
						foreach ($navBarLink['sublinks'] AS $sublink) {
							$output .= "<li>";
							$output .= "<a class=\"dropdown-item px-2\" href=\"" . $sublink['link'] . "\" >" . $sublink['title'] . "</a>";
							$output .= "</li>";
						}
						
						$output .= "</ul>";
					} else {
						$output  = "<li>";
						$output .= "<a class=\"nav-link px-2 " . $active . "\" href=\"" . $navBarLink['link'] . "\" >";
						$output .= $icon;
						$output .= $navBarLink['title'];
						$output .= "</a>";
						$output .= "</li>";
					}
					
					echo $output;
				}
				?>
			</ul>
			
			<div class="d-flex">
				<ul class="navbar-nav me-auto mb-2 mb-md-0">
				  <li class="nav-item dropdown me-2">
					<a class="nav-link dropdown-toggle d-print-none theme-icon-active" id="bd-theme" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
						<svg width="1em" height="1em" class="text-muted">
							<use xlink:href="images/icons.svg#dark-mode"/>
						</svg><span class="visually-hidden" id="bd-theme-text">Toggle theme</span></a>
					<ul class="dropdown-menu">
					  <li><button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
							  <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="images/icons.svg#light-mode"></use></svg>
							  Light
							  <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
							</button>
						</li>
						<li>
							<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
								<svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="images/icons.svg#dark-mode"></use></svg>
								Dark
								<svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
							  </button>
						</li>
						<li>
							<button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
								<svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="images/icons.svg#auto-mode"></use></svg>
								Auto
								<svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
							  </button>
						</li>
					</ul>
				  </li>
				</ul>
				  
			</div>
			
			<form role="search">
				<div class="auto-search-wrapper">
					<input class="form-control" type="search" placeholder="Quick Search" aria-label="Quick Search" id="basic" autocomplete="off" spellcheck="false">
				</div>
			</form>
			
			<div class="d-flex">
				
			  <ul class="navbar-nav mr-auto">
				<li class="nav-item dropdown">
				  <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false"><img class="rounded-circle me-1" width="32" height="32" src="<?php echo $_SESSION['avatar_url'] ; ?>" alt=""></a>
				  <ul class="dropdown-menu">
					<li><a class="dropdown-item" href="#"><?php echo $_SESSION['username'] . "<br />" . $_SESSION["user_type"]; ?></a></li>
					<li><a class="dropdown-item" href="./index.php?n=person_unique&cudid=<?php echo $_SESSION['cudid'];?>"><svg width="1em" height="1em" class="me-2"><use xlink:href="images/icons.svg#person"/></svg> My CUD Profile</a></li>
					<li><a class="dropdown-item" href="./index.php?n=ldap_unique&samaccountname=<?php echo $_SESSION['username'];?>"><svg width="1em" height="1em" class="me-2"><use xlink:href="images/icons.svg#ldap"/></svg> My LDAP Record</a></li>
					<li><a class="dropdown-item" href="./index.php?n=admin_settings"><svg width="1em" height="1em" class="me-2"><use xlink:href="images/icons.svg#settings"/></svg> Admin. Settings</a></li>
					<li><a class="dropdown-item" href="./index.php?n=admin_logon&logout=true"><svg width="1em" height="1em" class="me-2"><use xlink:href="images/icons.svg#signout"/></svg> Sign out</a></li>
				  </ul>
				</li>
			  </ul>
			</div>
		</div>
	</div>
</nav>

<script>
new Autocomplete("basic", {
	onSearch: ({ currentValue }) => {
		const api = `actions/search.php?search=${encodeURI(currentValue)}`;
		
		return new Promise((resolve) => {
			fetch(api)
			.then((response) => response.json())
			.then((data) => {
				resolve(data);
			})
			.catch((error) => {
				console.error(error);
			});
		});
	},
	onResults: ({ matches }) =>
	matches.map((el) => `<li>${el.name}</li>`).join(""),
	onSubmit: ({ index, element, object }) => {
		const { name, cudid } = object;
		
		window.location = "index.php?n=person_unique&cudid=" + cudid;
	},
});
</script>
