<div class="page-header">
	<h1>Student Import</h1>
</div>

<form role="form" action="" method="post" enctype="multipart/form-data">
<div class="form-group">
	<label for="inputFile">CUD CSV Student Data</label>
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
if ($_POST['testImport'] != "true") {
	echo "<div class=\"alert alert-info\" role=\"alert\">" . "Importing " . count($CSVContents) . " students</div>";
    
    foreach ($CSVContents AS $studentImport) {
    	$title = Titles::find_by_title_name($studentImport[$headerRow['cud:cas:title']]);
    	$nationality = Countries::find_by_name($studentImport[$headerRow['seh:adm:nationality']]);
    	
    	$student = new Students();
    	$student->st_type = $studentImport[$headerRow['cud:cas:university_card_type']];
    	$student->titlekey = $title->titleid;
    	$student->initials = NULL;
    	$student->forenames = $studentImport[$headerRow['cud:cas:firstname']];
    	$student->prefname = NULL;
    	$student->surname = $studentImport[$headerRow['cud:cas:lastname']];
    	$student->prev_surname = NULL;
    	$student->suffix = NULL;
    	$student->marital_status = NULL;
    	$student->dt_birth = NULL;
    	$student->gender = NULL;
    	$student->nationality = NULL;
    	$student->birth_cykey = NULL;
    	$student->resid_cykey = NULL;
    	$student->citiz_cykey = NULL;
    	$student->optout = NULL;
    	$student->family = NULL;
    	$student->eng_lang = NULL;
    	$student->occup_bg = NULL;
    	$student->disability = NULL;
    	$student->ethkey = NULL;
    	$student->rskey = NULL;
    	$student->cskey = NULL;
    	$student->relkey = NULL;
    	$student->rckey = NULL;
    	$student->SSNref = NULL;
    	$student->oss_pn = $studentImport[$headerRow['cud:fk:oss_student_number']];
    	$student->fee_status = $studentImport[$headerRow['seh:adm:fee_status']];
    	$student->univ_cardno = $studentImport[$headerRow['cud:cas:barcode7']];
    	$student->dt_card_exp = $studentImport[$headerRow['cud:uas:universitycard_comp_date']];
    	$student->course_yr = NULL;
    	$student->notes = NULL;
    	$student->email1 = $studentImport[$headerRow['cud:cas:oxford_email']];
    	$student->email2 = NULL;
    	$student->mobile = NULL;
    	$student->dt_start = NULL;
    	$student->dt_end = NULL;
    	$student->dt_matric = NULL;
    	$student->oucs_id = $studentImport[$headerRow['cud:cas:sso_username']];
    	$student->yr_app = NULL;
    	$student->yr_entry = NULL;
    	$student->yr_cohort = NULL;
    	$student->dt_created = date(Ymd);
    	$student->dt_lastmod = date(Ymd);
    	$student->who_mod = $_SESSION['username'];
		$student->photo = NULL;
		
		$student->create();
	} // end foreach
} else {
	echo "<div class=\"alert alert-info\" role=\"alert\">" . "This is just a test.  However, had it not been a test, ". count($CSVContents) . " students would have been imported.</div>";
} // end if ($_POST['testImport'] != true
?>