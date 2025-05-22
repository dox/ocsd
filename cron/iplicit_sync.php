<?php
include_once("../inc/autoload.php");

$sql  = "SELECT cudid FROM Person WHERE university_card_type IN ('PG', 'GT', 'GR')";
$cudPersons = $db->get($sql);

$i_students = 0;
$iplicit = new iPlicitAPI();

foreach ($cudPersons AS $cudPerson) {
	$cudPerson = new Person($cudPerson['cudid']);
	
	$tokenTimeRemaining = strtotime($iplicit->tokenDue) - strtotime(gmdate('c'));
	
	if ($tokenTimeRemaining <= 1) {
		cliOutput("Updating token, as it has expired", "green");
		$iplicit->getSession();
	}
	if (isset($cudPerson->sits_student_code)) { // only perform lookup on CUD persons with a SITS code
		$i_students++; // count how many students we're processing

		$exisitingiplicitContact = $iplicit->getContactAccount($cudPerson->sits_student_code);
		$iPlicitFriendlyCUDArray = $iplicit->cudidToiPlicitContact($cudPerson->cudid);
		
		if (isset($exisitingiplicitContact->id)) { // contact already exists in iPlicit
			//only update if something is different
			if ($iplicit->updateRequired($iPlicitFriendlyCUDArray, $exisitingiplicitContact)){
				$iplicit->updateContactAccount($exisitingiplicitContact->code, $iPlicitFriendlyCUDArray);
			} else {
				cliOutput("Skipping update for " . $cudPerson->FullName . " (" . $cudPerson->sits_student_code . ")", "white");
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
$mail_body  = "<p>iPlicit/CUD sync complete for " . $i_students . autoPluralise(" user ", " users ", $i_students) . "with SITS IDs (of a total of " . count($cudPersons) . " CUD persons) at " . date('Y-m-d H:i:s') . "</p>";
$mail_body .= "<p>" . $iplicit->i_updated . autoPluralise(" account was ", " accounts were ", $iplicit->i_updated) . "updated.</p>";
$mail_body .= "<p>The following " . $iplicit->i_created . autoPluralise(" account was ", " accounts were ", $i_created) . "created:</p>";
$mail_body .= "<ul>";

foreach ($iplicit->createLog AS $transaction) {
  $mail_body .= "<li>" . $transaction . "</li>";
}
$mail_body .= "</ul>";

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
		$this->getSession();
	}
	
	public function getSession() {
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
			
			$this->createLog[] = $event;
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
		
		$sql  = "SELECT * FROM Enrolments WHERE cudid = '" . $cudPerson->cudid . "' ORDER BY SCJSequence DESC";
		$cudPersonEnrolments = $db->get($sql);
		
		$sql2  = "SELECT * FROM EnrolAwdProg WHERE cudid = '" . $cudPerson->cudid . "' ORDER BY Code DESC";
		$cudPersonEnrolAwdProg = $db->get($sql);
		
		$iplicitContact['description'] = $cudPerson->FullName;
		$iplicitContact['code'] = $cudPerson->sits_student_code;
		$iplicitContact['contact']['intRef'] = $cudPerson->sits_student_code;
		$iplicitContact['contact']['title'] = $cudPerson->titl_cd;
		$iplicitContact['contact']['firstName'] = $cudPerson->firstname;
		$iplicitContact['contact']['middleName'] = $cudPerson->middlenames;
		$iplicitContact['contact']['lastName'] = $cudPerson->lastname;
		$iplicitContact['customer']['ext']['Currentyear'] = $cudPerson->unit_set_cd;
		
		$iplicitContact['customer']['ext']['AwardProgrammeTitle'] = $cudPersonEnrolAwdProg['AwdName'];
		$iplicitContact['customer']['ext']['AwardProgrammeCode'] = $cudPersonEnrolAwdProg['CrsCd'];
		$iplicitContact['customer']['ext']['ExpectedEndDate'] = $cudPersonEnrolments['CrsExpEndDt'];

		if (isset($cudPerson->oxford_email)) {
		  $iplicitContact['contact']['emails'][] = array("type" => "R", "email" => $cudPerson->oxford_email);
		}
		if (isset($cudPerson->alt_email)) {
		  $iplicitContact['contact']['emails'][] = array("type" => "P", "email" => $cudPerson->alt_email);
		}
		
		$cudAddress = $cudPerson->addresses()->getContactAddress();
		
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
		
		if ($cud['customer']['ext']['Activestatus'] != $iplicit->customer->ext->Activestatus) {
			$update = true;
			$changeFields['Activestatus'] = $cud['contact']['ext']['Activestatus'] . " != " . $iplicit->contact->ext->Activestatus;
		}
		
		if ($cud['customer']['ext']['Currentyear'] != $iplicit->customer->ext->Currentyear) {
			$update = true;
			$changeFields['Currentyear'] = $cud['contact']['ext']['Currentyear'] . " != " . $iplicit->contact->ext->Currentyear;
		}
		
		if ($cud['customer']['contactGroupCustomerId'] != $iplicit->customer->contactGroupCustomerId) {
			//$update = true;
			//$changeFields['contactGroupCustomerId'] = $cud['customer']['contactGroupCustomerId'] . " != " . $iplicit->customer->contactGroupCustomerId;
		}
		
		if ($cud['customer']['ext']['AwardProgrammeTitle'] != $iplicit->customer->ext->AwardProgrammeTitle ||
			$cud['customer']['ext']['AwardProgrammeCode'] != $iplicit->customer->ext->AwardProgrammeCode ||
			$cud['customer']['ext']['ExpectedEndDate'] != $iplicit->customer->ext->ExpectedEndDate) {
			
			$update = true;
			$changeFields['AwardProgrammeTitle'] = $cud['customer']['ext']['AwardProgrammeTitle'];
			$changeFields['AwardProgrammeCode'] = $cud['customer']['ext']['AwardProgrammeCode'];
			$changeFields['ExpectedEndDate'] = $cud['customer']['ext']['ExpectedEndDate'];
		}


		if ($cud['contact']['addresses'][0]['address'] != $iplicit->contact->addresses[0]->address) {
			$update = true;
			$changeFields['address'] = $cud['contact']['addresses'][0]['address'] . " != " . $iplicit->contact->addresses[0]->address;
		}
		
		if (!empty($changeFields)){
			printArray($changeFields);
		}
		
		return $update;
	}
}



function cudCountryCodeToiPlicitCountyCode($countryCode) {
	
	$countryArray = array(
		'602' => 'AF',
		'651' => 'AX',
		'603' => 'AL',
		'604' => 'DZ',
		'605' => 'AD',
		'606' => 'AO',
		'607' => 'AG',
		'608' => 'AR',
		'836' => 'AM',
		'637' => 'AW',
		'609' => 'AU',
		'610' => 'AT',
		'837' => 'AZ',
		'611' => 'BS',
		'612' => 'BH',
		'787' => 'BD',
		'613' => 'BB',
		'838' => 'BY',
		'614' => 'BE',
		'668' => 'BZ',
		'640' => 'BJ',
		'615' => 'BM',
		'616' => 'BT',
		'617' => 'BO',
		'848' => 'BA',
		'618' => 'BW',
		'619' => 'BR',
		'776' => 'VG',
		'620' => 'BN',
		'621' => 'BG',
		'769' => 'BF',
		'622' => 'MM',
		'623' => 'BI',
		'624' => 'KH',
		'625' => 'CM',
		'626' => 'CA',
		'751' => 'IC',
		'788' => 'CV',
		'789' => 'KY',
		'627' => 'CF',
		'629' => 'TD',
		'826' => 'XL',
		'630' => 'CL',
		'631' => 'CN',
		'652' => 'TW',
		'632' => 'CO',
		'804' => 'KM',
		'634' => 'CG',
		'633' => 'CD',
		'714' => 'CK',
		'635' => 'CR',
		'834' => 'HR',
		'636' => 'CU',
		'638' => 'XA',
		'849' => 'CZ',
		'641' => 'DK',
		'749' => 'DJ',
		'642' => 'DM',
		'643' => 'DO',
		'786' => 'TL',
		'645' => 'EC',
		'768' => 'EG',
		'646' => 'SV',
		'790' => 'GQ',
		'851' => 'ER',
		'831' => 'EE',
		'648' => 'ET',
		'649' => 'FK',
		'865' => 'FO',
		'650' => 'FJ',
		'651' => 'FI',
		'653' => 'FR',
		'791' => 'GF',
		'822' => 'PF',
		'654' => 'GA',
		'655' => 'GM',
		'847' => 'GE',
		'656' => 'DE',
		'658' => 'GH',
		'659' => 'GI',
		'661' => 'GR',
		'828' => 'GL',
		'662' => 'GD',
		'653' => 'GP',
		'796' => 'GU',
		'663' => 'GT',
		'593' => 'GG',
		'664' => 'GN',
		'802' => 'GW',
		'665' => 'GY',
		'666' => 'HT',
		'667' => 'HN',
		'669' => 'HK',
		'670' => 'HU',
		'671' => 'IS',
		'672' => 'IN',
		'673' => 'ID',
		'674' => 'IR',
		'675' => 'IQ',
		'676' => 'IE',
		'595' => 'IM',
		'677' => 'IL',
		'678' => 'IT',
		'679' => 'CI',
		'680' => 'JM',
		'681' => 'JP',
		'594' => 'JE',
		'682' => 'JO',
		'839' => 'KZ',
		'683' => 'KE',
		'660' => 'KI',
		'685' => 'KP',
		'684' => 'KR',
		'686' => 'KW',
		'840' => 'KG',
		'687' => 'LA',
		'832' => 'LV',
		'688' => 'LB',
		'690' => 'LS',
		'691' => 'LR',
		'692' => 'LY',
		'827' => 'LI',
		'833' => 'LT',
		'693' => 'LU',
		'694' => 'MO',
		'852' => 'MK',
		'695' => 'MG',
		'696' => 'MW',
		'698' => 'MY',
		'793' => 'MV',
		'699' => 'ML',
		'700' => 'MT',
		'861' => 'MH',
		'653' => 'MQ',
		'701' => 'MR',
		'702' => 'MU',
		'821' => 'YT',
		'703' => 'MX',
		'862' => 'FM',
		'841' => 'MD',
		'825' => 'MC',
		'704' => 'MN',
		'705' => 'MS',
		'706' => 'MA',
		'707' => 'MZ',
		'798' => 'NA',
		'805' => 'NR',
		'709' => 'NP',
		'710' => 'NL',
		'637' => 'AN',
		'711' => 'NC',
		'714' => 'NZ',
		'715' => 'NI',
		'716' => 'NE',
		'717' => 'NG',
		'714' => 'NU',
		'771' => 'MP',
		'718' => 'NO',
		'782' => 'ZZ',
		'708' => 'OM',
		'721' => 'PK',
		'796' => 'PW',
		'722' => 'PA',
		'712' => 'PG',
		'723' => 'PG',
		'724' => 'PY',
		'725' => 'PE',
		'726' => 'PH',
		'823' => 'PN',
		'727' => 'PL',
		'728' => 'PT',
		'730' => 'PR',
		'731' => 'QA',
		'653' => 'RE',
		'733' => 'RO',
		'842' => 'RU',
		'734' => 'RW',
		'741' => 'WS',
		'826' => 'SM',
		'803' => 'ST',
		'743' => 'SA',
		'785' => 'SN',
		'780' => 'RS', //serbia?
		'744' => 'SC',
		'745' => 'SL',
		'746' => 'SG',
		'850' => 'SK',
		'835' => 'SI',
		'747' => 'SB',
		'748' => 'SO',
		'750' => 'ZA',
		'751' => 'ES',
		'628' => 'LK',
		'735' => 'SH',
		'736' => 'KN',
		'737' => 'LC',
		'653' => 'PM',
		'738' => 'VC',
		'783' => 'AA',
		'752' => 'SD',
		'753' => 'SR',
		'718' => 'SJ',
		'754' => 'SZ',
		'755' => 'SE',
		'756' => 'CH',
		'757' => 'SY',
		'843' => 'TJ',
		'759' => 'TZ',
		'760' => 'TH',
		'762' => 'TG',
		'714' => 'TK',
		'784' => 'TO',
		'763' => 'TT',
		'765' => 'TN',
		'766' => 'TR',
		'844' => 'TM',
		'799' => 'TC',
		'647' => 'TV',
		'767' => 'UG',
		'845' => 'UA',
		'772' => 'XN',
		'764' => 'AE',
		'000' => 'GB',
		'771' => 'US',
		'800' => 'VI',
		'770' => 'UY',
		'846' => 'UZ',
		'713' => 'VU',
		'773' => 'VE',
		'774' => 'VN',
		'822' => 'WF',
		'706' => 'EH',
		'601' => 'YE',
		'781' => 'ZM',
		'732' => 'ZW'
	);
	
	return $countryArray[$countryCode];
}

function cudCardTypeToiPlicitGroup($cudCardType) {
  // accepts any Oxford University card type code (e.g. 'PT') and converts it to one of
  // 'Undergraduate', 'Postgraduate', 'Visiting' or 'Staff' iPlicit codes
  // defaults to 'Customer' if it fails
  
  $undergradCardTypes = array("UG", "PT", );
  $postgradCardTypes = array("GT", "GR");
  $visitingCardTypes = array("VR", "VD", "VV", "VC");
  $staffCardTypes = array("MC", "US", "FS", "FR", "FB", "AV", "DS", "CS", "CL", "CB", "VA", "VX");
  
  if (in_array($cudCardType, $undergradCardTypes)) {
	$iPlicitGroup = "U";
  } elseif (in_array($cudCardType, $postgradCardTypes)) {
	$iPlicitGroup = "G";
  } elseif(in_array($cudCardType, $visitingCardTypes)) {
	$iPlicitGroup = "V";
  } elseif(in_array($cudCardType, $staffCardTypes)) {
	$iPlicitGroup = "Z";
  } else {
	$iPlicitGroup = "CU";
  }
  
  return $iPlicitGroup;
}
?>