<style>
	#quick_search_results {
		position: absolute;
		z-index: 1;
		max-height: 200px;
		overflow-y: auto;
	}
</style>

<?php
$navbarArray['cud_person'] = array(
	"title" => "CUD Persons",
	"icon" => "person",
	"sublinks" => array(
		array(
			"title" => "All",
			"link" => "index.php?page=cud_persons"
		),
		array(
			"title" => "Staff",
			"link" => "index.php?page=cud_persons&filter=staff"
		),
		array(
			"title" => "Students",
			"link" => "index.php?page=cud_persons&filter=students"
		),
		array(
			"title" => "Suspended",
			"link" => "index.php?page=cud_persons&filter=suspended"
		),
		array(
			"title" => "Under 18",
			"link" => "index.php?page=cud_persons&filter=underage"
		),
		array(
			"title" => "Test",
			"link" => "index.php?page=cud_persons&filter=test"
		)
	)
);

$navbarArray['photos'] = array(
	"title" => "Photo Reports",
	"icon" => "filetype-pdf",
	"sublinks" => array(
		array(
			"title" => "All",
			"link" => "./report.php?n=photo_by_year"
		)
	)
);

$navbarArray['test'] = array(
	"title" => "Test",
	"icon" => "search",
	"link" => "./index.php?page=test"
);

$sql = "SELECT (crs_start_dt DIV 10000) AS crs_start_dt FROM `Person` WHERE crs_start_dt IS NOT NULL GROUP BY (crs_start_dt DIV 10000) ORDER BY crs_start_dt DESC";
$years = $db->query($sql);
foreach ($years AS $year) {
	$navbarArray['photos']['sublinks'][] = array(
		"title" => " - " . $year['crs_start_dt'],
		"link" => "export.php?page=export_photos&crs_start_dt=" . $year['crs_start_dt']
	);
}

$navbarArray['ldap_all'] = array(
	"title" => "LDAP",
	"icon" => "person-fill-lock",
	"sublinks" => array(
		array(
			"title" => "<form action=\"./index.php?page=ldap_users&filter=search\" method=\"POST\" target=\"_self\"><input type=\"text\" class=\"form-control\" id=\"ldap_search\" name=\"ldap_search\" placeholder=\"Search LDAP...\" title=\"LDAP Search\" ></form>",
			"link" => "#"
		),
		array(
			"title" => "LDAP no CUD",
			"link" => "index.php?page=ldap_users&filter=ldap-no-cud"
		),
		array(
			"title" => "CUD no LDAP",
			"link" => "index.php?page=cud_persons&filter=cud-no-ldap"
		),
		array(
			"title" => "Expiring Soon",
			"link" => "index.php?page=ldap_users&filter=expiring"
		),
		array(
			"title" => "Stale",
			"link" => "index.php?page=ldap_users&filter=expired"
		),
		array(
			"title" => "Stale Workstations",
			"link" => "index.php?page=ldap_computers&filter=expiring-workstations"
		),
		array(
			"title" => "Test",
			"link" => "index.php?page=ldap_users&filter=test"
		)
	)
);

?>


<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	<div class="container">
		<a class="navbar-brand" href="index.php"><?php echo icon('ocsd', '22') . " " . site_name; ?></a>
		
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample09" aria-controls="navbarsExample09" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		
		<div class="collapse navbar-collapse" id="navbarsExample09">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
				<?php
				foreach ($navbarArray AS $key => $navBarLink) {
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
						$output .= icon($navBarLink['icon']);
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
			
			<form role="search">
				<div class="auto-search-wrapper">
					<input type="text" id="quick_search" class="form-control" placeholder="Quick search" autocomplete="off" spellcheck="false">
					<ul id="quick_search_results" class="list-group"></ul>
				</div>
			</form>
			
			<div class="d-flex">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item dropdown">
						<button class="btn btn-link nav-link mt-1 dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" data-bs-display="static" aria-label="Toggle theme (dark)">
							<svg class="theme-icon-active" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><!-- Default icon will be replaced by JS --></svg>
						</button>
						<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme">
							<li>
								<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" data-icon-id="icon-light" aria-pressed="false">
									<?php echo icon('brightness-down');?>Light
								</button>
							</li>
							<li>
								<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" data-icon-id="icon-dark" aria-pressed="false">
									<?php echo icon('moon');?>Dark
								</button>
							</li>
							<li>
								<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto" data-icon-id="icon-auto" aria-pressed="false">
									<?php echo icon('brightness');?>Auto
								</button>
							</li>
						</ul>
					</li>
					<li class="nav-item py-2 py-lg-1 col-12 col-lg-auto">
						<div class="vr d-none d-lg-flex h-100 mx-lg-2 text-white"></div>
						<hr class="d-lg-none my-2 text-white-50">
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false"><?php echo icon('person-circle');?></a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="index.php?page=ldap_user&samaccountname=<?php echo $_SESSION['username']; ?>"><?php echo $_SESSION['username'] . "<br />" . $_SESSION["user_type"]; ?></a></li>
							<li><a class="dropdown-item" href="index.php?page=settings"><?php echo icon('gear');?> Settings</a></li>
							<li><a class="dropdown-item" href="index.php?page=scheduled_tasks"><?php echo icon('list-task');?> Scheduled Tasks</a></li>
							<li><a class="dropdown-item" href="index.php?page=logs"><?php echo icon('clock-history');?> Logs</a></li>
							<li><hr class="dropdown-divider"></li>
							<li><a class="dropdown-item" href="logout.php"><?php echo icon('box-arrow-right');?> Sign out</a></li>
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
	xhr.open('GET', 'actions/cud_persons.php?search=' + encodeURIComponent(query), true);
	
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
					link.href = "index.php?page=cud_person&cudid=" + item.cudid;
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
