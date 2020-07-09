<header class="navbar navbar-expand-md navbar-light">
		<div class="container-xl">
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-menu">
				<span class="navbar-toggler-icon"></span>
			</button>
			<a href="." class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pr-0 pr-md-3">
				<img src="images/logo.svg" alt="OCSD" class="navbar-brand-image">
				<?php
				if (debug == true) {
					echo "<button type=\"button\" class=\"btn btn-sm btn-warning\">DEBUG ENABLED</button>";
				}?>
			</a>
			<div class="navbar-nav flex-row order-md-last">
				<div class="nav-item dropdown d-none d-md-flex mr-3">
					<a href="#" class="nav-link px-0" data-toggle="dropdown" tabindex="-1">
						<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" /><path d="M9 17v1a3 3 0 0 0 6 0v-1" /></svg>
						<span class="badge bg-red"></span>
					</a>

					<div class="dropdown-menu dropdown-menu-right dropdown-menu-card">
						<div class="card">
							<div class="card-body">
								This system is massivley still in development!  It's not even at beta yet.  So don't use it.  You've been warned...
							</div>
						</div>
					</div>
				</div>
				<div class="nav-item dropdown">
					<a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-toggle="dropdown">
						<span class="avatar" style="background-image: url(./static/avatars/000m.jpg)"></span>
						<div class="d-none d-xl-block pl-2">
							<div><?php echo $_SESSION['username']; ?></div>
							<div class="mt-1 small text-muted">Administrator</div>
						</div>
					</a>
					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item" href="#">
							<svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><circle cx="12" cy="12" r="3" /></svg>
							Action
						</a>
						<a class="dropdown-item" href="#">
							<svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" /><path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" /><line x1="16" y1="5" x2="19" y2="8" /></svg>
							Another action
						</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="./index.php?n=admin_logon&logout=true">
							<svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"></path><path d="M7 12h14l-3 -3m0 6l3 -3"></path></svg>
							Sign Out</a>
					</div>
				</div>
			</div>
		</div>
	</header>
	<div class="navbar-expand-md">
		<div class="collapse navbar-collapse" id="navbar-menu">
			<div class="navbar navbar-light">
				<div class="container-xl">
					<ul class="navbar-nav">
						<li class="nav-item active">
							<a class="nav-link" href="./index.php" >
								<span class="nav-link-icon d-md-none d-lg-inline-block"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><polyline points="5 12 3 12 12 3 21 12 19 12" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
								</span>
								<span class="nav-link-title">
									Home
								</span>
							</a>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#navbar-base" data-toggle="dropdown" role="button" aria-expanded="false" >
								<span class="nav-link-icon d-md-none d-lg-inline-block"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="7" r="4"></circle><path d="M5.5 21v-2a4 4 0 0 1 4 -4h5a4 4 0 0 1 4 4v2"></path></svg>
								</span>
								<span class="nav-link-title">
									Persons
								</span>
							</a>
							<ul class="dropdown-menu dropdown-menu">
								<li >
									<a class="dropdown-item" href="./index.php?n=persons_all&filter=students" >
										Students
									</a>
								</li>
								<li >
									<a class="dropdown-item" href="./index.php?n=persons_all&filter=staff" >
										Staff
									</a>
								</li>
								<li >
									<a class="dropdown-item" href="./index.php?n=persons_all" >
										All
									</a>
								</li>
							</ul>
						</li>
						<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle" href="#navbar-base" data-toggle="dropdown" role="button" aria-expanded="false" >
										<span class="nav-link-icon d-md-none d-lg-inline-block"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="15" y1="8" x2="15.01" y2="8"></line><rect x="4" y="4" width="16" height="16" rx="3"></rect><path d="M4 15l4 -4a3 5 0 0 1 3 0l 5 5"></path><path d="M14 14l1 -1a3 5 0 0 1 3 0l 2 2"></path></svg>
										</span>
										<span class="nav-link-title">
											Photo Reports
										</span>
									</a>
									<ul class="dropdown-menu">
										<li>
											<a class="dropdown-item" href="./report.php?n=photo_by_year" >All</a>
										</li>
									<?php
									$currentYear = date('Y');
									$yearOutput = $currentYear;
									$totalYears = 6;
									$output = "";


									$i = 1;
									do {
										$output .= "<li><a href=\"./report.php?n=photo_by_year&cohort=" . $i . "\" class=\"dropdown-item\">- " . $yearOutput . "</a></li>";
										$yearOutput = $yearOutput - 1;
										$i++;
									} while ($i <= $totalYears);

									echo $output;
									?>
									</ul>
							</li>
						<li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#navbar-base" data-toggle="dropdown" role="button" aria-expanded="false" >
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><rect x="5" y="11" width="14" height="10" rx="2"></rect><circle cx="12" cy="16" r="1"></circle><path d="M8 11v-4a4 4 0 0 1 8 0v4"></path></svg>
                    </span>
                    <span class="nav-link-title">
                      LDAP
                    </span>
                  </a>
                  <ul class="dropdown-menu ">
										<li>
											<a class="dropdown-item" href="./index.php?n=ldap_all&filter=all" >All</a>
										</li>
										<li>
                      <a class="dropdown-item" href="./index.php?n=ldap_all&filter=ldap-no-cud" >LDAP no CUD</a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="./index.php?n=ldap_all&filter=cud-no-ldap" >CUD no LDAP</a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="./index.php?n=ldap_all&filter=expiring" >Expiring Soon</a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="./index.php?n=ldap_all&filter=stale" >Stale</a>
                    </li>
                	</ul>
              </li>
						<li class="nav-item">
							<a class="nav-link" href="./index.php?n=emergency_email" >
								<span class="nav-link-icon d-md-none d-lg-inline-block"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><rect x="3" y="5" width="18" height="14" rx="2"></rect><polyline points="3 7 12 13 21 7"></polyline></svg></span>
								<span class="nav-link-title">
									Emergency Email
								</span>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="./index.php?n=admin_logs" >
								<span class="nav-link-icon d-md-none d-lg-inline-block"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><circle cx="12" cy="12" r="9"></circle><line x1="12" y1="8" x2="12.01" y2="8"></line><polyline points="11 12 12 12 12 16 13 16"></polyline></svg></span>
								<span class="nav-link-title">
									Logs
								</span>
							</a>
						</li>
					</ul>
					<div class="my-2 my-md-0 flex-grow-1 flex-md-grow-0 order-first order-md-last">
						<form action="./index.php?n=persons_all&filter=search" method="POST" target="_self">
							<div class="input-icon">
								<span class="input-icon-addon">
									<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><circle cx="10" cy="10" r="7" /><line x1="21" y1="21" x2="15" y2="15" /></svg>
								</span>
								<input type="text" class="form-control typeahead" name="navbar_search" id="navbar_search" placeholder="Search…" tabindex="1" autocomplete="off" spellcheck="false">
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
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
