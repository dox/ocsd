<div class="page-header">
	<h1>Batch Photo Import</h1>
</div>
<div>
	<?php
	$foldersArray = false;
	$filesArray = false;
	
	$directory = "/var/www/ocsd/photos/";
	
	$files = scandir($directory);
	
	foreach($files as $file) {
		//echo $directory . $file . "<br />";
		
		if (is_file($directory . $file)) {
			$filesArray[] = $file;
				//"<div class=\"alert alert-success\"><strong>Success!</strong> " . $entry . "</div>";
		} else {
			$foldersArray[] = $file;
		}
	}
	
	$allowedFileExt = array("jpg","jpeg", "png");
	
	echo "<p>Searching " . $directory . " for files</p>";
	echo "<p>Running import on " . count($filesArray) . " files</p>";
	echo "<p>Ignoring " . count($foldersArray) . " sub-folders</p>";
	echo "<p>Allowed extensions: " . implode($allowedFileExt, ", ") . "</p>";
	
	
	foreach ($filesArray AS $file) {
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		$fileName = str_replace($ext, "", $file);
		$fileName = str_replace(".", "", $fileName);
		
		if (in_array($ext, $allowedFileExt)) {
			$user = Students::find_by_uid($fileName, "univ_cardno");
			
			if (isset($user->studentid)) {
				//$user->inlineUpdate($user->studentid, "photo", $file);
				//echo $user->fullDisplayName();
				echo "<div class=\"alert alert-success\"><strong>Success!</strong> File '" . $file . "' imported</div>";
			} else {
				echo "<div class=\"alert\"><strong>Warning!</strong> File '" . $file . "' could not be matched to a user.</div>";
			}
			//echo "<div class=\"alert\"><strong>Warning!</strong> File '" . $file . "' could not be matched to a user.</div>";
		} else {
			echo "<div class=\"alert alert-danger\"><strong>Error!</strong> File '" . $file . "' is not an image.</div>";
		}
	}
	
	?>
</div>