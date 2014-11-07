<div class="page-header">
	<h1>Student Address Batch Import</h1>
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
    	$student = Students::find_by_official_ids($studentImport[$headerRow['seh:adm:email2']]);
    	
    	if (isset($student->studentid)) {
			// build the address!
				$address = new Addresses;
				
				$country = Countries::find_by_name($studentImport[$headerRow['seh:adm:cykey']]);
				
				//$address->addrid		= $_POST['addrid'];
				$address->studentkey	= $student->studentid;
				$address->line1			= $studentImport[$headerRow['seh:adm:add1']];
				$address->line2			= $studentImport[$headerRow['seh:adm:add2']];
				$address->line3			= $studentImport[$headerRow['seh:adm:add3']];
				$address->line4			= $studentImport[$headerRow['seh:adm:add4']];
				$address->town			= $studentImport[$headerRow['seh:adm:town']];
				$address->county		= $studentImport[$headerRow['seh:adm:county']];
				$address->postcode		= $studentImport[$headerRow['seh:adm:postcode']];
				$address->cykey			= $country->cyid;
				$address->phone			= null;
				$address->email			= null;
				$address->mobile		= null;
				$address->fax			= null;
				$address->defalt		= "Yes";
				$address->atkey			= "8";
				
				echo "creating address";
				
				$address->create();
    	}
		$student->update();
	} // end foreach
?>