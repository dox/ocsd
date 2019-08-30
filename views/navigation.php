<div class="en ble">
<nav class="bll">
	<div class="blf">
		<img src="../images/logo.svg" style="max-width: 130px;" class="rounded mx-auto d-block">
		<button class="bkb bkd blg" type="button" data-toggle="collapse" data-target="#nav-toggleable-md"><span class="yz">Toggle nav</span></button>
	</div>
	<div class="collapse bki" id="nav-toggleable-md">
		<form action="index.php?n=search_results" method="POST" target="_self" class="blj">
			<input class="form-control" name="search_term" type="text" placeholder="Search...">
			<button type="submit" class="ku"><i class="fas fa-search"></i></button>
		</form>
		<ul class="nav lq nav-stacked st">
			<li class="asv">Navigation</li>
			<li class="lp"><a class="ln active" href="index.php">Home</a></li>
			<li class="lp"><a class="ln " href="index.php?n=persons_all">Persons</a></li>
			
			<li class="asv">Reports</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Photo Report</a>
				<div class="dropdown-menu">
					<?php
						$currentYear = date('Y');
						$yearOutput = $currentYear;
						$totalYears = 6;
						$output = "";
						
						$i = 1;
						do {
							$output .= "<a class=\"dropdown-item\" href=\"/report.php?n=photo_by_year&cohort=" . $i . "\">" . $yearOutput . "</a>";
							$yearOutput = $yearOutput - 1;
							$i++;
						} while ($i <= $totalYears);
						
						echo $output;
					?>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="/report.php?n=photo_by_year">All</a>
				</div>
			</li>
			
			<li class="asv">Admin</li>
			<li class="lp"><a class="ln" href="index.php?n=admin_logs">Logs</a></li>
			<li class="lp"><a class="ln" href="index.php?n=admin_logon&logout=true">Log Out</a></li>
		</ul>
		<hr class="bmi aah">
	</div>
</nav>
</div>