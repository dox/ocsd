<?php
include_once("../inc/autoload.php");

$sql  = "SELECT cudid FROM Person WHERE university_card_type IN ('PG', 'GT', 'GR')";
$cudPersons = $db->get($sql);

$i_students = 0;
$iplicit = new iPlicitAPI();

foreach ($cudPersons AS $cudPerson) {
	// Regenerate API token if it's expired
	$tokenTimeRemaining = strtotime($iplicit->tokenDue) - strtotime(gmdate('c'));
	if ($tokenTimeRemaining <= 1) {
		cliOutput("Updating token, as it has expired", "green");
		$iplicit->getSession();
	}
	
	
	$cudPerson = new Person($cudPerson['cudid']);
	
	if (!isset($cudPerson->sits_student_code)) {
		// Missing student ID, so skip this record
		continue;
	}
	
	$i_students++; // count how many students we're processing
	
	$address = $cudPerson->addresses()->getHomeAddress();
	
	$addressParts = [
		$address['Line1'] ?? null,
		$address['Line2'] ?? null,
		$address['Line3'] ?? null,
		$address['Line4'] ?? null,
		$address['Line5'] ?? null,
		$address['City'] ?? null,
		$address['State'] ?? null,
		$address['County'] ?? null
	];
	
	// Filter out empty/null values & Join into a single line
	$filtered = array_filter($addressParts, fn($part) => !empty($part));
	$singleLineAddress = implode(', ', $filtered);
	
	$cudData = [
		 'code' => $cudPerson->sits_student_code,
		 'description' => $cudPerson->FullName,
		 'customer' => [
			 'ext' => [
				 'Activestatus' => $cudPerson->Enrolments()->all()[0]['SCJStatusName'],
				 'SSO' => $cudPerson->sso_username,
				 'Currentyear' => $cudPerson->unit_set_cd,
			 ],
		 ],
		 'contact' => [
			 'intRef' => $cudPerson->sits_student_code,
			 'companyName' => $cudPerson->FullName,
			 'title' => $cudPerson->titl_cd,
			 'firstName' => $cudPerson->firstname,
			 'lastName' => $cudPerson->lastname
		 ]
	];
	
	# Phones
	if (isset($cudPerson->addresses()->getContactAddress()['TelNo'])) {
		$cudData['contact']['phones'][] = [
			'type' => 'H',
			'phone' => $cudPerson->addresses()->getContactAddress()['TelNo'],
		];
	}
	if (isset($cudPerson->addresses()->getContactAddress()['MobileNo'])) {
		$cudData['contact']['phones'][] = [
			'type' => 'M',
			'phone' => $cudPerson->addresses()->getContactAddress()['MobileNo'],
		];
	}
	
	#Emails
	if (isset($cudPerson->oxford_email)) {
		$cudData['contact']['emails'][] = [
			'type' => 'R',
			'email' => $cudPerson->oxford_email,
		];
	}
	if (isset($cudPerson->alt_email)) {
		$cudData['contact']['emails'][] = [
			'type' => 'P',
			'email' => $cudPerson->alt_email,
		];
	}
	
	#Addressess
	if (isset($singleLineAddress)) {
		$cudData['contact']['addresses'][] = [
			'type' => 'R',
			'address' =>  transliterator_transliterate('Any-Latin; Latin-ASCII; [\u0100-\u7fff] remove', $singleLineAddress),
			'postCode' => $address['PostCode'],
			'city' => $address['County'],
			'countryCode' => cudCountryCodeToiPlicitCountyCode($address['AddressCtyCd'])
		];
	}
	
	$iplicitData = $iplicit->getContactAccount($cudPerson->sits_student_code);
	if (isset($iplicitData->id)) {
		// account exists, update it
		
		if (!isset($cudData['contact']['addresses'][0]['address'])) {
			unset($cudData['contact']['addresses'][0]);
		}
		
		//printArray($cudData);
		cliOutput("Updating " . $cudPerson->FullName . " (" . $iplicitData->id . ")", "green");
		$iplicit->updateContactAccount($iplicitData->id, $cudData);
			
	} else {
		// account needs creating
		$iplicit->createContactAccount($cudData);
	}
	
}

$db->upsertByName('cron_iplicit_sync', date('c'));
cliOutput(count($iplicit->updateLog) . " students updated of " .  " CUD persons", "green");
cliOutput(count($iplicit->createLog) . " students created", "green");
if (count($iplicit->errorLog) > 0) {
	cliOutput(count($iplicit->errorLog) . " errors encountered", "red");
	
	$logData = [
		'category' => 'cron',
		'result'   => 'warning',
		'description' => 'Updated ' . count($iplicit->updateLog) . ' iPlicit records with ' . count($iplicit->errorLog) . 'errors'
	];
	$log->create($logData);
} else {
	cliOutput(count($iplicit->errorLog) . " errors encountered", "green");
	
	$logData = [
		'category' => 'cron',
		'result'   => 'success',
		'description' => 'Updated ' . count($iplicit->updateLog) . ' iPlicit records with ' . count($iplicit->errorLog) . 'errors'
	];
	$log->create($logData);
}

if (count($iplicit->errorLog) > 0) {
	$recipients = [
		'to' => [
			'email' => 'andrew.breakspear@seh.ox.ac.uk'
		]
	];
	
	sendMail('iPlicit sync errors', $recipients, implode(', ', $iPlicit->errorLog));
}

class iPlicitAPI {
	public $sessionToken;
	public $tokenDue;

	public array $updateLog = []; 	// array of iPlicit updates
	public array $createLog = []; 	// array of iPlicit creations
	public array $errorLog = []; 	// array of iPlicit errors
	
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
		global $log;
		
		$url = "https://api.iplicit.com/api/ContactAccount/" . $idOrCode;
		
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($contactArray));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers());
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		try {
			$data = json_decode(curl_exec($curl));
			$this->updateLog[] = $idOrCode;
		} catch(Exception $e) {
			$event = "Error updating iPlicit record for " . $contactArray['description'] . " (" . $contactArray['code'] . ") - " . json_encode($data);
			$this->errorLog[] = $event;
			cliOutput($event, "red");
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
		
		try {
			$data = json_decode(curl_exec($curl));
			$this->createLog[] = "complete";
		} catch(Exception $e) {
			$event = "Error creating iPlicit record for " . $contactArray['description'] . " (" . $contactArray['code'] . ") - " . json_encode($data);
			$this->errorLog[] = $event;
			cliOutput($event, "red");
		}
		
		curl_close($curl);
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