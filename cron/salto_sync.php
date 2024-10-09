<?php
include_once("../includes/autoload.php");

# SALTO ACTIONS TABLE
# 1 = Create new record.
# 2 = Update existing record.
# 3 = Update record if exists, create new one otherwise.
# 4 = Delete existing
# 8 = Cancel key

$createCount = 0;
$updateCount = 0;

// BUILD ARRAY OF CUD PERSONS
$cudPersons = (new Persons)->all();
cliOutput("Connection to CUD db established", "white");

// CONNECT TO SALTO DB
$connectionInfo = array( "Database"=>salto_db_name, "UID"=>salto_db_username, "PWD"=>salto_db_password, "TrustServerCertificate"=>"Yes");
$conn = sqlsrv_connect(salto_db_host, $connectionInfo);

if($conn) {
	cliOutput("Connection to SALTO db established", "white");
	
	cliOutput("Removing expired records from StagingTable!", "yellow");
	$sql = sqlsrv_query($conn, "DELETE FROM StagingTable WHERE [UserExpiration.ExpDate] < '" . date('Y-m-d') . "'");
	
	$records = sqlsrv_query($conn, "SELECT * FROM StagingTable");
	if ($records === false) {
		$logInsert = (new Logs)->insert("cron","error",null,"Connection to Staging Table on SALTO could not be established to SALTO db");
		die(printArray(sqlsrv_errors(), true));
	}
	
	// BUILD ARRAY OF SALTO USERS
	while($row = sqlsrv_fetch_array($records, SQLSRV_FETCH_ASSOC)) {
		$saltoUsers[$row['ExtUserID']] = $row;
	}
} else {
	cliOutput("Connection could not be established to SALTO db", "red");
	$logInsert = (new Logs)->insert("cron","error",null,"Connection to SALTO db could not be established");
	die(printArray(sqlsrv_errors(), true));
}

//printArray($cudPersons);
//printArray($saltoUsers);



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
				$logInsert = (new Logs)->insert("cron","success",null,$cudPerson['sso_username'] . " (" . $cudPerson['FullName'] . ") updated " . implode(", ", array_keys($updateArray)) . " on SALTO staging table");
				$updateCount ++;
				if ($update === false) {
					$logInsert = (new Logs)->insert("cron","error",null,"Error updating " . $cudPerson['sso_username'] . " (" . $cudPerson['FullName'] . ") on SALTO staging table");
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
			$logInsert = (new Logs)->insert("cron","success",null,$cudPerson['sso_username'] . " (" . $cudPerson['FullName'] . ") created on SALTO staging table");

			$createCount ++;
			if ($create === false) {
				$logInsert = (new Logs)->insert("cron","error",null,"Error creating " . $cudPerson['sso_username'] . " (" . $cudPerson['FullName'] . ") on SALTO staging table");
				die(printArray(sqlsrv_errors(), true));
			}
		}	
	}
	
}

$event = "SALTO sync complete.  Created: " . $createCount . " / Updated: " . $updateCount;
$logInsert = (new Logs)->insert("cron","success",null,$event);
?>