<?php
include_once("../inc/autoload.php");

$updateCount = 0;

// CONNECT TO Kx DB
$connectionInfo = array( "Database"=>kx_db_name, "UID"=>kx_db_username, "PWD"=>kx_db_password, "TrustServerCertificate"=>"Yes");
$conn = sqlsrv_connect(kx_db_host, $connectionInfo);

if($conn) {
	cliOutput("Connection to Kx db established", "green");
	
	cliOutput("Removing expired records from " . kx_db_table . " staging table", "yellow");
	$sql = sqlsrv_query($conn, "DELETE FROM " . kx_db_table . ";");
	
} else {
	$logData = [
		'category' => 'cron',
		'result'   => 'error',
		'description' => 'FAILED to connect to Kx database'
	];
	$log->create($logData);
	
	cliOutput("FAILED to connect to Kx database", "red");
	die();
}

// BUILD ARRAY OF CUD PERSONS
$sql = "SELECT cudid FROM Person";
$cudPersons = $db->get($sql);
cliOutput("Connection to CUD db established", "green");

foreach ($cudPersons AS $cudPerson) {
	$cudPerson = new Person($cudPerson['cudid']);
	$updateArray = array();
	
	$updateArray['StudentID'] = $cudPerson->sits_student_code; // Unique identifier to the student. Primary link between Kx and the student records system.
	$updateArray['Title'] = $cudPerson->titl_cd; // Title, e.g. Mr, Miss, etc
	$updateArray['Forename'] = str_replace("'", "''", $cudPerson->firstname); // Forename
	$updateArray['Midname'] = str_replace("'", "''", $cudPerson->middlenames); // Middle name or names
	$updateArray['Surname'] = str_replace("'", "''", $cudPerson->lastname); // Family name
	$updateArray['UCASNumber'] = '12345'; // Application identifier, typically UCAS number, but can often be other identifiers e.g. PG Applications etc
	$updateArray['AKAName'] = str_replace("'", "''", $cudPerson->known_as); // The name the student is known as if not their forename.
	//$updateArray['Nationality'] = ''; // Nationality description
	//$updateArray['Ethnicity'] = ''; // Language Preference
	$updateArray['Domicile'] = $cudPerson->dom_name; // Country of residence / origin / birth
	$updateArray['DOB'] = $cudPerson->dob; // Date of birth of the student
	$updateArray['Gender'] = in_array($g = strtoupper($cudPerson->gnd), ['M', 'F']) ? $g : 'U'; // Gender of the student, can only be M, F or U
	//$updateArray['Disability'] = ''; // Disability description, leave NULL if not disabled
	//$updateArray['FeeStatus'] = ''; // [No specific description provided]
	//$updateArray['AcademicStatus'] = ''; // Overall status of the student
	$homeAddress = $cudPerson->addresses()->getHomeAddress();
	$updateArray['HomeAddressType'] = $homeAddress['AddressTyp']; // Address Type description for home address, e.g. Home
	$updateArray['HomeAddress'] = str_replace("'", "''", mb_substr(implode(', ', array_filter([$homeAddress['Line1'] ?? '', $homeAddress['Line2'] ?? '', $homeAddress['Line3'] ?? '', $homeAddress['Line4'] ?? '', $homeAddress['Line5'] ?? ''])), 0, 500)); // First line or lines of the home address
	$updateArray['HomeTown'] = str_replace("'", "''", mb_substr(implode(', ', array_filter([$homeAddress['City'] ?? '', $homeAddress['State'] ?? ''])), 0, 50)); // Town or City of the home address
	$updateArray['HomeCounty'] = str_replace("'", "''", $homeAddress['AddressCtryDesc']); // County (or equivalent) of the home address
	$updateArray['HomeCountry'] = $homeAddress['AddressCtryDesc']; // Country of the home address
	$updateArray['HomePostcode'] = $homeAddress['PostCode']; // Post Code of the home address
	$updateArray['HomeTelephone'] = $homeAddress['TelNo']; // Home telephone number
	$updateArray['HomeMobile'] = $homeAddress['MobileNo']; // Alternative telephone number for the home address
	$updateArray['HomeEmail'] = $homeAddress['AddressEmail']; // Home email address
	//$updateArray['HomeForename'] = ''; // Forename of the home contact, if not the student
	//$updateArray['HomeSurname'] = ''; // Surname of the home contact, if not the student
	//$updateArray['HomeRelationship'] = ''; // Relationship of the home contact to the student, e.g. Father
	$contactAddress = $cudPerson->addresses()->getContactAddress();
	$updateArray['ContactAddressType'] = $contactAddress['AddressTyp']; // Address Type description for contact address, e.g. Contact
	$updateArray['ContactAddress'] = str_replace("'", "''", mb_substr(implode(', ', array_filter([$contactAddress['Line1'] ?? '', $contactAddress['Line2'] ?? '', $contactAddress['Line3'] ?? '', $contactAddress['Line4'] ?? '', $contactAddress['Line5'] ?? ''])), 0, 500)); // First line or lines of the contact address
	$updateArray['ContactTown'] = str_replace("'", "''", mb_substr(implode(', ', array_filter([$contactAddress['City'] ?? '', $contactAddress['State'] ?? ''])), 0, 50)); // Town or City of the contact address
	$updateArray['ContactCounty'] = str_replace("'", "''", mb_substr(implode(', ', array_filter([$contactAddress['State'] ?? '', $contactAddress['County'] ?? ''])), 0, 50)); // County (or equivalent) of the contact address
	$updateArray['ContactCountry'] = $contactAddress['AddressCtryDesc']; // Country of the contact address
	$updateArray['ContactPostcode'] = $contactAddress['PostCode']; // Postcode for the contact address
	$updateArray['ContactTelephone'] = $contactAddress['TelNo']; // Contact telephone number
	$updateArray['ContactMobile'] = $contactAddress['MobileNo']; // Contact mobile number
	$updateArray['ContactEmail'] = $contactAddress['AddressEmail']; // Contact email address
	//$updateArray['ContactForename'] = ''; // Forename of the contact at the contact address, if not the student.
	//$updateArray['ContactSurname'] = ''; // Surname of the contact at the contact address, if not the student
	//$updateArray['ContactRelationship'] = ''; // Relationship of the contact to the student, e.g. Father
	//$updateArray['EmergencyAddressType'] = ''; // Address Type description for emergency address, e.g. Emergency
	//$updateArray['EmergencyAddress'] = ''; // First line or lines of the emergency address
	//$updateArray['EmergencyTown'] = ''; // Town or City of the emergency address
	//$updateArray['EmergencyCounty'] = ''; // County (or equivalent) of the emergency address
	//$updateArray['EmergencyCountry'] = ''; // Country of the emergency address
	//$updateArray['EmergencyPostcode'] = ''; // Postcode for the emergency address
	//$updateArray['EmergencyTelephone'] = ''; // Emergency telephone number
	//$updateArray['EmergencyMobile'] = ''; // Emergency mobile number
	//$updateArray['EmergencyEmail'] = ''; // Emergency email address
	//$updateArray['EmergencyForename'] = ''; // Forename of the contact at the emergency address, if not the student.
	//$updateArray['EmergencySurname'] = ''; // Surname of the contact at the emergency address, if not the student
	//$updateArray['EmergencyRelationship'] = ''; // Relationship of the emergency contact to the student, e.g. Father
	//$updateArray['PreferredEmailAddressType'] = ''; // Address Type description for preferred email address, e.g. Web, University
	$updateArray['PreferredEmail'] = $cudPerson->oxford_email; // Preferred Email Address
	$updateArray['CourseCode'] = $cudPerson->rout_cd; // Course Code
	$updateArray['CourseName'] = str_replace("'", "''", $cudPerson->rout_name); // Course Name, associated with the Course Code
	$updateArray['Faculty'] = mb_substr($cudPerson->div_desc, 0, 40); // Faculty description, associated with the Course Code
	$updateArray['CourseType'] = $cudPerson->EnrolAwdProg()->all()[0]['CrsLevel']; // Course Type description, e.g. ‘PG’, ‘UG’ etc.
	$updateArray['CourseStartDate'] = $cudPerson->crs_start_dt; // Start date of the course (optional)
	$updateArray['CourseEnddate'] = $cudPerson->crs_exp_end_dt; // End date of the course (optional)
	//$updateArray['CourseYearOfEntry'] = '';
	$updateArray['CourseAttendanceMode'] = $cudPerson->mode_of_attendance; // How the student is attending the course, e.g. ‘FT’, ‘PT’
	$updateArray['CourseStatus'] = $cudPerson->course_status; // Student Status description, i.e. the status of the application for the specific course which may be different to the Academic Status.
	//$updateArray['CurrentCourse'] = '';
	//$updateArray['UDFName1'] = '';
	//$updateArray['UDFValue1'] = '';
	//$updateArray['UDFName2'] = '';
	//$updateArray['UDFValue2'] = '';
	//$updateArray['UDFName3'] = '';
	//$updateArray['UDFValue3'] = '';
	//$updateArray['UDFName4'] = '';
	//$updateArray['UDFValue4'] = '';
	//$updateArray['UDFName5'] = '';
	//$updateArray['UDFValue5'] = '';
	//$updateArray['UDFName6'] = '';
	//$updateArray['UDFValue6'] = '';
	//$updateArray['UDFName7'] = '';
	//$updateArray['UDFValue7'] = '';
	//$updateArray['UDFName8'] = '';
	//$updateArray['UDFValue8'] = '';
	//$updateArray['UDFName9'] = '';
	//$updateArray['UDFValue9'] = '';
	//$updateArray['UDFName10'] = '';
	//$updateArray['UDFValue10'] = '';
	//$updateArray['UDFName11'] = '';
	//$updateArray['UDFValue11'] = '';
	//$updateArray['UDFName12'] = '';
	//$updateArray['UDFValue12'] = '';
	//$updateArray['UDFName13'] = '';
	//$updateArray['UDFValue13'] = '';
	//$updateArray['UDFName14'] = '';
	//$updateArray['UDFValue14'] = '';
	//$updateArray['UDFName15'] = '';
	//$updateArray['UDFValue15'] = '';
	//$updateArray['UDFName16'] = '';
	//$updateArray['UDFValue16'] = '';
	//$updateArray['UDFName17'] = '';
	//$updateArray['UDFValue17'] = '';
	//$updateArray['UDFName18'] = '';
	//$updateArray['UDFValue18'] = '';
	//$updateArray['UDFName19'] = '';
	//$updateArray['UDFValue19'] = '';
	//$updateArray['UDFName20'] = '';
	//$updateArray['UDFValue20'] = '';
	//$updateArray['DebtorStatus'] = '';
	//$updateArray['InvoiceLanguage'] = '';
	$updateArray['HomeAddressValidFrom'] = $homeAddress['LastUpdateDt'];
	//$updateArray['HomeAddressValidTo'] = '';
	$updateArray['ContactAddressValidFrom'] = $contactAddress['LastUpdateDt'];
	//$updateArray['ContactAddressValidTo'] = '';
	//$updateArray['EmergencyAddressValidFrom'] = '';
	//$updateArray['EmergencyAddressValidTo'] = '';
	//$updateArray['UDFName21'] = '';
	//$updateArray['UDFValue21'] = '';
	//$updateArray['UDFName22'] = '';
	//$updateArray['UDFValue22'] = '';
	//$updateArray['UDFName23'] = '';
	//$updateArray['UDFValue23'] = '';
	//$updateArray['UDFName24'] = '';
	//$updateArray['UDFValue24'] = '';
	//$updateArray['UDFName25'] = '';
	
	$sql = "INSERT INTO " . kx_db_table . " ([" . implode("], [", array_keys($updateArray)) . "]) VALUES ('" . implode("', '", $updateArray) . "');";
	
	$create = sqlsrv_query($conn, $sql);
	
	if ($create) {
		$updateCount ++;
		cliOutput("Created Kx record for " . $cudPerson->FullName, "green");
	} else {
		$logData = [
			'category' => 'cron',
			'result'   => 'error',
			'cudid'   => $cudPerson->cudid,
			'description' => 'FAILED to create Kx record for ' . $cudPerson->FullName . ': ' . print_r(sqlsrv_errors(), true)
		];
		$log->create($logData);
		
		cliOutput("FAILED to create Kx record for " . $cudPerson->FullName . ": " . print_r(sqlsrv_errors(), true), "red");
	}
}

$db->upsertByName('cron_kx_sync', date('c'));
$logData = [
	'category' => 'cron',
	'result'   => 'success',
	'description' => 'Updated ' . $updateCount . ' records in the Kx staging table'
];
$log->create($logData);
?>
