<?php
include_once("../includes/autoload.php");

$cudPersons = (new Persons)->allStudents(); // get all students from CUD
$i_students = 0;

$iplicit = new iPlicitAPI();

foreach ($cudPersons AS $cudPerson) {
	if (isset($cudPerson['sits_student_code'])) { // only perform lookup on CUD persons with a SITS code
		$i_students++; // count how many students we're processing

		$exisitingiplicitContact = $iplicit->getContactAccount($cudPerson['sits_student_code']);
		$iPlicitFriendlyCUDArray = $iplicit->cudidToiPlicitContact($cudPerson['cudid']);
		
		if (isset($exisitingiplicitContact->id)) { // contact already exists in iPlicit
			//only update if something is different
			if ($iplicit->updateRequired($iPlicitFriendlyCUDArray, $exisitingiplicitContact)){
				$iplicit->updateContactAccount($exisitingiplicitContact->code, $iPlicitFriendlyCUDArray);
			} else {
				cliOutput("Skipping update for " . $cudPerson['FullName'] . " (" . $cudPerson['sits_student_code'] . ")", "white");
			}
			
		} else { // contact needs to be crated in iPlicit
			// add additional fields
			$iPlicitFriendlyCUDArray['customer']['paymentMethodId'] = "BC";
			
			$iplicit->createContactAccount($iPlicitFriendlyCUDArray);
		}
	}
}

cliOutput($i_students . " students processed of " . count($cudPersons) . " CUD persons", "green");
cliOutput($iplicit->i_updated . " students updated", "green");
cliOutput($iplicit->i_created . " students created", "green");
cliOutput($iplicit->i_error . " errors encountered", "red");

# ----------------------------------------------- #
#           PROCESS EMAIL NOTIFICATION            #
# ----------------------------------------------- #
//email here!
$mail_body  = "<p>iPlicit/CUD sync complete for " . $i_students . autoPluralise(" user ", " users ", $i_students) . "with SITS IDs (of a total of " . $i_persons . " CUD persons) at " . date('Y-m-d H:i:s') . "</p>";
$mail_body .= "<p>" . $iplicit->i_updated . autoPluralise(" account was ", " accounts were ", $iplicit->i_updated) . "updated.</p>";
$mail_body .= "<p>The following " . $iplicit->i_created . autoPluralise(" account was ", " accounts were ", $i_created) . "created:</p>";
$mail_body .= "<ul>";

foreach ($iplicit->createLog AS $transaction) {
  $mail_body .= "<li>" . $transaction . "</li>";
}
$mail_body . "</ul>";

if ($iplicit->i_error > 0) {
	$mail_body .= "<hr />";
	$mail_body .= "<p>The following " . $iplicit->i_error . autoPluralise(" error was ", " errors were ", $iplicit->i_error) . "encountered:</p>";
	$mail_body .= "<ul>";
	
	foreach ($iplicit->errorLog AS $transaction) {
	  $mail_body .= "<li>" . $transaction . "</li>";
	}
	$mail_body .= "</ul>";
	$mail_body .= "<hr />";
}

$mail_subject = "iPlicit/CUD sync";
$mail_recipients = iplicit_api_notifications;

// only email if accounts were created
if ($iplicit->i_created > 0 || $iplicit->i_error > 0) {
  sendMail($mail_subject, $mail_recipients, $mail_body);
  cliOutput("Sending email to: " . implode(", ", $mail_recipients), "green");
}

$logInsert = (new Logs)->insert("cron","success",null,"iPlicit sync complete for " . $i_students . " student(s)");








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
			
			$this->$createLog[] = $event;
			cliOutput($event, "green");
			debug($event);
			$logInsert = (new Logs)->insert("cron","success",null,$event);
		}
		
		curl_close($curl);
	}
	
	public function cudidToiPlicitContact($cudid) {
		// find a person from their cudid and return an array formatted corrected for iPlicit
		
		global $db;
		
		$cudPerson = new Person($cudid);
		
		$sql  = "SELECT * FROM Enrolments WHERE cudid = '" . $cudPerson->cudid . "'";
		$cudPersonEnrolments = $db->query($sql)->fetchAll()[0];
		
		$iplicitContact['description'] = $cudPerson->FullName;
		$iplicitContact['code'] = $cudPerson->sits_student_code;
		$iplicitContact['contact']['intRef'] = $cudPerson->sits_student_code;
		$iplicitContact['contact']['title'] = $cudPerson->titl_cd;
		$iplicitContact['contact']['firstName'] = $cudPerson->firstname;
		$iplicitContact['contact']['middleName'] = $cudPerson->middlenames;
		$iplicitContact['contact']['lastName'] = $cudPerson->lastname;
		
		if (isset($cudPerson->oxford_email)) {
		  $iplicitContact['contact']['emails'][] = array("type" => "R", "email" => $cudPerson->oxford_email);
		}
		if (isset($cudPerson->alt_email)) {
		  $iplicitContact['contact']['emails'][] = array("type" => "P", "email" => $cudPerson->alt_email);
		}
		
		$cudAddress = $cudPerson->address('C');
		
		$cleanAddress = null;
		if (isset($cudAddress)) {
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
		
		if (!empty($cudAddress['TelNo'])) {
		  $iplicitContact['contact']['phones'][] = array("type" => "H", 'phone' => $cudAddress['TelNo']);
		}
		if (!empty($cudAddress['MobileNo'])) {
		  $iplicitContact['contact']['phones'][] = array("type" => "M", 'phone' => $cudAddress['MobileNo']);
		}
		
		if (!empty($cudPerson->sso_username)) {
		  $iplicitContact['customer']['ext']['SSO'] = $cudPerson->sso_username;
		}
		
		if (!empty($cudPersonEnrolments['SCJStatusName'])) {
		  $iplicitContact['customer']['ext']['Activestatus'] = $cudPersonEnrolments['SCJStatusName'];
		}
		
		$iplicitContact['customer']['contactGroupCustomerId'] = cudCardTypeToiPlicitGroup($cudPerson->university_card_type);
		
		return $iplicitContact;
	}
	
	public function updateRequired($cud, $iplicit) {
		$update = false;
		$changeFields = array();
		
		//printArray($iplicit);
		
		if ($cud['description'] != $iplicit->description) {
			$changeFields['description'] = $cud['description'] . " != " . $iplicit->description;
			$update = true;
		}
		if ($cud['contact']['title'] != $iplicit->contact->title) {
			$update = true;
			$changeFields['title'] = $cud['contact']['title'] . " != " . $iplicit->contact->title;
		}
		if ($cud['contact']['firstName'] != $iplicit->contact->firstName) {
			$update = true;
			$changeFields['firstName'] = $cud['contact']['firstName'] . " != " . $iplicit->contact->firstName;
		}
		if ($cud['contact']['middleName'] != $iplicit->contact->middleName) {
			$update = true;
			$changeFields['middleName'] = $cud['contact']['middleName'] . " != " . $iplicit->contact->middleName;
		}
		if ($cud['contact']['lastName'] != $iplicit->contact->lastName) {
			$update = true;
			$changeFields['lastName'] = $cud['contact']['lastName'] . " != " . $iplicit->contact->lastName;
		}
		
		if ($cud['contact']['emails'][0]['email'] != $iplicit->contact->emails[0]->email) {
			$update = true;
			$changeFields['email0'] = $cud['contact']['emails'][0]['email'] . " != " . $iplicit->contact->emails[0]->email;
		}
		if ($cud['contact']['emails'][1]['email'] != $iplicit->contact->emails[1]->email) {
			$update = true;
			$changeFields['email1'] = $cud['contact']['emails'][1]['email'] . " != " . $iplicit->contact->emails[1]->email;
		}
		
		if ($cud['contact']['phones'][0]['phone'] != $iplicit->contact->phones[0]->phone) {
			$update = true;
			$changeFields['phone0'] = $cud['contact']['phones'][0]['phone'] . " != " . $iplicit->contact->phones[0]->phone;
		}
		if ($cud['contact']['phones'][1]['phone'] != $iplicit->contact->phones[1]->phone) {
			$update = true;
			$changeFields['phone1'] = $cud['contact']['phones'][1]['phone'] . " != " . $iplicit->contact->phones[1]->phone;
		}
		
		if ($cud['customer']['ext']['SSO'] != $iplicit->customer->ext->SSO) {
			$update = true;
			$changeFields['email1'] = $cud['contact']['ext']['sso'] . " != " . $iplicit->contact->ext->sso;
		}
		
		if ($cud['customer']['ext']['SCJStatusName'] != $iplicit->customer->ext->SCJStatusName) {
			$update = true;
			$changeFields['SCJStatusName'] = $cud['contact']['ext']['SCJStatusName'] . " != " . $iplicit->contact->ext->SCJStatusName;
		}
		
		if ($cud['customer']['contactGroupCustomerId'] != $iplicit->customer->contactGroupCustomerId) {
			//$update = true;
			//$changeFields['contactGroupCustomerId'] = $cud['customer']['contactGroupCustomerId'] . " != " . $iplicit->customer->contactGroupCustomerId;
		}
		
		if ($cud['contact']['addresses'][0]['address'] != $iplicit->contact->addresses[0]->address) {
			$update = true;
			$changeFields['address'] = $cud['contact']['addresses'][0]['address'] . " != " . $iplicit->contact->addresses[0]->address;
		}
		
		if (!empty($changeFields)){
			debug($changeFields);
		}
		
		return $update;
	}
}
?>