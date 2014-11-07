<div class="page-header">
	<h1>Student Batch Update</h1>
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
		// Get headers
		if (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
			// build an array of the headers
			foreach ($data AS $cell) {
				$headerRow[] = $cell;
			}
			$headerRow = array_flip($headerRow);
		}
		
		// Get the rest
		while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
			$CSVContents[] = $data;
			
			//echo '<tr><td>'.implode('</td><td>', $data).'</td></tr>';
			$dataRowCount ++;
		}
		$output .= "<div class=\"alert alert-info\" role=\"alert\">" . "CSV file contains " . $dataRowCount . " rows (not including the headers)</div>";
		
		fclose($handle);
	}
	
	// output the debug steps
	echo $output;
}

// itterate through the data and import each CSV row
	echo "<div class=\"alert alert-info\" role=\"alert\">" . "Importing " . count($CSVContents) . " students</div>";
    
    foreach ($CSVContents AS $studentImport) {
    	// try to find a student already in the database that matches this CSV row
    	$student = Students::find_by_official_ids($studentImport[$headerRow['cud:fk:oss_student_number']]);
    	
    	if (isset($student->studentid)) {
    		echo "Found " . $student->fullDisplayName() . "<br />";
			//$student->st_type = $studentImport[$headerRow['cud:cas:university_card_type']];
			$student->initials = $studentImport[$headerRow['seh:adm:initials']];
			//$student->forenames = $studentImport[$headerRow['cud:cas:firstname']];
			//$student->forenames = $studentImport[$headerRow['seh:adm:firstname']];
			$student->prefname = NULL;
			//$student->surname = $studentImport[$headerRow['cud:cas:lastname']];
			//$student->surname = $studentImport[$headerRow['seh:adm:lastname']];
			$student->prev_surname = NULL;
			$student->suffix = NULL;
			$student->marital_status = NULL;
			//$student->dt_birth = $studentImport[$headerRow['seh:adm:dob']];
			$student->gender = $studentImport[$headerRow['seh:adm:gender']];
			$student->nationality = $studentImport[$headerRow['seh:adm:nationality']];
			$student->birth_cykey = NULL;
			$student->resid_cykey = NULL;
			$student->citiz_cykey = NULL;
			$student->optout = NULL;
			$student->family = NULL;
			$student->eng_lang = NULL;
			$student->occup_bg = NULL;
			$student->disability = $studentImport[$headerRow['seh:adm:disabilities']];
			$student->ethkey = NULL;
			$student->rskey = NULL;
			$student->cskey = $studentImport[$headerRow['seh:adm:cskey']];
			$student->relkey = NULL;
			$student->rckey = NULL;
			$student->SSNref = NULL;
			//$student->oss_pn = $studentImport[$headerRow['cud:fk:oss_student_number']];
			//$student->oss_pn = $studentImport[$headerRow['seh:adm:oss']];
			$student->fee_status = $studentImport[$headerRow['seh:adm:fee_status']];
			$student->univ_cardno = $studentImport[$headerRow['cud:cas:barcode7']];
			$student->dt_card_exp = $studentImport[$headerRow['cud:uas:universitycard_comp_date']];
			$student->course_yr = NULL;
			$student->notes = $studentImport[$headerRow['seh:adm:notes']];
			$student->email1 = $studentImport[$headerRow['cud:cas:oxford_email']];
			$student->email2 = $studentImport[$headerRow['seh:adm:email2']];
			$student->mobile = $studentImport[$headerRow['seh:adm:mobile']];
			$student->dt_start = $studentImport[$headerRow['seh:adm:dt_start']];
			$student->dt_end = $studentImport[$headerRow['cud:uas:universitycard_comp_date']];
			$student->dt_matric = NULL;
			$student->oucs_id = $studentImport[$headerRow['cud:cas:sso_username']];
			$student->yr_app = $studentImport[$headerRow['seh:adm:yearapplication']];
			$student->yr_entry = $studentImport[$headerRow['seh:adm:yearstart']];
			$student->yr_cohort = $studentImport[$headerRow['seh:adm:yr_cohort']];
			//$student->dt_created = date(Ymd);
			$student->dt_lastmod = date(Ymd);
			$student->who_mod = $_SESSION['username'];
			$student->photo = NULL;
			
			$student->update();
    	} else {
	    	echo "not found OSS: " . $studentImport[$headerRow['cud:fk:oss_student_number']] . "<br />";
    	}
    	
    	
    	
		
		
	} // end foreach
?>