<?php
include_once("../inc/autoload.php");

# SALTO ACTIONS TABLE
# 1 = Create new record.
# 2 = Update existing record.
# 3 = Update record if exists, create new one otherwise.
# 4 = Delete existing
# 8 = Cancel key

$createCount = 0;
$updateCount = 0;

// CONNECT TO SALTO DB
$connectionInfo = array( "Database"=>salto_db_name, "UID"=>salto_db_username, "PWD"=>salto_db_password, "TrustServerCertificate"=>"Yes");
$conn = sqlsrv_connect(salto_db_host, $connectionInfo);

if($conn) {
	cliOutput("Connection to SALTO db established", "white");
	
	cliOutput("Removing expired records from StagingTable!", "yellow");
	$sql = sqlsrv_query($conn, "DELETE FROM " . salto_db_table . " WHERE [UserExpiration.ExpDate] < '" . date('Y-m-d') . "'");
	
	$records = sqlsrv_query($conn, "SELECT * FROM " . salto_db_table);
	if ($records === false) {
		$logData = [
			'category' => 'cron',
			'result'   => 'error',
			'description' => 'Connection to " . salto_db_table . " could not be established'
		];
		$log->create($logData);
		die(printArray(sqlsrv_errors(), true));
	}
	
	// BUILD ARRAY OF SALTO USERS
	while($row = sqlsrv_fetch_array($records, SQLSRV_FETCH_ASSOC)) {
		$saltoUsers[$row['ExtUserID']] = $row;
	}
} else {
	cliOutput("Connection could not be established to SALTO db", "red");
	$logData = [
		'category' => 'cron',
		'result'   => 'error',
		'description' => 'Connection to SALTO db could not be established'
	];
	$log->create($logData);
	die(printArray(sqlsrv_errors(), true));
}


// BUILD ARRAY OF CUD PERSONS
$sql = "SELECT * FROM Person";
$cudPersons = $db->get($sql);
cliOutput("Connection to CUD db established", "green");

foreach ($cudPersons AS $cudPerson) {
	$updateArray = array();
	$columns = array();

	if (!empty($cudPerson['sso_username'])) {
		if (array_key_exists($cudPerson['sso_username'], $saltoUsers)) {
			// CHECK MULTIPLE VALUES TO SEE IF UPDATE NEEDED
			if (debug) {
				cliOutput($cudPerson['sso_username'] . " is a CUD Person, and exists in the SALTO staging table", "green");
			}
			
			if ($saltoUsers[$cudPerson['sso_username']]['FirstName'] != utf8_encode(str_replace('\'', '', $cudPerson['firstname']))) {
				$updateArray['FirstName'] = utf8_encode(str_replace('\'', '', $cudPerson['firstname']));
			}
			
			if ($saltoUsers[$cudPerson['sso_username']]['LastName'] != utf8_encode(str_replace('\'', '', $cudPerson['lastname']))) {
				echo "updating last name";
				$updateArray['LastName'] = utf8_encode(str_replace('\'', '', $cudPerson['lastname']));
			}
			
			if ($saltoUsers[$cudPerson['sso_username']]['Title'] != $cudPerson['university_card_type']) {
				$updateArray['Title'] = $cudPerson['university_card_type'];
			}
			
			if ($saltoUsers[$cudPerson['sso_username']]['GPF5'] != $cudPerson['crs_start_dt']) {
				$updateArray['GPF5'] = $cudPerson['universitycrs_start_dt_card_type'];
			}
			
			if ($saltoUsers[$cudPerson['sso_username']]['AutoKeyEdit.ROMCode'] != $cudPerson['MiFareID']) {
				$updateArray['AutoKeyEdit.ROMCode'] = $cudPerson['MiFareID'];
			}
			
			// photograph??
			
			if (count($updateArray) >= 1) {
				$updateArray['Action'] = "3";
				$updateArray['ToBeProcessedBySalto'] = "1";

				cliOutput($cudPerson['sso_username'] . " (" . $cudPerson['FullName'] . ") requires updating on " . implode(", ", array_keys($updateArray)), "magenta");
				
				$sql = "UPDATE StagingTable SET ";
				foreach ($updateArray AS $column => $value) {
					$columns[] = "[" . $column . "] = '" . $value . "'";
				}
				$sql .= implode(", ", $columns) . " WHERE ExtUserId = '" . $cudPerson['sso_username'] . "';";
				
				if (debug) {
					cliOutput($sql, "white");
				}
				
				$update = sqlsrv_query($conn, $sql);
				
				$name = mb_convert_encoding($cudPerson['FullName'], "UTF-8", "ISO-8859-1");
				$logData = [
					'category' => 'cron',
					'result'   => 'success',
					'cudid'   => $cudPerson['cudid'],
					'description' => $cudPerson['sso_username'] . ' updated ' . implode(', ', array_keys($updateArray)) . ' on SALTO staging table'
				];
				$log->create($logData);
				$updateCount ++;
				if ($update === false) {
					$logData = [
						'category' => 'cron',
						'result'   => 'error',
						'cudid'   => $cudPerson['cudid'],
						'description' => 'Error updating ' . $cudPerson['sso_username'] . ' on SALTO staging table'
					];
					$log->create($logData);
					die(printArray(sqlsrv_errors(), true));
				}
			}
			
			
			
		} else {
			// CREATE/IMPORT NEW USER INTO SALTO
			//printArray($cudPerson);
			cliOutput($cudPerson['sso_username'] . " (" . $cudPerson['FullName'] . ") requires creating", "cyan");
			
			$updateArray['Action'] = "3";
			$updateArray['ToBeProcessedBySalto'] = "1";
			$updateArray['ExtUserID'] = $cudPerson['sso_username'];
			$updateArray['FirstName'] = utf8_encode(str_replace('\'', '', $cudPerson['firstname']));
			$updateArray['LastName'] = utf8_encode(str_replace('\'', '', $cudPerson['lastname']));
			$updateArray['Title'] = $cudPerson['university_card_type'];
			
			// IF STUDENT, SET OFFIEC TO 0
			$studentArrayTypes = array('GT', 'GR', 'UG', 'VR', 'PT', 'VD', 'VV', 'VC');
			if (in_array($cudPerson['university_card_type'], $studentArrayTypes)) {
				$updateArray['Office'] = "0";
			} else {
				$updateArray['Office'] = "1";
			}
			
			$updateArray['Privacy'] = "0";
			$updateArray['AuditOpenings'] = "1";
			$updateArray['ExtendedOpeningTime'] = "0";
			$updateArray['Antipassback'] = "1";
			$updateArray['CalendarID'];
			$updateArray['GPF1'];
			$updateArray['GPF2'];
			$updateArray['GPF3'];
			$updateArray['GPF4'] = $cudPerson['sso_username'];
			$updateArray['GPF5'] = $cudPerson['crs_start_dt'];
			$updateArray['ExtAccessLevellDList'];
			$updateArray['AutoKeyEdit.ROMCode'] = $cudPerson['MiFareID'];
			$updateArray['UserActivation'] = date("Y/m/d G:i:s");
			$updateArray['UserExpiration.ExpDate'] = date("Y/m/d", strtotime($cudPerson['University_Card_End_Dt']));
			$updateArray['STKE.Period'] = "30";
			$updateArray['STKE.UnitOfPeriod'] = "0";
			$updateArray['PIN.Code'];
			$updateArray['WiegandCode'];
			$updateArray['NewKeyIsCancellableThroughBL'] = "1";
			$updateArray['ProcessedDateTime'] = date("Y/m/d G:i:s");
			$updateArray['ErrorCode'] = "0";
			$updateArray['ErrorMessage'];
			$updateArray['PhotographFile'];
			
			$sql = "INSERT INTO StagingTable ([" . implode("], [", array_keys($updateArray)) . "]) VALUES ('" . implode("', '", $updateArray) . "');";
			
			if (debug) {
				cliOutput($sql, "white");
			}
			
			$create = sqlsrv_query($conn, $sql);
			$name = mb_convert_encoding($cudPerson['FullName'], "UTF-8", "ISO-8859-1");
			$logData = [
				'category' => 'cron',
				'result'   => 'success',
				'cudid'   => $cudPerson['cudid'],
				'description' => $cudPerson['sso_username'] . ' created on SALTO staging table'
			];
			$log->create($logData);

			$createCount ++;
			if ($create === false) {
				$logData = [
					'category' => 'cron',
					'result'   => 'error',
					'cudid'   => $cudPerson['cudid'],
					'description' => 'Error creating ' . $cudPerson['sso_username'] . ' on SALTO staging table'
				];
				$log->create($logData);
				die(printArray(sqlsrv_errors(), true));
			}
		}	
	}
	
}

$event = "SALTO sync complete.  Created: " . $createCount . " / Updated: " . $updateCount;
$logData = [
	'category' => 'cron',
	'result'   => 'success',
	'description' => $event
];
$log->create($logData);
?>
