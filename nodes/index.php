<?php

$logsClass = new Logs();

$totalPersons = $logsClass->totalPersons();

echo "Total Persons:";
printArray($totalPersons);
?>

<?php
$sql = "SELECT * FROM _stats WHERE name = 'person_rows_total' ORDER BY date_created DESC LIMIT 7";
$statsPersonsTotals = $db->query($sql)->fetchAll();
foreach ($statsPersonsTotals AS $personTotal) {
	$personTotalArray["'" . date('Y-m-d', strtotime($personTotal['date_created'])) . "'"] = $personTotal['value'];
}
$personTotalArray = array_reverse($personTotalArray);


$sql = "SELECT * FROM _stats WHERE name = 'student_rows_total' ORDER BY date_created DESC LIMIT 7";
$statsStudentTotals = $db->query($sql)->fetchAll();
foreach ($statsStudentTotals AS $studentTotal) {
	$studentTotalArray["'" . date('Y-m-d', strtotime($studentTotal['date_created'])) . "'"] = $studentTotal['value'];
}
$studentTotalArray = array_reverse($studentTotalArray);


$sql = "SELECT * FROM _logs WHERE type = 'LOGON' ORDER BY date_created DESC LIMIT 7";
$logonsAll = $db->query($sql)->fetchAll();
$logonsAllCount = count($logonsAll);

$sql = "SELECT DATE(date_created) AS date_created, COUNT(*) AS cnt FROM _logs WHERE type = 'LOGON' GROUP BY DATE(date_created) ORDER BY date_created DESC LIMIT 7";
$logonsByDay = $db->query($sql)->fetchAll();

$logonsCountArray = array();
foreach ($logonsByDay AS $day) {
	$logonsCountArray["'" . date('Y-m-d', strtotime($day['date_created'])) . "'"] = $day['cnt'];
}

$sql = "SELECT * FROM _logs WHERE type = 'VIEW' ORDER BY date_created DESC";
$logViewsAll = $db->query($sql)->fetchAll();
$logViewsAllCount = count($logViewsAll);

$sql = "SELECT DATE(date_created) AS date_created, COUNT(*) AS cnt FROM _logs GROUP BY DATE(date_created) ORDER BY date_created DESC";
$logsByDay = $db->query($sql)->fetchAll();
foreach ($logsByDay AS $day) {
	$logsCountArray["'" . date('Y-m-d', strtotime($day['date_created'])) . "'"] = $day['cnt'];
}
$logsCountArray = array_reverse(array_slice($logsCountArray, 0, 7));

$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"images/icons.svg#bell\"/></svg> Test Button", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteMealModal\"");
$icons[] = array("class" => "btn-warning", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"images/icons.svg#email\"/></svg> Test Button", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteMealModal\"");

echo displayTitle("Dashboard", "Overview", $icons);
?>


	<div class="row row-deck row-cards">
		<?php
		$personsCountCard = array(
			"id" => "persons_count",
			"area_type" => "area",
			"title" => "Persons Count",
			"count_total" => end($personTotalArray),
			"titles" => array_keys($personTotalArray),
			"values" => $personTotalArray
		);
		$otherCard1 = array(
			"id" => "students_count",
			"area_type" => "area",
			"title" => "Students Count",
			"count_total" => end($studentTotalArray),
			"titles" => array_keys($studentTotalArray),
			"values" => $studentTotalArray
		);
		$otherCard2 = array(
			"id" => "logons_count",
			"area_type" => "line",
			"title" => "Logons Count",
			"count_total" => array_sum($logonsCountArray),
			"titles" => array_keys($logonsCountArray),
			"values" => $logonsCountArray
		);
		$otherCard3 = array(
			"id" => "logs_count",
			"area_type" => "bar",
			"title" => "Logs Count",
			"count_total" => array_sum($logsCountArray),
			"titles" => array_keys($logsCountArray),
			"values" => $logsCountArray
		);
		echo cardWithGraph($personsCountCard);
		echo cardWithGraph($otherCard1);
		echo cardWithGraph($otherCard2);
		echo cardWithGraph($otherCard3);
		?>
	</div>

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

	$currentReading = end($data['values']);
	$previousReading = prev($data['values']);
	//printArray($data['values']);
	//echo "Prev: " . $previousReading . " Current: " . $currentReading;

	$differencePercentage = 34;
	if ($differencePercentage > 0) {
		$icon = "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon ml-1\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\"/><polyline points=\"3 17 9 11 13 15 21 7\" /><polyline points=\"14 7 21 7 21 14\" /></svg>";
		$class = "text-green";
	} elseif ($differencePercentage < 0) {
		$icon = "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon ml-1\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\"></path><polyline points=\"3 7 9 13 13 9 21 17\"></polyline><polyline points=\"21 10 21 17 14 17\"></polyline></svg>";
		$class = "text-red";
	} else {
		$icon = "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon ml-1\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\"></path><line x1=\"5\" y1=\"12\" x2=\"19\" y2=\"12\"></line></svg>";
		$class = "text-orange";
	}

	$output .= "<span class=\"" . $class . " d-inline-flex align-items-center lh-1\">";
	$output .= $differencePercentage . "% " . $icon;
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
										$script .= "data: ['1','2','3']";
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
																	$script .= "labels: ['1','2','3'],";
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
