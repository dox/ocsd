<?php
include_once("../includes/autoload.php");

$i_students = 0; // total students processed

$iplicit = new iPlicitAPI();
$cudPersons = (new Persons)->allStudents(); // get all students from CUD

foreach ($cudPersons AS $cudPerson) {
	if (isset($cudPerson['sits_student_code'])) { // only perform lookup on CUD persons with a SITS code
		$i_students++; // count how many students we're processing
		
		$sql  = "SELECT * FROM Enrolments WHERE cudid = '" . $cudPerson['cudid'] . "' ORDER BY SCJSequence DESC LIMIT 1";
		$cudPersonEnrolments = $db->query($sql)->fetchArray();
		
		$sql2  = "SELECT * FROM EnrolAwdProg WHERE cudid = '" . $cudPerson['cudid'] . "' ORDER BY Code DESC LIMIT 1";
		$cudPersonEnrolAwdProg = $db->query($sql2)->fetchArray();
		
		$iPlicitPersonArray = $iplicit->getContactAccount($cudPerson['sits_student_code']);
		
		// check if the iPlicit record already exists or needs to be created
		if (isset($iPlicitPersonArray->id)) {
			// contact already exists in iPlicit
			
			$cudArray = array(
				'description' => $cudPerson['FullName'],
				'code' => $cudPerson['sits_student_code'],
				'contact' => array(
					'intRef' => $cudPerson['sits_student_code'],
					'title' => $cudPerson['titl_cd'],
					'firstName' => $cudPerson['firstname'],
					'middleName' => $cudPerson['middlenames'],
					'lastName' => $cudPerson['lastname'],
				),
				'customer' => array(
					'ext' => array(
						'Currentyear' => $cudPerson['unit_set_cd'],
						'AwardProgrammeTitle' => $cudPersonEnrolAwdProg['AwdName'],
						'AwardProgrammeCode' => $cudPersonEnrolAwdProg['CrsCd'],
						'ExpectedEndDate' => $cudPersonEnrolments['CrsExpEndDt'],
						'Currentyear' => $cudPerson['unit_set_cd'],
					),
					'contactGroupCustomerId' => cudCardTypeToiPlicitGroup($cudPerson['university_card_type']),
				)
			);
			
			if (isset($cudPerson['sso_username'])) {
				$cudArray['customer']['ext']['SSO'] = $cudPerson['sso_username'];
			}
			
			if (isset($cudPerson['oxford_email'])) {
				$cudArray['contact']['emails'][] = array("type" => "R", "email" => $cudPerson['oxford_email']);
			}
			
			if (isset($cudPerson['alt_email'])) {
				$cudArray['contact']['emails'][] = array("type" => "P", "email" => $cudPerson['alt_email']);
			}
			
			if (isset($cudPerson['TelNo'])) {
				$cudArray['contact']['phones'][] = array("type" => "H", "email" => $cudPerson['TelNo']);
			}
			
			if (isset($cudPerson['MobileNo'])) {
				$cudArray['contact']['phones'][] = array("type" => "M", "email" => $cudPerson['MobileNo']);
			}
			
			if (isset($cudPersonEnrolments['SCJStatusName'])) {
				$cudArray['customer']['ext']['Activestatus'] = $cudPersonEnrolments['SCJStatusName'];
			}
			
			print_r($iPlicitPersonArray);

			$differences = compareArrays($cudArray, $iPlicitPersonArray);
			print_r($differences);
			
			exit("END");
			
			
			/*
			
			
			$cudAddress = $cudPerson->address('C');
			
			$cleanAddress = null;
			if (isset($cudAddress['AddressCtryCd']) || isset($cudAddress['Line1']) || isset($cudAddress['Line2'])) {
			  if (isset($cudAddress['Line1'])) {
				$cleanAddress = $cudAddress['Line1'];
			  }
			  if (isset($cudAddress['Line2'])) {
				$cleanAddress = $cleanAddress . ", " . $cudAddress['Line2'];
			  }
			  if (isset($cudAddress['Line3'])) {
				$cleanAddress = $cleanAddress . ", " . $cudAddress['Line3'];
			  }
			  if (isset($cudAddress['Line4'])) {
				$cleanAddress = $cleanAddress . ", " . $cudAddress['Line4'];
			  }
			  if (isset($cudAddress['Line5'])) {
				$cleanAddress = $cleanAddress . ", " . $cudAddress['Line5'];
			  }
			  
			  if (!empty($cudAddress['PostCode'])) {
				  $cleanPostcode = $cudAddress['PostCode'];
			  } else {
				  $cleanPostcode = "Unknown";
			  }
			  
			  $iplicitContact['contact']['addresses'][0]['type'] = "R";
			  $iplicitContact['contact']['addresses'][0]['address'] = $cleanAddress;
			  $iplicitContact['contact']['addresses'][0]['postcode'] = $cleanPostcode;
			  $iplicitContact['contact']['addresses'][0]['city'] = $cudAddress['City'] . " " . $cudAddress['State'];
			  $iplicitContact['contact']['addresses'][0]['county'] = $cudAddress['County'];
			  $iplicitContact['contact']['addresses'][0]['countryCode'] = cudCountryCodeToiPlicitCountyCode($cudAddress['AddressCtryCd']);
			  $iplicitContact['contact']['addresses'][0]['description'] = "Last updated: " . $cudAddress['LastUpdateDt'];
			}
			
			
			
			*/
			
			
			
			
			
			
			
			
			if ($iplicit->updateRequired($iPlicitFriendlyCUDArray, $exisitingiplicitContact)){
				//$iplicit->updateContactAccount($exisitingiplicitContact->code, $iPlicitFriendlyCUDArray);
			} else {
				cliOutput("Skipping update for " . $cudPerson['FullName'] . " (" . $cudPerson['sits_student_code'] . ")", "white");
			}
			
		} else { // contact needs to be crated in iPlicit
			// add additional fields
			$iPlicitFriendlyCUDArray['customer']['paymentMethodId'] = "BC";
			
			//$iplicit->createContactAccount($iPlicitFriendlyCUDArray);
		}
	}
}




function compareArrays($array, $object) {
	$differences = array();

	foreach ($array as $key => $value) {
		// Check if the key exists in the object
		if (isset($object->{$key})) {
			// If the value is an array and the object property is an object, recursively call compareArrays
			if (is_array($value) && is_object($object->{$key})) {
				$nestedDifferences = compareArrays($value, $object->{$key});
				// If there are nested differences, add them to the differences array
				if (!empty($nestedDifferences)) {
					$differences[$key] = $nestedDifferences;
				}
			} 
			// If the values are different, add the key-value pair to the differences array
			elseif ($object->{$key} !== $value) {
				$differences[$key] = $value;
			}
		} 
		// If the key doesn't exist in the object, add it to the differences array
		else {
			$differences[$key] = $value;
		}
	}

	return $differences;
}




//exit("EOF");






cliOutput($i_students . " students processed of " . count($cudPersons) . " CUD persons", "green");
cliOutput($iplicit->i_updated . " students updated", "green");
cliOutput($iplicit->i_created . " students created", "green");
cliOutput($iplicit->i_error . " errors encountered", "red");





class iPlicitAPI {
	public $sessionToken;
	public $tokenDue;
	
	public $i_updated = 0; 	// total iPlicit contacts updated
	public $i_created = 0; 	// total iPlicit contacts created
	public $i_error = 0; 	// total iPlicit errors encountered

	public $updateLog = array(); 	// array of iPlicit updates
	public $createLog = array(); 	// array of iPlicit creations
	public $errorLog = array(); 	// array of iPlicit errors
	
	function __construct() {
		$url = 'https://api.iplicit.com/api/session/create/api';
		
		$sessionFields = array(
		  'username' => iplicit_api_username,
		  'userApiKey' => iplicit_api_userApiKey
		);
		$sessionHeaders = array(
		  'Content-Type:application/json',
		  'Domain:' . iplicit_api_domain
		);
		
		// open curl to iPlicit API and authenticate
		$curl_session = curl_init();
		 
		curl_setopt($curl_session, CURLOPT_URL, $url);
		curl_setopt($curl_session, CURLOPT_POST, TRUE);
		curl_setopt($curl_session, CURLOPT_POSTFIELDS, json_encode($sessionFields));
		curl_setopt($curl_session, CURLOPT_HTTPHEADER, $sessionHeaders);
		curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, TRUE);
		
		$session = json_decode(curl_exec($curl_session));
		
		cliOutput("Connected to iPlicit API.  Token: " . $session->sessionToken, "white");
		
		// get a session token (and expiry) to be used in all following API calls
		$this->sessionToken = $session->sessionToken;
		$this->tokenDue = $session->tokenDue;
		
		curl_close($curl_session);
	}
	
	public function headers() {
		$headers = array(
		  'Content-Type:application/json',
		  'Domain:' . iplicit_api_domain,
		  'Authorization: Bearer ' . $this->sessionToken
		);
		
		return $headers;
	}
	
	public function getContactAccount($idOrCode) {
		$url = "https://api.iplicit.com/api/ContactAccount/" . $idOrCode . "?include=customer%2Csupplier%2Ccontact";
		
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers());
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$existingUser = json_decode(curl_exec($curl));
		
		curl_close($curl);
		
		if (!empty($existingUser)) {
			return $existingUser;
		} else {
			return false;
		}
	}
	
	public function updateContactAccount($idOrCode, $contactArray) {
		$url = "https://api.iplicit.com/api/ContactAccount/" . $idOrCode;
		
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($contactArray));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers());
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$data = json_decode(curl_exec($curl));
		
		if (isset($data->type) || isset($data->message)) {
			$this->i_error++;
			
			$event = "Error updating iPlicit record for " . $contactArray['description'] . " (" . $contactArray['code'] . ") - " . json_encode($data);
			
			$this->errorLog[] = $event;
			cliOutput($event, "red");
			debug($event);
			$logInsert = (new Logs)->insert("cron","error",null,$event);
		} else {
			$this->i_updated++;
			
			$event = "Updated iPlicit record for " . $contactArray['description'] . " (" . $contactArray['code'] . ") - " . json_encode($data);
			
			$this->updateLog[] = $event;
			cliOutput($event, "green");
			debug($event);
			
			if (debug) {
				$logInsert = (new Logs)->insert("cron","success",null,$event);
			}
		}
		
		curl_close($curl);
	}
	
	public function createContactAccount($contactArray) {
		$url = "https://api.iplicit.com/api/ContactAccount/";
		
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, TRUE);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($contactArray));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers());
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$data = json_decode(curl_exec($curl));
		
		if (isset($data->type) || isset($data->message)) {
			$this->i_error++;
			
			$event = "Error creating iPlicit record for " . $contactArray['description'] . " (" . $contactArray['code'] . ") - " . json_encode($data);
			
			$this->errorLog[] = $event;
			cliOutput($event, "red");
			debug($event);
			$logInsert = (new Logs)->insert("cron","error",null,$event);
		} else {
			$this->i_created++;
			
			$event = "Created iPlicit record for " . $contactArray['description'] . " (" . $contactArray['code'] . ") - " . json_encode($data);
			
			$this->createLog[] = $event;
			cliOutput($event, "green");
			debug($event);
			$logInsert = (new Logs)->insert("cron","success",null,$event);
		}
		
		curl_close($curl);
	}
}
?>