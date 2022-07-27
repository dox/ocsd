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

<header class="p-3 mb-3 border-bottom bg-light shadow ">
	<div class="container">
		<div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
			<a href="index.php" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
				<svg class="me-2 text-primary" width="2em" height="2em" role="img" aria-label="OCSD"><use xlink:href="images/icons.svg#ocsd-logo"/></svg>
			</a>
			
			<ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
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
						$output  = "<li>";
						$output .= "<a class=\"nav-link px-2 link-dark dropdown-toggle " . $active . "\" href=\"#navbar-base\" role=\"button\" data-bs-toggle=\"dropdown\" >";
						$output .= $icon;
						$output .= $navBarLink['title'];
						$output .= "</a>";
						
						$output .= "<ul class=\"dropdown-menu\">";
						
						foreach ($navBarLink['sublinks'] AS $sublink) {
							$output .= "<li>";
							$output .= "<a class=\"nav-link px-2 link-dark dropdown-item\" href=\"" . $sublink['link'] . "\" >" . $sublink['title'] . "</a>";
							$output .= "</li>";
						}
						
						$output .= "</ul>";
					} else {
						$output  = "<li>";
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
			
			<?php
			if (debug == true) {
				$output  = "<button type=\"button\" class=\"btn btn-warning me-3\">";

				//$output .= "<svg width=\"1em\" height=\"1em\" class=\"mx-2\"><use xlink:href=\"images/icons.svg#alert\"/></svg>";
				$output .= "<strong>DEBUG ENABLED!</strong>";
				//$output .= "<svg width=\"1em\" height=\"1em\" class=\"mx-2\"><use xlink:href=\"images/icons.svg#alert\"/></svg>";
				$output .= "</button>";
				
				echo $output;
			}
			?>
			
			<!--<form class="" action="./index.php?n=persons_all&filter=search" method="POST" target="_self">-->
				<div class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3 auto-search-wrapper max-height loupe">

				  <input type="text" class="form-control" id="basic" placeholder="Search CUD" aria-label="Search" autocomplete="off" spellcheck="false">
				</div>
			
			<div class="dropdown text-end">
				<a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
					<img src="<?php echo $_SESSION['avatar_url'] ; ?>" alt="mdo" width="32" height="32" class="rounded-circle">
				</a>
				
				<ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1">
					<li>
						<?php echo $_SESSION['username']; ?> / <div class="mt-1 small text-muted text-end"><?php echo $_SESSION["user_type"]; ?></div>
					</li>
					<li>
						<a class="dropdown-item" href="./index.php?n=person_unique&cudid=<?php echo $_SESSION['cudid'];?>"><svg width="1em" height="1em" class="me-2"><use xlink:href="images/icons.svg#person"/></svg> My CUD Profile</a>
					</li>
					<li>
						<a class="dropdown-item" href="./index.php?n=ldap_unique&samaccountname=<?php echo $_SESSION['username'];?>"><svg width="1em" height="1em" class="me-2"><use xlink:href="images/icons.svg#ldap"/></svg> My LDAP Record</a>
					</li>
					<li>
						<a class="dropdown-item" href="./index.php?n=admin_settings"><svg width="1em" height="1em" class="me-2"><use xlink:href="images/icons.svg#ldap"/></svg> Admin. Settings</a>
					</li>
					<li>
						<hr class="dropdown-divider">
					</li>
					<li>
						<a class="dropdown-item" href="./index.php?n=admin_logon&logout=true"><svg width="1em" height="1em" class="me-2"><use xlink:href="images/icons.svg#signout"/></svg> Sign out</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</header>




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
