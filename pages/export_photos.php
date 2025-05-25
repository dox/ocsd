<?php
$mpdf = new \Mpdf\Mpdf();

if (isset($_GET['crs_start_dt'])) {
	// Get course start year safely
	$crs_start_dt = (int) $_GET['crs_start_dt'];
	$sql = "SELECT cudid FROM Person WHERE crs_start_dt DIV 10000 = $crs_start_dt";
} else {
	$crs_start_dt = "All";
	$sql = "SELECT cudid FROM Person";
}


$personsAll = $db->get($sql);

// CSS styling
$style = '<style>
.card-container {
	width: 100%;
	overflow: hidden;
	margin-bottom: 20px;
}
.card {
	width: 30%;
	float: left;
	margin-right: 3.333%;
	margin-bottom: 20px;
	text-align: center;
	border: 0px;
	padding: 10px;
	box-sizing: border-box;
	min-height: 260px;
}
.card:nth-child(3n) {
	margin-right: 0;
}
.image-box {
	width: 100%;
	height: 200px;
	background-size: cover;
	background-position: center;
	background-repeat: no-repeat;
	margin: 0 auto 10px auto;
	border: 1px solid #ddd;
}

</style>';


$mpdf->WriteHTML($style);
$mpdf->WriteHTML('<h1>Course Start Year: ' . $crs_start_dt . '</h1>');

// Start of content
$html = '<div class="card-container">';
$personCount = 0;

foreach ($personsAll as $index => $personData) {
	$person = new Person($personData['cudid']);
	
	$rout_name = str_replace("Visiting Non-Matriculated", "", $person->rout_name);
	$rout_name = str_replace("- ", "", $rout_name);
	
	
	$html .= '<div class="card">';
	$html .= '<div class="image-box" style="background-image: url(\'' . $person->photograph() . '\');"></div>';
	$html .= '<strong>' . $person->FullName . '</strong><br>';
	$html .= $rout_name . '<br>';
	$html .= '</div>';
	
	$personCount++;
	
	// After 9 people (3x3), close container, add a page, and start new container
	if ($personCount % 9 === 0) {
		
		$html .= '</div>';
		$mpdf->WriteHTML($html);
		$mpdf->AddPage();
		$html = '<div class="card-container">';
	}
}

// Final flush
$html .= '</div>';
$mpdf->WriteHTML($html);

$mpdf->Output();
?>