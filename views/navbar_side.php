<?php
if (isset($_SESSION['username'])) {
?>
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
	<div class="sidebar-sticky pt-3">
		<ul class="nav flex-column">
			<li class="nav-item"><a class="nav-link active" href="index.php"><span data-feather="home"></span><i class="fas fa-home"></i> Dashboard <span class="sr-only">(current)</span></a></li>
			<li class="nav-item"><a class="nav-link" href="index.php?n=persons_all"><span data-feather="file"></span><i class="fas fa-user-friends"></i> Persons</a></li>			
			<li class="nav-item"><a class="nav-link" href="index.php?n=emergency_email"><span data-feather="file"></span><i class="fas fa-envelope"></i> Emergency Email</a></li>
			<li class="nav-item"><a class="nav-link" href="index.php?n=admin_logs"><span data-feather="file"></span><i class="fas fa-cogs"></i> Logs</a></li>
		</ul>
		
		<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
			<span>Saved reports</span>
			<a class="d-flex align-items-center text-muted" href="#" aria-label="Add a new report"><span data-feather="plus-circle"></span></a>
		</h6>
		
		<ul class="nav flex-column mb-2">
			<li class="nav-item"><a class="nav-link" href="report.php?n=ad_mifare"><span data-feather="file"></span><i class="fas fa-code"></i> AD miFare (PaperCut)</a></li>
			<li class="nav-item">
				<div class="accordion" id="accordionExample">
					<a href="#" class="nav-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne"><i class="fas fa-camera"></i> Photo Reports</a>
				</div>
				<div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
					<div class="card-body">
						<a class="dropdown-item" href="report.php?n=photo_by_year">All</a>
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
					</div>
				</div>
			<li>
		</ul>
	</div>
</nav>
<?php
}
?>