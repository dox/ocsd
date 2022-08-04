<?php
include_once("../includes/autoload.php");

$i_persons = 0;		// total CUD persons
$i_students = 0;	// total CUD students
$i_updated = 0; 	// total iPlicit contacts updated
$i_created = 0; 	// total iPlicit contacts created

$cudPersons = (new Persons)->allStudents(); // get all students from CUD
$i_persons = count($cudPersons);

$iplicit = new iPlicitAPI();

foreach ($cudPersons AS $cudPerson) {
	if (isset($cudPerson['sits_student_code'])) { // only perform lookup on CUD persons with a SITS code
		$i_students++; // count how many students we're processing

		$exisitingiplicitContact = $iplicit->getContactAccount($cudPerson['sits_student_code']);
		$iPlicitFriendlyCUDArray = $iplicit->cudidToiPlicitContact($cudPerson['cudid']);
		
		if (isset($exisitingiplicitContact->id)) { // contact already exists in iPlicit
			//only update if something is different
			if ($iplicit->updateRequired($iPlicitFriendlyCUDArray, $exisitingiplicitContact)){
				$i_updated++;
				//printArray($exisitingiplicitContact);
				//printArray($iPlicitFriendlyCUDArray);
				
				$iplicit->updateContactAccount($exisitingiplicitContact->code, $iPlicitFriendlyCUDArray);
			} else {
				cliOutput("Skipping update for " . $cudPerson['FullName'] . " (" . $cudPerson['sits_student_code'] . ")", "white");
			}
			
		} else { // contact needs to be crated in iPlicit
			$i_created++;
						
			// add additional fields
			$iPlicitFriendlyCUDArray['customer']['paymentMethodId'] = "BC";
			
			$iplicit->createContactAccount($iPlicitFriendlyCUDArray);
			
			$emailOutput[] = $cudPerson->FullName . " (" . $cudPerson->sits_student_code . ")";
		}
	}
}

cliOutput($i_students . " students processed of " . $i_persons . " CUD persons", "green");
cliOutput($i_updated . " students updated", "green");
cliOutput($i_created . " students created", "green");

# ----------------------------------------------- #
#           PROCESS EMAIL NOTIFICATION            #
# ----------------------------------------------- #
//email here!
$mail_body  = "<p>iPlicit/CUD sync complete for " . $i_students . autoPluralise(" user ", " users ", $i_students) . "with SITS IDs (of a total of " . $i_persons . " CUD persons) at " . date('Y-m-d H:i:s') . "</p>";
$mail_body .= "<p>" . $i_updated . autoPluralise(" account was ", " accounts were ", $i_updated) . "updated.</p>";
$mail_body .= "<p>The following " . $i_created . autoPluralise(" account was ", " accounts were ", $i_created) . "created:</p>";
$mail_body .= "<ul>";

foreach ($emailOutput AS $transaction) {
  $mail_body .= "<li>" . $transaction . "</li>";
}
$mail_body . "</ul>";

$mail_subject = "iPlicit/CUD sync";
$mail_recipients = iplicit_api_notifications;
$mail_recipients = array("andrew.breakspear@seh.ox.ac.uk");

// only email if accounts were created
if ($i_created > 0) {
  sendMail($mail_subject, $mail_recipients, $mail_body);
  cliOutput("Sending email to: " . implode(", ", $mail_recipients), "green");
}

$logInsert = (new Logs)->insert("cron","success",null,"iPlicit sync complete for " . $i_students . " student(s)");








class iPlicitAPI {
	public $sessionToken;
	public $tokenDue;
	
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
		
		if (isset($data->type)) {
			cliOutput("Error updating iPlicit record for " . $contactArray['description'] . " (" . $idOrCode . ")", "red");
			debug(json_encode($data));
			$logInsert = (new Logs)->insert("cron","error",null,"Error updating iPlicit record for " . $contactArray['description'] . " (" . $idOrCode . ") - " . json_encode($data));
		} else {
			cliOutput("Updated iPlicit record for " . $contactArray['description'] . " (" . $idOrCode . ")", "green");
			
			if (debug) {
				$logInsert = (new Logs)->insert("cron","success",null,"Updated iPlicit record for " . $contactArray['description'] . " (" . $idOrCode . ") - " . json_encode($data));
			}
		}
		
		curl_close($curl);
	}
	
	public function createContactAccount($idOrCode, $contactArray) {
		$url = "https://api.iplicit.com/api/ContactAccount/" . $idOrCode;
		
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, TRUE);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($contactArray));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers());
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$data = json_decode(curl_exec($curl));
		
		if (isset($data->type)) {
			echo "\033[31m Error creating iPlicit record for " . $contactArray['description'] . " (" . $idOrCode . ")\n";
			debug(json_encode($data));
			$logInsert = (new Logs)->insert("cron","error",null,"Error creating iPlicit record for " . $contactArray['description'] . " (" . $idOrCode . ") - " . json_encode($data));
		} else {
			echo "\033[33m Created iPlicit record for " . $contactArray['description'] . " (" . $idOrCode . ")\n";
			$logInsert = (new Logs)->insert("cron","success",null,"Created iPlicit record for " . $contactArray['description'] . " (" . $idOrCode . ") - " . json_encode($data));
			
			$emailOutput[] = $contactArray['description'] . " (" . $idOrCode . ")";
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
		  /*
		  $iplicitContact['contact']['addresses'][0]['type'] = "R";
		  $iplicitContact['contact']['addresses'][0]['address'] = $cleanAddress;
		  $iplicitContact['contact']['addresses'][0]['postcode'] = $cudAddress['PostCode'];
		  $iplicitContact['contact']['addresses'][0]['city'] = $cudAddress['City'] . " " . $cudAddress['State'];
		  $iplicitContact['contact']['addresses'][0]['county'] = $cudAddress['County'];
		  $iplicitContact['contact']['addresses'][0]['countryCode'] = cudCountryCodeToiPlicitCountyCode($cudAddress['AddressCtryCd']);
		  $iplicitContact['contact']['addresses'][0]['description'] = "Last updated: " . $cudAddress['LastUpdateDt'];
		  */
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
	
	public function updateRequired($authoritative, $check) {
		$update = false;
		$changeFields = array();
		
		if ($authoritative['description'] != $check->description) {
			$changeFields['description'] = $authoritative['description'] . " != " . $check->description;
			$update = true;
		}
		if ($authoritative['contact']['title'] != $check->contact->title) {
			$update = true;
			$changeFields['title'] = $authoritative['contact']['title'] . " != " . $check->contact->title;
		}
		if ($authoritative['contact']['firstName'] != $check->contact->firstName) {
			$update = true;
			$changeFields['firstName'] = $authoritative['contact']['firstName'] . " != " . $check->contact->firstName;
		}
		if ($authoritative['contact']['middleName'] != $check->contact->middleName) {
			$update = true;
			$changeFields['middleName'] = $authoritative['contact']['middleName'] . " != " . $check->contact->middleName;
		}
		if ($authoritative['contact']['lastName'] != $check->contact->lastName) {
			$update = true;
			$changeFields['lastName'] = $authoritative['contact']['lastName'] . " != " . $check->contact->lastName;
		}
		
		if ($authoritative['contact']['emails'][0]['email'] != $check->contact->emails[0]->email) {
			$update = true;
			$changeFields['email0'] = $authoritative['contact']['emails'][0]['email'] . " != " . $check->contact->emails[0]->email;
		}
		if ($authoritative['contact']['emails'][1]['email'] != $check->contact->emails[1]->email) {
			$update = true;
			$changeFields['email1'] = $authoritative['contact']['emails'][1]['email'] . " != " . $check->contact->emails[1]->email;
		}
		
		if ($authoritative['contact']['phones'][0]['phone'] != $check->contact->phones[0]->phone) {
			$update = true;
			$changeFields['phone0'] = $authoritative['contact']['phones'][0]['phone'] . " != " . $check->contact->phones[0]->phone;
		}
		if ($authoritative['contact']['phones'][1]['phone'] != $check->contact->phones[1]->phone) {
			$update = true;
			$changeFields['phone1'] = $authoritative['contact']['phones'][1]['phone'] . " != " . $check->contact->phones[1]->phone;
		}
		
		if ($authoritative['customer']['ext']['SSO'] != $check->customer->ext->SSO) {
			$update = true;
			$changeFields['email1'] = $authoritative['contact']['ext']['sso'] . " != " . $check->contact->ext->sso;
		}
		
		if ($authoritative['customer']['ext']['SCJStatusName'] != $check->customer->ext->SCJStatusName) {
			$update = true;
			$changeFields['SCJStatusName'] = $authoritative['contact']['ext']['SCJStatusName'] . " != " . $check->contact->ext->SCJStatusName;
		}
		
		if ($authoritative['customer']['contactGroupCustomerId'] != $check->customer->contactGroupCustomerId) {
			//$returnArray['customer']['contactGroupCustomerId'] = $authoritative['customer']['contactGroupCustomerId'];
		}
		
		if (!empty($changeFields)){
			debug($changeFields);
		}
		
		
		return $update;
	}
}
?>