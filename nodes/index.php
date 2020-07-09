<?php
$sql = "SELECT * FROM _stats WHERE name = 'person_rows_total' ORDER BY date_created DESC";
$statsPersonsTotals = $db->query($sql, 'test', 'test')->fetchAll();
foreach ($statsPersonsTotals AS $personTotal) {
	$personTotalArray["'" . date('Y-m-d', strtotime($personTotal['date_created'])) . "'"] = $personTotal['value'];
}
$personTotalArray = array_reverse($personTotalArray);


$sql = "SELECT * FROM _stats WHERE name = 'student_rows_total' ORDER BY date_created DESC";
$statsStudentTotals = $db->query($sql, 'test', 'test')->fetchAll();
foreach ($statsStudentTotals AS $studentTotal) {
	$studentTotalArray["'" . date('Y-m-d', strtotime($studentTotal['date_created'])) . "'"] = $studentTotal['value'];
}
$studentTotalArray = array_reverse($studentTotalArray);


$sql = "SELECT * FROM _logs WHERE type = 'LOGON' ORDER BY date_created DESC";
$logonsAll = $db->query($sql, 'test', 'test')->fetchAll();
$logonsAllCount = count($logonsAll);

$sql = "SELECT DATE(date_created) AS date_created, COUNT(*) AS cnt FROM _logs WHERE type = 'LOGON' GROUP BY DATE(date_created) ORDER BY date_created DESC";
$logonsByDay = $db->query($sql, 'test', 'test')->fetchAll();
foreach ($logonsByDay AS $day) {
	$logonsCountArray["'" . date('Y-m-d', strtotime($day['date_created'])) . "'"] = $day['cnt'];
}

$sql = "SELECT * FROM _logs WHERE type = 'VIEW' ORDER BY date_created DESC";
$logViewsAll = $db->query($sql, 'test', 'test')->fetchAll();
$logViewsAllCount = count($logViewsAll);

$sql = "SELECT DATE(date_created) AS date_created, COUNT(*) AS cnt FROM _logs WHERE type = 'VIEW' GROUP BY DATE(date_created) ORDER BY date_created DESC";
$logViewsByDay = $db->query($sql, 'test', 'test')->fetchAll();
foreach ($logViewsByDay AS $day) {
	$logViewsCountArray["'" . date('Y-m-d', strtotime($day['date_created'])) . "'"] = $day['cnt'];
}
$logViewsCountArray = array_reverse(array_slice($logViewsCountArray, 0, 7));
?>









<div class="content">
	<div class="container-xl">
		<!-- Page title -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col-auto">
					<!-- Page pre-title -->
					<div class="page-pretitle">
						Overview
					</div>
					<h2 class="page-title">
						Dashboard
					</h2>
				</div>
				<!-- Page title actions -->
				<div class="col-auto ml-auto d-print-none">
					<span class="d-none d-sm-inline">
						<a href="#" class="btn btn-white">
							New view
						</a>
					</span>
					<a href="#" class="btn btn-primary ml-3 d-none d-sm-inline-block" data-toggle="modal" data-target="#modal-report">
						<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
						Create new report
					</a>
					<a href="#" class="btn btn-primary ml-3 d-sm-none btn-icon" data-toggle="modal" data-target="#modal-report" aria-label="Create new report">
						<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
					</a>
				</div>
			</div>
		</div>
		<div class="row row-deck row-cards">
			<?php
			$personsCountCard = array(
				"id" => "persons_count",
				"area_type" => "area",
				"title" => "Persons Count",
				"count_total" => end($personTotalArray),
				"titles" => implode(array_keys($personTotalArray), ", "),
				"values" => implode($personTotalArray, ", ")
			);
			$otherCard1 = array(
				"id" => "students_count",
				"area_type" => "area",
				"title" => "Students Count",
				"count_total" => end($studentTotalArray),
				"titles" => implode(array_keys($studentTotalArray), ", "),
				"values" => implode($studentTotalArray, ", ")
			);
			$otherCard2 = array(
				"id" => "logons_count",
				"area_type" => "line",
				"title" => "Logons Count",
				"count_total" => array_sum($logonsCountArray),
				"titles" => implode(array_keys($logonsCountArray), ", "),
				"values" => implode($logonsCountArray, ", ")
			);
			$otherCard3 = array(
				"id" => "logs_count",
				"area_type" => "bar",
				"title" => "Logs Count",
				"count_total" => array_sum($logViewsAllCount),
				"titles" => implode(array_keys($logViewsAllCount), ", "),
				"values" => implode($logViewsAllCount, ", ")
			);
			echo cardWithGraph($personsCountCard);
			echo cardWithGraph($otherCard1);
			echo cardWithGraph($otherCard2);
			echo cardWithGraph($otherCard3);
			?>
<!--
			<div class="col-lg-7">
				<div class="card">
					<div class="card-body">
						<h3 class="card-title">Traffic summary</h3>
						<div id="chart-mentions" class="chart-lg"></div>
					</div>
				</div>
			</div>
			<div class="col-lg-5">
				<div class="card">
					<div class="card-body">
						<h3 class="card-title">Top countries</h3>
						<div class="embed-responsive embed-responsive-16by9">
							<div class="embed-responsive-item">
								<div id="map-world" class="w-100 h-100"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="row row-cards row-deck">
					<div class="col-sm-6">
						<div class="card">
							<div class="card-body p-4 py-5 text-center">
								<span class="avatar avatar-xl mb-4">W</span>
								<h3 class="mb-0">New website</h3>
								<p class="text-muted">Due to: 28 Aug 2019</p>
								<p class="mb-3">
									<span class="badge bg-red-lt">Waiting</span>
								</p>
								<div>
									<div class="avatar-list avatar-list-stacked">
										<span class="avatar" style="background-image: url(./static/avatars/000m.jpg)"></span>
										<span class="avatar">JL</span>
										<span class="avatar" style="background-image: url(./static/avatars/002m.jpg)"></span>
										<span class="avatar" style="background-image: url(./static/avatars/003m.jpg)"></span>
										<span class="avatar" style="background-image: url(./static/avatars/000f.jpg)"></span>
									</div>
								</div>
							</div>
							<div class="progress card-progress">
								<div class="progress-bar" style="width: 38%" role="progressbar" aria-valuenow="38" aria-valuemin="0" aria-valuemax="100">
									<span class="sr-only">38% Complete</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="card">
							<div class="card-body p-4 py-5 text-center">
								<span class="avatar avatar-xl mb-4 bg-green-lt">W</span>
								<h3 class="mb-0">UI Redesign</h3>
								<p class="text-muted">Due to: 11 Nov 2019</p>
								<p class="mb-3">
									<span class="badge bg-green-lt">Final review</span>
								</p>
								<div>
									<div class="avatar-list avatar-list-stacked">
										<span class="avatar">HS</span>
										<span class="avatar" style="background-image: url(./static/avatars/006m.jpg)"></span>
										<span class="avatar" style="background-image: url(./static/avatars/004f.jpg)"></span>
									</div>
								</div>
							</div>
							<div class="progress card-progress">
								<div class="progress-bar bg-green" style="width: 38%" role="progressbar" aria-valuenow="38" aria-valuemin="0" aria-valuemax="100">
									<span class="sr-only">38% Complete</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="card">
							<div class="card-body p-2 text-center">
								<div class="text-right text-green">
									<span class="text-green d-inline-flex align-items-center lh-1">
										6% <svg xmlns="http://www.w3.org/2000/svg" class="icon ml-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><polyline points="3 17 9 11 13 15 21 7" /><polyline points="14 7 21 7 21 14" /></svg>
									</span>
								</div>
								<div class="h1 m-0">43</div>
								<div class="text-muted mb-4">New Tickets</div>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="card">
							<div class="card-body p-2 text-center">
								<div class="text-right text-red">
									<span class="text-red d-inline-flex align-items-center lh-1">
										-2% <svg xmlns="http://www.w3.org/2000/svg" class="icon ml-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><polyline points="3 7 9 13 13 9 21 17" /><polyline points="21 10 21 17 14 17" /></svg>
									</span>
								</div>
								<div class="h1 m-0">95</div>
								<div class="text-muted mb-4">Daily Earnings</div>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="card">
							<div class="card-body p-2 text-center">
								<div class="text-right text-green">
									<span class="text-green d-inline-flex align-items-center lh-1">
										9% <svg xmlns="http://www.w3.org/2000/svg" class="icon ml-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><polyline points="3 17 9 11 13 15 21 7" /><polyline points="14 7 21 7 21 14" /></svg>
									</span>
								</div>
								<div class="h1 m-0">7</div>
								<div class="text-muted mb-4">New Replies</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="card">
					<div id="chart-development-activity" class="mt-4"></div>
					<div class="table-responsive">
						<table class="table card-table table-vcenter">
							<thead>
								<tr>
									<th>User</th>
									<th>Commit</th>
									<th>Date</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="w-1">
										<span class="avatar" style="background-image: url(./static/avatars/000m.jpg)"></span>
									</td>
									<td class="td-truncate">
										<div class="text-truncate">
											Fix dart Sass compatibility (#29755)
										</div>
									</td>
									<td class="text-nowrap text-muted">28 Nov 2019</td>
								</tr>
								<tr>
									<td class="w-1">
										<span class="avatar">JL</span>
									</td>
									<td class="td-truncate">
										<div class="text-truncate">
											Change deprecated html tags to text decoration classes (#29604)
										</div>
									</td>
									<td class="text-nowrap text-muted">27 Nov 2019</td>
								</tr>
								<tr>
									<td class="w-1">
										<span class="avatar" style="background-image: url(./static/avatars/002m.jpg)"></span>
									</td>
									<td class="td-truncate">
										<div class="text-truncate">
											justify-content:between ⇒ justify-content:space-between (#29734)
										</div>
									</td>
									<td class="text-nowrap text-muted">26 Nov 2019</td>
								</tr>
								<tr>
									<td class="w-1">
										<span class="avatar" style="background-image: url(./static/avatars/003m.jpg)"></span>
									</td>
									<td class="td-truncate">
										<div class="text-truncate">
											Update change-version.js (#29736)
										</div>
									</td>
									<td class="text-nowrap text-muted">26 Nov 2019</td>
								</tr>
								<tr>
									<td class="w-1">
										<span class="avatar" style="background-image: url(./static/avatars/000f.jpg)"></span>
									</td>
									<td class="td-truncate">
										<div class="text-truncate">
											Regenerate package-lock.json (#29730)
										</div>
									</td>
									<td class="text-nowrap text-muted">25 Nov 2019</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-xl-3">
				<div class="card card-sm">
					<div class="card-body d-flex align-items-center">
						<span class="bg-blue text-white stamp mr-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" /><path d="M12 3v3m0 12v3" /></svg>
						</span>
						<div class="mr-3 lh-sm">
							<div class="strong">
								132 Sales
							</div>
							<div class="text-muted">12 waiting payments</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-xl-3">
				<div class="card card-sm">
					<div class="card-body d-flex align-items-center">
						<span class="bg-green text-white stamp mr-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><circle cx="9" cy="19" r="2" /><circle cx="17" cy="19" r="2" /><path d="M3 3h2l2 12a3 3 0 0 0 3 2h7a3 3 0 0 0 3 -2l1 -7h-15.2" /></svg>
						</span>
						<div class="mr-3 lh-sm">
							<div class="strong">
								78 Orders
							</div>
							<div class="text-muted">32 shipped</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-xl-3">
				<div class="card card-sm">
					<div class="card-body d-flex align-items-center">
						<div class="mr-3">
							<div class="chart-sparkline chart-sparkline-square" id="sparkline-7"></div>
						</div>
						<div class="mr-3 lh-sm">
							<div class="strong">
								1,352 Members
							</div>
							<div class="text-muted">163 registered today</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-xl-3">
				<div class="card card-sm">
					<div class="card-body d-flex align-items-center">
						<div class="mr-3 lh-sm">
							<div class="strong">
								132 Comments
							</div>
							<div class="text-muted">16 waiting</div>
						</div>
						<div class="ml-auto">
							<div class="chart-sparkline chart-sparkline-square" id="sparkline-8"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-lg-8">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title">Most Visited Pages</h4>
					</div>
					<div class="table-responsive">
						<table class="table card-table table-vcenter">
							<thead>
								<tr>
									<th>Page name</th>
									<th>Visitors</th>
									<th>Unique</th>
									<th colspan="2">Bounce rate</th>
								</tr>
							</thead>
							<tr>
								<td>
									/about.html
									<a href="#" class="link-secondary ml-2"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M10 14a3.5 3.5 0 0 0 5 0l4 -4a3.5 3.5 0 0 0 -5 -5l-.5 .5" /><path d="M14 10a3.5 3.5 0 0 0 -5 0l-4 4a3.5 3.5 0 0 0 5 5l.5 -.5" /></svg>
									</a>
								</td>
								<td class="text-muted">4,896</td>
								<td class="text-muted">3,654</td>
								<td class="text-muted">82.54%</td>
								<td class="text-right">
									<div class="chart-sparkline" id="sparkline-9"></div>
								</td>
							</tr>
							<tr>
								<td>
									/special-promo.html
									<a href="#" class="link-secondary ml-2"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M10 14a3.5 3.5 0 0 0 5 0l4 -4a3.5 3.5 0 0 0 -5 -5l-.5 .5" /><path d="M14 10a3.5 3.5 0 0 0 -5 0l-4 4a3.5 3.5 0 0 0 5 5l.5 -.5" /></svg>
									</a>
								</td>
								<td class="text-muted">3,652</td>
								<td class="text-muted">3,215</td>
								<td class="text-muted">76.29%</td>
								<td class="text-right">
									<div class="chart-sparkline" id="sparkline-10"></div>
								</td>
							</tr>
							<tr>
								<td>
									/news/1,new-ui-kit.html
									<a href="#" class="link-secondary ml-2"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M10 14a3.5 3.5 0 0 0 5 0l4 -4a3.5 3.5 0 0 0 -5 -5l-.5 .5" /><path d="M14 10a3.5 3.5 0 0 0 -5 0l-4 4a3.5 3.5 0 0 0 5 5l.5 -.5" /></svg>
									</a>
								</td>
								<td class="text-muted">3,256</td>
								<td class="text-muted">2,865</td>
								<td class="text-muted">72.65%</td>
								<td class="text-right">
									<div class="chart-sparkline" id="sparkline-11"></div>
								</td>
							</tr>
							<tr>
								<td>
									/lorem-ipsum-dolor-sit-amet-very-long-url.html
									<a href="#" class="link-secondary ml-2"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M10 14a3.5 3.5 0 0 0 5 0l4 -4a3.5 3.5 0 0 0 -5 -5l-.5 .5" /><path d="M14 10a3.5 3.5 0 0 0 -5 0l-4 4a3.5 3.5 0 0 0 5 5l.5 -.5" /></svg>
									</a>
								</td>
								<td class="text-muted">986</td>
								<td class="text-muted">865</td>
								<td class="text-muted">44.89%</td>
								<td class="text-right">
									<div class="chart-sparkline" id="sparkline-12"></div>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-lg-4">
				<a href="https://github.com/sponsors/codecalm" class="card card-sponsor" target="_blank" style="background-image: url(./static/sponsor-banner-homepage.svg)">
					<div class="card-body"></div>
				</a>
			</div>
			<div class="col-md-6 col-lg-4">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title">Social Media Traffic</h4>
					</div>
					<table class="table card-table table-vcenter">
						<thead>
							<tr>
								<th>Network</th>
								<th colspan="2">Visitors</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Instagram</td>
								<td>3,550</td>
								<td class="w-50">
									<div class="progress progress-xs">
										<div class="progress-bar bg-primary" style="width: 71.0%"></div>
									</div>
								</td>
							</tr>
							<tr>
								<td>Twitter</td>
								<td>1,798</td>
								<td class="w-50">
									<div class="progress progress-xs">
										<div class="progress-bar bg-primary" style="width: 35.96%"></div>
									</div>
								</td>
							</tr>
							<tr>
								<td>Facebook</td>
								<td>1,245</td>
								<td class="w-50">
									<div class="progress progress-xs">
										<div class="progress-bar bg-primary" style="width: 24.9%"></div>
									</div>
								</td>
							</tr>
							<tr>
								<td>Pinterest</td>
								<td>854</td>
								<td class="w-50">
									<div class="progress progress-xs">
										<div class="progress-bar bg-primary" style="width: 17.08%"></div>
									</div>
								</td>
							</tr>
							<tr>
								<td>VK</td>
								<td>650</td>
								<td class="w-50">
									<div class="progress progress-xs">
										<div class="progress-bar bg-primary" style="width: 13.0%"></div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-md-6 col-lg-8">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title">Tasks</h4>
					</div>
					<div class="table-responsive">
						<table class="table card-table table-vcenter">
							<tr>
								<td class="w-1 pr-0">
									<label class="form-check m-0">
										<input type="checkbox" class="form-check-input" checked>
										<span class="form-check-label"></span>
									</label>
								</td>
								<td class="w-100">
									<a href="#" class="text-reset">Extend the data model.</a>
								</td>
								<td class="text-nowrap text-muted">
									<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
									January 01, 2019
								</td>
								<td class="text-nowrap">
									<a href="#" class="text-muted">
										<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M5 12l5 5l10 -10" /></svg>
										2/7
									</a>
								</td>
								<td class="text-nowrap">
									<a href="#" class="text-muted">
										<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M4 21v-13a3 3 0 0 1 3 -3h10a3 3 0 0 1 3 3v6a3 3 0 0 1 -3 3h-9l-4 4" /><line x1="8" y1="9" x2="16" y2="9" /><line x1="8" y1="13" x2="14" y2="13" /></svg>
										3</a>
									</td>
									<td>
										<span class="avatar" style="background-image: url(./static/avatars/000m.jpg)"></span>
									</td>
								</tr>
								<tr>
									<td class="w-1 pr-0">
										<label class="form-check m-0">
											<input type="checkbox" class="form-check-input">
											<span class="form-check-label"></span>
										</label>
									</td>
									<td class="w-100">
										<a href="#" class="text-reset">Verify the event flow.</a>
									</td>
									<td class="text-nowrap text-muted">
										<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
										January 01, 2019
									</td>
									<td class="text-nowrap">
										<a href="#" class="text-muted">
											<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M5 12l5 5l10 -10" /></svg>
											3/10
										</a>
									</td>
									<td class="text-nowrap">
										<a href="#" class="text-muted">
											<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M4 21v-13a3 3 0 0 1 3 -3h10a3 3 0 0 1 3 3v6a3 3 0 0 1 -3 3h-9l-4 4" /><line x1="8" y1="9" x2="16" y2="9" /><line x1="8" y1="13" x2="14" y2="13" /></svg>
											6</a>
										</td>
										<td>
											<span class="avatar">JL</span>
										</td>
									</tr>
									<tr>
										<td class="w-1 pr-0">
											<label class="form-check m-0">
												<input type="checkbox" class="form-check-input">
												<span class="form-check-label"></span>
											</label>
										</td>
										<td class="w-100">
											<a href="#" class="text-reset">Database backup and maintenance</a>
										</td>
										<td class="text-nowrap text-muted">
											<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
											January 01, 2019
										</td>
										<td class="text-nowrap">
											<a href="#" class="text-muted">
												<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M5 12l5 5l10 -10" /></svg>
												0/6
											</a>
										</td>
										<td class="text-nowrap">
											<a href="#" class="text-muted">
												<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M4 21v-13a3 3 0 0 1 3 -3h10a3 3 0 0 1 3 3v6a3 3 0 0 1 -3 3h-9l-4 4" /><line x1="8" y1="9" x2="16" y2="9" /><line x1="8" y1="13" x2="14" y2="13" /></svg>
												1</a>
											</td>
											<td>
												<span class="avatar" style="background-image: url(./static/avatars/002m.jpg)"></span>
											</td>
										</tr>
										<tr>
											<td class="w-1 pr-0">
												<label class="form-check m-0">
													<input type="checkbox" class="form-check-input" checked>
													<span class="form-check-label"></span>
												</label>
											</td>
											<td class="w-100">
												<a href="#" class="text-reset">Identify the implementation team.</a>
											</td>
											<td class="text-nowrap text-muted">
												<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
												January 01, 2019
											</td>
											<td class="text-nowrap">
												<a href="#" class="text-muted">
													<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M5 12l5 5l10 -10" /></svg>
													6/10
												</a>
											</td>
											<td class="text-nowrap">
												<a href="#" class="text-muted">
													<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M4 21v-13a3 3 0 0 1 3 -3h10a3 3 0 0 1 3 3v6a3 3 0 0 1 -3 3h-9l-4 4" /><line x1="8" y1="9" x2="16" y2="9" /><line x1="8" y1="13" x2="14" y2="13" /></svg>
													12</a>
												</td>
												<td>
													<span class="avatar" style="background-image: url(./static/avatars/003m.jpg)"></span>
												</td>
											</tr>
											<tr>
												<td class="w-1 pr-0">
													<label class="form-check m-0">
														<input type="checkbox" class="form-check-input">
														<span class="form-check-label"></span>
													</label>
												</td>
												<td class="w-100">
													<a href="#" class="text-reset">Define users and workflow</a>
												</td>
												<td class="text-nowrap text-muted">
													<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
													January 01, 2019
												</td>
												<td class="text-nowrap">
													<a href="#" class="text-muted">
														<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M5 12l5 5l10 -10" /></svg>
														3/7
													</a>
												</td>
												<td class="text-nowrap">
													<a href="#" class="text-muted">
														<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M4 21v-13a3 3 0 0 1 3 -3h10a3 3 0 0 1 3 3v6a3 3 0 0 1 -3 3h-9l-4 4" /><line x1="8" y1="9" x2="16" y2="9" /><line x1="8" y1="13" x2="14" y2="13" /></svg>
														5</a>
													</td>
													<td>
														<span class="avatar" style="background-image: url(./static/avatars/000f.jpg)"></span>
													</td>
												</tr>
												<tr>
													<td class="w-1 pr-0">
														<label class="form-check m-0">
															<input type="checkbox" class="form-check-input" checked>
															<span class="form-check-label"></span>
														</label>
													</td>
													<td class="w-100">
														<a href="#" class="text-reset">Check Pull Requests</a>
													</td>
													<td class="text-nowrap text-muted">
														<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
														January 01, 2019
													</td>
													<td class="text-nowrap">
														<a href="#" class="text-muted">
															<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M5 12l5 5l10 -10" /></svg>
															2/9
														</a>
													</td>
													<td class="text-nowrap">
														<a href="#" class="text-muted">
															<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><path d="M4 21v-13a3 3 0 0 1 3 -3h10a3 3 0 0 1 3 3v6a3 3 0 0 1 -3 3h-9l-4 4" /><line x1="8" y1="9" x2="16" y2="9" /><line x1="8" y1="13" x2="14" y2="13" /></svg>
															3</a>
														</td>
														<td>
															<span class="avatar" style="background-image: url(./static/avatars/001f.jpg)"></span>
														</td>
													</tr>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
					<div class="modal modal-blur fade" id="modal-report" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog modal-lg" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">New report</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" /></svg>
									</button>
								</div>
								<div class="modal-body">
									<div class="mb-3">
										<label class="form-label">Name</label>
										<input type="text" class="form-control" name="example-text-input" placeholder="Your report name">
									</div>
									<label class="form-label">Report type</label>
									<div class="form-selectgroup-boxes row mb-3">
										<div class="col-lg-6">
											<label class="form-selectgroup-item">
												<input type="radio" name="report-type" value="1" class="form-selectgroup-input" checked>
												<span class="form-selectgroup-label d-flex align-items-center p-3">
													<span class="mr-3">
														<span class="form-selectgroup-check"></span>
													</span>
													<span class="form-selectgroup-label-content">
														<span class="form-selectgroup-title strong mb-1">Simple</span>
														<span class="d-block text-muted">Provide only basic data needed for the report</span>
													</span>
												</span>
											</label>
										</div>
										<div class="col-lg-6">
											<label class="form-selectgroup-item">
												<input type="radio" name="report-type" value="1" class="form-selectgroup-input">
												<span class="form-selectgroup-label d-flex align-items-center p-3">
													<span class="mr-3">
														<span class="form-selectgroup-check"></span>
													</span>
													<span class="form-selectgroup-label-content">
														<span class="form-selectgroup-title strong mb-1">Advanced</span>
														<span class="d-block text-muted">Insert charts and additional advanced analyses to be inserted in the report</span>
													</span>
												</span>
											</label>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-8">
											<div class="mb-3">
												<label class="form-label">Report url</label>
												<div class="input-group input-group-flat">
													<span class="input-group-text">
														https://tabler.io/reports/
													</span>
													<input type="text" class="form-control pl-0"  value="report-01">
												</div>
											</div>
										</div>
										<div class="col-lg-4">
											<div class="mb-3">
												<label class="form-label">Visibility</label>
												<select class="form-select">
													<option value="1" selected>Private</option>
													<option value="2">Public</option>
													<option value="3">Hidden</option>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="modal-body">
									<div class="row">
										<div class="col-lg-6">
											<div class="mb-3">
												<label class="form-label">Client name</label>
												<input type="text" class="form-control">
											</div>
										</div>
										<div class="col-lg-6">
											<div class="mb-3">
												<label class="form-label">Reporting period</label>
												<input type="date" class="form-control">
											</div>
										</div>
										<div class="col-lg-12">
											<div>
												<label class="form-label">Additional information</label>
												<textarea class="form-control" rows="3"></textarea>
											</div>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<a href="#" class="btn btn-link link-secondary" data-dismiss="modal">
										Cancel
									</a>
									<a href="#" class="btn btn-primary ml-auto" data-dismiss="modal">
										<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
										Create new report
									</a>
								</div>
							</div>
						</div>







						<script>
						// @formatter:off
						document.addEventListener("DOMContentLoaded", function () {
							window.ApexCharts && (new ApexCharts(document.getElementById('chart-mentions'), {
								chart: {
									type: "bar",
									fontFamily: 'inherit',
									height: 240,
									parentHeightOffset: 0,
									toolbar: {
										show: false,
									},
									animations: {
										enabled: false
									},
									stacked: true,
								},
								plotOptions: {
									bar: {
										columnWidth: '50%',
									}
								},
								dataLabels: {
									enabled: false,
								},
								fill: {
									opacity: 1,
								},
								series: [{
									name: "Web",
									data: [1, 0, 0, 0, 0, 1, 1, 0, 0, 0, 2, 12, 5, 8, 22, 6, 8, 6, 4, 1, 8, 24, 29, 51, 40, 47, 23, 26, 50, 26, 41, 22, 46, 47, 81, 46, 6]
								},{
									name: "Social",
									data: [2, 5, 4, 3, 3, 1, 4, 7, 5, 1, 2, 5, 3, 2, 6, 7, 7, 1, 5, 5, 2, 12, 4, 6, 18, 3, 5, 2, 13, 15, 20, 47, 18, 15, 11, 10, 0]
								},{
									name: "Other",
									data: [2, 9, 1, 7, 8, 3, 6, 5, 5, 4, 6, 4, 1, 9, 3, 6, 7, 5, 2, 8, 4, 9, 1, 2, 6, 7, 5, 1, 8, 3, 2, 3, 4, 9, 7, 1, 6]
								}],
								grid: {
									padding: {
										top: -20,
										right: 0,
										left: -4,
										bottom: -4
									},
									strokeDashArray: 4,
									xaxis: {
										lines: {
											show: true
										}
									},
								},
								xaxis: {
									labels: {
										padding: 0
									},
									tooltip: {
										enabled: false
									},
									axisBorder: {
										show: false,
									},
									type: 'datetime',
								},
								yaxis: {
									labels: {
										padding: 4
									},
								},
								labels: [
									'2020-06-21', '2020-06-22', '2020-06-23', '2020-06-24', '2020-06-25', '2020-06-26', '2020-06-27', '2020-06-28', '2020-06-29', '2020-06-30', '2020-07-01', '2020-07-02', '2020-07-03', '2020-07-04', '2020-07-05', '2020-07-06', '2020-07-07', '2020-07-08', '2020-07-09', '2020-07-10', '2020-07-11', '2020-07-12', '2020-07-13', '2020-07-14', '2020-07-15', '2020-07-16', '2020-07-17', '2020-07-18', '2020-07-19', '2020-07-20', '2020-07-21', '2020-07-22', '2020-07-23', '2020-07-24', '2020-07-25', '2020-07-26', '2020-07-27'
								],
								colors: ["#206bc4", "#79a6dc", "#bfe399"],
								legend: {
									show: true,
									position: 'bottom',
									height: 32,
									offsetY: 8,
									markers: {
										width: 8,
										height: 8,
										radius: 100,
									},
									itemMargin: {
										horizontal: 8,
									},
								},
							})).render();
						});
						// @formatter:on
						</script>

						<script>
						// @formatter:off
						document.addEventListener("DOMContentLoaded", function () {
							window.ApexCharts && (new ApexCharts(document.getElementById('chart-development-activity'), {
								chart: {
									type: "area",
									fontFamily: 'inherit',
									height: 160,
									sparkline: {
										enabled: true
									},
									animations: {
										enabled: false
									},
								},
								dataLabels: {
									enabled: false,
								},
								fill: {
									opacity: .16,
									type: 'solid'
								},
								title: {
									text: "Development Activity",
									margin: 0,
									floating: true,
									offsetX: 10,
									style: {
										fontSize: '18px',
									},
								},
								stroke: {
									width: 2,
									lineCap: "round",
									curve: "smooth",
								},
								series: [{
									name: "Purchases",
									data: [3, 5, 4, 6, 7, 5, 6, 8, 24, 7, 12, 5, 6, 3, 8, 4, 14, 30, 17, 19, 15, 14, 25, 32, 40, 55, 60, 48, 52, 70]
								}],
								grid: {
									strokeDashArray: 4,
								},
								xaxis: {
									labels: {
										padding: 0
									},
									tooltip: {
										enabled: false
									},
									axisBorder: {
										show: false,
									},
									type: 'datetime',
								},
								yaxis: {
									labels: {
										padding: 4
									},
								},
								labels: [
									'2020-06-21', '2020-06-22', '2020-06-23', '2020-06-24', '2020-06-25', '2020-06-26', '2020-06-27', '2020-06-28', '2020-06-29', '2020-06-30', '2020-07-01', '2020-07-02', '2020-07-03', '2020-07-04', '2020-07-05', '2020-07-06', '2020-07-07', '2020-07-08', '2020-07-09', '2020-07-10', '2020-07-11', '2020-07-12', '2020-07-13', '2020-07-14', '2020-07-15', '2020-07-16', '2020-07-17', '2020-07-18', '2020-07-19', '2020-07-20'
								],
								colors: ["#206bc4"],
								legend: {
									show: false,
								},
								point: {
									show: false
								},
							})).render();
						});
						// @formatter:on
						</script>
						<script>
						document.addEventListener("DOMContentLoaded", function () {
							$().peity && $('#sparkline-7').text("56/100").peity("pie", {
								width: 40,
								height: 40,
								stroke: "#cd201f",
								strokeWidth: 2,
								fill: ["#cd201f", "rgba(110, 117, 130, 0.2)"],
								padding: .2,
								innerRadius: 17,
							});
						});
						</script>
						<script>
						document.addEventListener("DOMContentLoaded", function () {
							$().peity && $('#sparkline-8').text("22/100").peity("pie", {
								width: 40,
								height: 40,
								stroke: "#fab005",
								strokeWidth: 2,
								fill: ["#fab005", "rgba(110, 117, 130, 0.2)"],
								padding: .2,
								innerRadius: 17,
							});
						});
						</script>
						<script>
						document.addEventListener("DOMContentLoaded", function () {
							$().peity && $('#sparkline-9').text("17, 24, 20, 10, 5, 1, 4, 18, 13").peity("line", {
								width: 64,
								height: 40,
								stroke: "#206bc4",
								strokeWidth: 2,
								fill: ["#d2e1f3"],
								padding: .2,
							});
						});
						</script>
						<script>
						document.addEventListener("DOMContentLoaded", function () {
							$().peity && $('#sparkline-10').text("13, 11, 19, 22, 12, 7, 14, 3, 21").peity("line", {
								width: 64,
								height: 40,
								stroke: "#206bc4",
								strokeWidth: 2,
								fill: ["#d2e1f3"],
								padding: .2,
							});
						});
						</script>
						<script>
						document.addEventListener("DOMContentLoaded", function () {
							$().peity && $('#sparkline-11').text("10, 13, 10, 4, 17, 3, 23, 22, 19").peity("line", {
								width: 64,
								height: 40,
								stroke: "#206bc4",
								strokeWidth: 2,
								fill: ["#d2e1f3"],
								padding: .2,
							});
						});
						</script>
						<script>
						document.addEventListener("DOMContentLoaded", function () {
							$().peity && $('#sparkline-12').text("9, 6, 14, 11, 8, 24, 2, 16, 15").peity("line", {
								width: 64,
								height: 40,
								stroke: "#206bc4",
								strokeWidth: 2,
								fill: ["#d2e1f3"],
								padding: .2,
							});
						});
						</script>
						<script>
						document.body.style.display = "block"
						</script>

-->
						<?php

						function cardWithGraph($data = null) {
							$output  = "<div class=\"col-sm-6 col-lg-3\">";
							$output .= "<div class=\"card\">";
							$output .= "<div class=\"card-body\">";
							$output .= "<div class=\"d-flex align-items-center\">";
							$output .= "<div class=\"subheader\">" . $data['title'] . "</div>";
							$output .= "<div class=\"ml-auto lh-1\">";
							$output .= "<div class=\"dropdown\">";
							$output .= "<a class=\"dropdown-toggle text-muted\" href=\"#\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">Last 7 days</a>";
							$output .= "<div class=\"dropdown-menu dropdown-menu-right\">";
							$output .= "<a class=\"dropdown-item active\" href=\"#\">Last 7 days</a>";
							$output .= "<a class=\"dropdown-item\" href=\"#\">Last 30 days</a>";
							$output .= "<a class=\"dropdown-item\" href=\"#\">Last 3 months</a>";
							$output .= "</div>";
							$output .= "</div>";
							$output .= "</div>";
							$output .= "</div>";
							$output .= "<div class=\"d-flex align-items-baseline\">";
							$output .= "<div class=\"h1 mb-0 mr-2\">" . $data['count_total'] . "</div>";
							$output .= "<div class=\"mr-auto\">";
							$output .= "<span class=\"text-green d-inline-flex align-items-center lh-1\">";
							$output .= "8% <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon ml-1\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\"/><polyline points=\"3 17 9 11 13 15 21 7\" /><polyline points=\"14 7 21 7 21 14\" /></svg>";
							$output .= "</span>";
							$output .= "</div>";
							$output .= "</div>";
							$output .= "</div>";
							$output .= "<div id=\"chart-" . $data['id'] . "-bg\" class=\"chart-sm\"></div>";
							$output .= "</div>";
							$output .= "</div>";


							$script  = "<script>";
							//$script .= "// @formatter:off";
							$script .= "document.addEventListener(\"DOMContentLoaded\", function () {";
								$script .= "window.ApexCharts && (new ApexCharts(document.getElementById('chart-" . $data['id'] . "-bg'), {";
									$script .= "chart: {";
										$script .= "type: \"" . $data['area_type']. "\",";
										$script .= "fontFamily: 'inherit',";
										$script .= "height: 40.0,";
										$script .= "sparkline: {";
											$script .= "enabled: true";
											$script .= "},";
											$script .= "animations: {";
												$script .= "enabled: false";
												$script .= "},";
												$script .= "},";
												$script .= "dataLabels: {";
													$script .= "enabled: false,";
													$script .= "},";
													$script .= "fill: {";
														$script .= "opacity: .16,";
														$script .= "type: 'solid'";
														$script .= "},";
														$script .= "stroke: {";
															$script .= "width: 2,";
															$script .= "lineCap: \"round\",";
															$script .= "curve: \"smooth\",";
															$script .= "},";
															$script .= "series: [{";
																$script .= "name: \"Count\",";
																$script .= "data: [" . $data['values'] . "]";
																$script .= "}],";
																$script .= "grid: {";
																	$script .= "strokeDashArray: 4,";
																	$script .= "},";
																	$script .= "xaxis: {";
																		$script .= "labels: {";
																			$script .= "padding: 0";
																			$script .= "},";
																			$script .= "tooltip: {";
																				$script .= "enabled: false";
																				$script .= "},";
																				$script .= "axisBorder: {";
																					$script .= "show: false,";
																					$script .= "},";
																					$script .= "type: 'datetime',";
																					$script .= "},";
																					$script .= "yaxis: {";
																						$script .= "labels: {";
																							$script .= "padding: 4";
																							$script .= "},";
																							$script .= "},";
																							$script .= "labels: [";
																							$script .= $data['titles'];
																							$script .= "],";
																							$script .= "colors: [\"#206bc4\"],";
																							$script .= "legend: {";
																								$script .= "show: false,";
																								$script .= "},";
																								$script .= "})).render();";
																								$script .= "});";
																								$script .= "// @formatter:on";
																								$script .= "</script>";

																								$_SESSION['scripts_output'][] = $script;

																								return $output;
																							}

																							foreach ($_SESSION['scripts_output'] AS $script) {
																								echo $script;
																							}
																							?>
