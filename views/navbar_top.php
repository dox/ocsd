<style>
	#quick_search_results {
		position: absolute;
		z-index: 1;
		max-height: 200px;
		overflow-y: auto;
	}
</style>

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
			"title" => "All",
			"link" => "./index.php?n=persons_all&filter=all"
		),
		array(
			"title" => "Staff",
			"link" => "./index.php?n=persons_all&filter=staff"
		),
		array(
			"title" => "Students",
			"link" => "./index.php?n=persons_all&filter=students"
		),
		array(
			"title" => "Suspended",
			"link" => "./index.php?n=persons_all&filter=suspended"
		),
		array(
			"title" => "Under 18",
			"link" => "./index.php?n=persons_all&filter=underage"
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
					<input type="text" id="quick_search" class="form-control" placeholder="Quick search" autocomplete="off" spellcheck="false">
					<ul id="quick_search_results" class="list-group"></ul>
					
				</div>
			</form>
			
			<div class="d-flex">
				
			  <ul class="navbar-nav mr-auto">
				<li class="nav-item dropdown">
				  <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false"><img class="rounded-circle me-1" style="object-fit: cover;" width="32" height="32" src="<?php echo $_SESSION['avatar_url'] ; ?>" alt=""></a>
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
	document.getElementById('quick_search').addEventListener('keyup', function() {
		let query = this.value;
		
		// Create a new XMLHttpRequest object
		let xhr = new XMLHttpRequest();
		
		// Configure it: GET-request for the URL with the query
		xhr.open('GET', 'actions/search.php?search=' + encodeURIComponent(query), true);
		
		// Set up the callback function
		xhr.onload = function() {
			if (xhr.status === 200) {
				// Parse JSON response
				let response = JSON.parse(xhr.responseText);
				
				// Display the results
				let resultsDiv = document.getElementById('quick_search_results');
				resultsDiv.innerHTML = '';
		
				if (response.data.length === 0) {
					let listItem = document.createElement('li');
					listItem.className = "list-group-item";
					listItem.textContent = 'No results found';
					
					resultsDiv.appendChild(listItem);
				} else {
					response.data.forEach(function(item) {
						let listItem = document.createElement('li');
						listItem.className = "list-group-item";
						var link = document.createElement("a");
						link.className = "text-truncate";
						link.href = "index.php?n=person_unique&cudid=" + item.cudid;
						link.textContent = item.name;
						listItem.appendChild(link);
						
						resultsDiv.appendChild(listItem);
					});
				}
			}
		};
		
		// Send the request
		xhr.send();
	});
</script>

