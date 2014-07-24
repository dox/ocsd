<div class="page-header">
	<h1>Student Import</h1>
</div>

<form role="form" action="" method="post" enctype="multipart/form-data">
<div class="form-group">
	<label for="inputFile">CSV Student Data</label>
	<input type="file" id="inputFile" name="inputFile">
	<p class="help-block">Warning! If you are unsure of what you're doing - please stop!</p>
</div>
<div class="checkbox">
	<?php
	if ($_POST['testImport'] == "true") {
		$checkedValue = "checked";
	} else {
		$checkedValue = "";
	}
	?>
	<label><input type="checkbox" id="testImport" name="testImport" value="true" <?php echo $checkedValue; ?>> Test Import</label>
</div>
<button type="submit" class="btn btn-primary" name="submit">Import</button>
</form>

<?php
// check if a file has been uploaded from the HTML form
if (isset($_FILES) && !empty($_FILES)) {
	$output  = "<div class=\"alert alert-info\" role=\"alert\">" . "Importing file '" . $_FILES['inputFile']['name'] . "'" . " (" . $_FILES['inputFile']['size'] . " bytes)</div>";
	
	// start itterating through the CSV
	$row = 1;
	$dataRowCount = 0;
	if (($handle = fopen($_FILES['inputFile']['tmp_name'], 'r')) !== FALSE) {
		echo "<table class=\"table table-striped\">";
		
		// Get headers
		if (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
			// build an array of the headers
			foreach ($data AS $cell) {
				$headerRow[] = $cell;
			}
			$headerRow = array_flip($headerRow);
			
			echo '<tr><th>'.implode('</th><th>', $data).'</th></tr>';
		}
		
		// Get the rest
		while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
			$CSVContents[] = $data;
			echo '<tr><td>'.implode('</td><td>', $data).'</td></tr>';
			$dataRowCount ++;
		}
		$output .= "<div class=\"alert alert-info\" role=\"alert\">" . "CSV file contains " . $dataRowCount . " rows (not including the headers)</div>";
		
		fclose($handle);
		echo '</table>';
	}
	
	// output the debug steps
	echo $output;
}


// itterate through the data and import each CSV row
if (isset($_POST['testImport'])) {
	if ($_POST['testImport'] != "true") {
		echo "<div class=\"alert alert-info\" role=\"alert\">" . "Importing " . count($CSVContents) . " students</div>";
		
		foreach ($CSVContents AS $studentImport) {
	$student = new Students();
	$student->st_type = $studentImport[$headerRow['cud:cas:university_card_type']];
	$student->titlekey = $studentImport[$headerRow['cud:cas:title']];
	$student->initials = "";
	$student->forenames = $studentImport[$headerRow['cud:cas:firstname']];
	$student->prefname = "";
	$student->surname = $studentImport[$headerRow['cud:cas:lastname']];
	$student->prev_surname = "";
	$student->suffix = "";
	$student->marital_status = "";
	$student->dt_birth = "";
	$student->gender = "";
	$student->nationality = "";
	$student->birth_cykey = "";
	$student->resid_cykey = "";
	$student->citiz_cykey = "";
	$student->optout = "";
	$student->family = "";
	$student->eng_lang = "";
	$student->occup_bg = "";
	$student->disability = "";
	$student->ethkey = "";
	$student->rskey = "";
	$student->cskey = "";
	$student->relkey = "";
	$student->rckey = "";
	$student->SSNref = "";
	$student->oss_pn = $studentImport[$headerRow['cud:fk:oss_student_number']];
	$student->fee_status = "";
	$student->univ_cardno = $studentImport[$headerRow['cud:cas:barcode7']];
	$student->dt_card_exp = $studentImport[$headerRow['cud:uas:universitycard_comp_date']];
	$student->course_yr = "";
	$student->notes = "";
	$student->email1 = $studentImport[$headerRow['cud:cas:oxford_email']];
	$student->email2 = "";
	$student->mobile = "";
	$student->dt_start = "";
	$student->dt_end = "";
	$student->dt_matric = "";
	$student->oucs_id = $studentImport[$headerRow['cud:cas:sso_username']];
	$student->yr_app = "";
	$student->yr_entry = "";
	$student->yr_cohort = "";
	$student->dt_created = date(Ymd);
	$student->dt_lastmod = date(Ymd);
	$student->who_mod = $_SESSION['username'];
	$student->photo = "";
	
	$student->create();	
} // end foreach
	} else { // end if ($_POST['testImport'] != true
		echo "<div class=\"alert alert-info\" role=\"alert\">" . "This is just a test.  However, had it not been a test, ". count($CSVContents) . " students would have been imported.</div>";
	} // end if (isset($_POST...
}
?>