<?php
function printArray($array) {
	echo ("<pre>");
	print_r ($array);
	echo ("</pre>");
}

function win_time_to_unix_time($win_time) {
	//round the win timestamp down to seconds and remove the seconds between 1601-01-01 and 1970-01-01
	$unix_time = round($win_time / 10000000) - 11644477200;

	return $unix_time;
}

function convertToDateString($dateString, $time = false) {
	if ($time == "true") {
		$dateFormat = "d F Y H:i:s";
	} else {
		$dateFormat = "d F Y";
	}

	if ($dateString == "") {
		$dateString = date('Y-m-d H:i:s');
	}

	$date = strtotime($dateString);
	$returnDate = date($dateFormat, $date);

	return $returnDate;
}


function age($date = NULL) {
	if ($birthDate == NULL) {
		$birthDate = date('Y-m-d');
	}

	return intval(substr(date('Ymd') - date('Ymd', strtotime($date)), 0, -4));
}

function autoPluralise ($singular, $plural, $count = 1) {
	// fantasticly clever function to return the correct plural of a word/count combo
	// Usage:	$singular	= single version of the word (e.g. 'Bus')
	//       	$plural 	= plural version of the word (e.g. 'Busses')
	//			$count		= the number you wish to work out the plural from (e.g. 2)
	// Return:	the singular or plural word, based on the count (e.g. 'Jobs')
	// Example:	autoPluralise("Bus", "Busses", 3)  -  would return "Busses"
	//			autoPluralise("Bus", "Busses", 1)  -  would return "Bus"

	return ($count == 1)? $singular : $plural;
} // END function autoPluralise

function makeEmail($address) {
	$output  = "<a href=\"mailto:" . $address . "\">" . $address . "</a>";
	return $output;
}

function sendMail($subject = "No Subject Specified", $recipients = NULL, $body = NULL, $senderAddress = NULL, $senderName = NULL) {
	global $mail;

	$mail->IsSMTP();
	$mail->Host = smtp_server;
	$mail->IsHTML(true);

	if (isset($senderAddress)) {
		$mail->From = $senderAddress;
		$mail->FromName = $senderName;
		$mail->AddReplyTo($senderAddress, $senderName);
	} else {
		$mail->From = "noreply@seh.ox.ac.uk";
		$mail->FromName = "No Reply ";
		//$mail->AddReplyTo("communications@seh.ox.ac.uk", "SEH Communications");

	}

	//$recipients = explode(",", $recipients);

	//echo $recipients;

	//$mail->AddAddress("noreply@seh.ox.ac.uk");
	foreach ($recipients AS $recipient) {
		$mail->addBCC($recipient);
	}




	$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
	//$mail->AddAttachment('/var/tmp/file.tar.gz');         // Add attachments
	//$mail->AddAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
	$mail->IsHTML(true);                                  // Set email format to HTML

	$mail->Subject = $subject;
	$mail->Body    = $body;
	//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

	if($mail->Send()) {
		$logInsert = (new Logs)->insert("email","success",null,"Email sent to " . implode(", ",$recipients) . ". Subject: " . $subject);
	} else {
		$logInsert = (new Logs)->insert("email","error",null,"Email could not be sent to " . implode(", ",$recipients) . " <code>" . $mail->ErrorInfo . "</code>");
		echo 'Message could not be sent.';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
		exit;
	}

	//echo 'Message has been sent';
}

function isingroup($groupname) {
	$username = $_SESSION['username'];
	$usergroups = $_SESSION['userinfo'][0]['memberof'];

	//clean the LDAP group names up
	foreach ($usergroups AS $group) {
		if (strlen($group) > 1) {
			$firstcomma = strpos($group, ",");
			$firstCN = strpos($group, "CN=");
			$name = substr($group, $firstCN + 3, $firstcomma - 3);

			$groupArray[] = $name;
		}
	}

	// check each LDAP group this user is a member of against $groupnamecheck
	foreach ($groupArray AS $group) {
		if ($groupname == $group) {
			return true;
			break;
		}
	}
}

function generateRandomString($string = null) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';

	$stringLengthMin = floor(strlen($string)/0.5);
	$stringLengthMax = ceil(strlen($string)*0.5);
	$stringLength = rand($stringLengthMin,$stringLengthMax);

	for ($i = 0; $i < $stringLength; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

function gatekeeper($groupnamecheck = "All Staff") {
	if (isingroup($groupnamecheck)) {
	} else {
		echo "SECURITY ACCESS DENIED";
		exit;
	}
}

function curPageURL() {
	$pageURL = 'http';

	if ($_SERVER["HTTPS"] == "on") {
		$pageURL .= "s";
	}

	$pageURL .= "://";

	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}

	return $pageURL;
}

function api_decode($category = null, $endpoint = null, $filter = null, $username = null, $password = null) {
	$url = site_url . "/api/" . $category . "/" . $endpoint . ".php";

	$data2 = array(
		'api_token' => api_token
	);

	$postdata = http_build_query($filter);

	$opts = array('http' =>
		array(
			'method'  => 'POST',
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'content' => $postdata
		)
	);

	if($username && $password) {
		$opts['http']['header'] .= ("Authorization: Basic " . base64_encode("$username:$password")); // .= to append to the header array element
	}

	$context = stream_context_create($opts);

	return json_decode(file_get_contents($url, false, $context));
}

function howLongAgo($strPastDate = null) {
	$diff = time() - ((int) $strPastDate);

	if ($diff < 0) {
		return FALSE;
	} else if ($diff < 60) {
		return ("just now");
	} else if ($diff < 3600) {
		// minutes ago
		$diff = round($diff / 60);
		if ($diff == 0) {
			$diff = 1;
		}
		$diff = $diff . (autoPluralise (" minute", " minutes", $diff)) . " ago";

		return ($diff);
	} else if ($diff < 86400) {
		// hours ago
		$diff = round($diff / 3600);
		if ($diff == 0) {
			$diff = 1;
		}
		$diff = $diff . (autoPluralise (" hour", " hours", $diff)) . " ago";

		return ($diff);
	} else if ($diff < 2592000) {
		// days ago
		$diff = round($diff / 86400);
		if ($diff == 0 | $diff == 1) {
			$diff = ("yesterday");
			return $diff;
		}
		$diff = $diff . (autoPluralise (" day", " days", $diff)) . " ago";
		return ($diff);
	} else if ($diff < 31536000) {
		//months ago
		$diff = round($diff / 2592000);
		$diff = $diff . (autoPluralise (" month", " months", $diff)) . " ago";
		return ($diff);
	} else {
		// years ago
		$diff = round($diff / 31536000);
		$diff = $diff . (autoPluralise (" year", " years", $diff)) . " ago";
		return ($diff);
	}
}

function displayTitle($title = null, $subtitle = nulll, $iconsArray = null) {
	$output  = "<div class=\"page-header mt-3 mb-3\">";
	$output .= "<div class=\"row align-items-center\">";
	$output .= "<div class=\"col\">";
	if ($subtitle != null) {
		$output .= "<div class=\"page-pretitle\">" . $subtitle . "</div>";
	}
	if ($title != null) {
		$output .= "<h2 class=\"page-title\" role=\"heading\" aria-level=\"1\">" . $title . "</h2>";
	}
	$output .= "</div>";

	// Page title actions
	$output .= "<div class=\"col-auto ml-auto d-print-none\">";

	foreach ($iconsArray AS $icon) {
		$output .= "<button type=\"button\" class=\"btn btn-sm ms-1 " . $icon['class'] . "\"" . $icon['value'] . ">";
		$output .= $icon['name'];
		$output .= "</button>";
	}

	$output .= "</div>";
	$output .= "</div>";
	$output .= "</div>";

	return $output;
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

function debug($message, $linebreak = false) {
	if (debug == true) {
		echo "<code>" . $message . "</code><br />";
		
		if ($linebreak == true) {
			echo "<br />\n";
		}
		echo "\n";
	}
}

function w32timeToTime($inputTime = null) {
  $winSecs = (int)($inputTime / 10000000); // divide by 10 000 000 to get seconds
  $unixTimestamp = ($winSecs - 11644473600); // 1.1.1600 -> 1.1.1970 difference in seconds

  return ($unixTimestamp);
}

function cudCountryCodeToiPlicitCountyCode($countryCode) {
	
	$countryArray = array(
	'004' => 'AF',
	'248' => 'AX',
	'008' => 'AL',
	'012' => 'DZ',
	'016' => 'AS',
	'020' => 'AD',
	'024' => 'AO',
	'660' => 'AI',
	'028' => 'AG',
	'032' => 'AR',
	'051' => 'AM',
	'533' => 'AW',
	'036' => 'AU',
	'010' => 'AQ',
	'040' => 'AT',
	'031' => 'AZ',
	'044' => 'BS',
	'048' => 'BH',
	'581' => 'UM',
	'050' => 'BD',
	'052' => 'BB',
	'112' => 'BY',
	'056' => 'BE',
	'084' => 'BZ',
	'204' => 'BJ',
	'060' => 'BM',
	'064' => 'BT',
	'068' => 'BO',
	'070' => 'BA',
	'072' => 'BW',
	'074' => 'BV',
	'076' => 'BR',
	'086' => 'IO',
	'092' => 'VG',
	'096' => 'BN',
	'100' => 'BG',
	'854' => 'BF',
	'108' => 'BI',
	'116' => 'KH',
	'120' => 'CM',
	'124' => 'CA',
	'132' => 'CV',
	'136' => 'KY',
	'140' => 'CF',
	'148' => 'TD',
	'152' => 'CL',
	'156' => 'CN',
	'158' => 'TW',
	'162' => 'CX',
	'166' => 'CC',
	'170' => 'CO',
	'174' => 'KM',
	'178' => 'CG',
	'180' => 'CD',
	'184' => 'CK',
	'188' => 'CR',
	'384' => 'CI',
	'191' => 'HR',
	'192' => 'CU',
	'196' => 'CY',
	'203' => 'CZ',
	'208' => 'DK',
	'262' => 'DJ',
	'212' => 'DM',
	'214' => 'DO',
	'218' => 'EC',
	'818' => 'EG',
	'222' => 'SV',
	'226' => 'GQ',
	'232' => 'ER',
	'233' => 'EE',
	'231' => 'ET',
	'238' => 'FK',
	'234' => 'FO',
	'242' => 'FJ',
	'246' => 'FI',
	'250' => 'FR',
	'254' => 'GF',
	'258' => 'PF',
	'260' => 'TF',
	'266' => 'GA',
	'270' => 'GM',
	'268' => 'GE',
	'276' => 'DE',
	'288' => 'GH',
	'292' => 'GI',
	'300' => 'GR',
	'304' => 'GL',
	'308' => 'GD',
	'312' => 'GP',
	'316' => 'GU',
	'320' => 'GT',
	'831' => 'GG',
	'324' => 'GN',
	'624' => 'GW',
	'328' => 'GY',
	'332' => 'HT',
	'334' => 'HM',
	'340' => 'HN',
	'344' => 'HK',
	'348' => 'HU',
	'352' => 'IS',
	'356' => 'IN',
	'360' => 'ID',
	'364' => 'IR',
	'368' => 'IQ',
	'372' => 'IE',
	'833' => 'IM',
	'376' => 'IL',
	'380' => 'IT',
	'388' => 'JM',
	'392' => 'JP',
	'832' => 'JE',
	'400' => 'JO',
	'398' => 'KZ',
	'404' => 'KE',
	'296' => 'KI',
	'408' => 'KP',
	'410' => 'KR',
	'926' => 'XK',
	'414' => 'KW',
	'417' => 'KG',
	'418' => 'LA',
	'428' => 'LV',
	'422' => 'LB',
	'426' => 'LS',
	'430' => 'LR',
	'434' => 'LY',
	'438' => 'LI',
	'440' => 'LT',
	'442' => 'LU',
	'446' => 'MO',
	'807' => 'MK',
	'450' => 'MG',
	'454' => 'MW',
	'458' => 'MY',
	'462' => 'MV',
	'466' => 'ML',
	'470' => 'MT',
	'584' => 'MH',
	'474' => 'MQ',
	'478' => 'MR',
	'480' => 'MU',
	'175' => 'YT',
	'484' => 'MX',
	'583' => 'FM',
	'498' => 'MD',
	'492' => 'MC',
	'496' => 'MN',
	'499' => 'ME',
	'500' => 'MS',
	'504' => 'MA',
	'508' => 'MZ',
	'104' => 'MM',
	'516' => 'NA',
	'520' => 'NR',
	'524' => 'NP',
	'528' => 'NL',
	'540' => 'NC',
	'554' => 'NZ',
	'558' => 'NI',
	'562' => 'NE',
	'566' => 'NG',
	'570' => 'NU',
	'574' => 'NF',
	'580' => 'MP',
	'578' => 'NO',
	'512' => 'OM',
	'586' => 'PK',
	'585' => 'PW',
	'591' => 'PA',
	'598' => 'PG',
	'600' => 'PY',
	'604' => 'PE',
	'608' => 'PH',
	'612' => 'PN',
	'616' => 'PL',
	'620' => 'PT',
	'630' => 'PR',
	'634' => 'QA',
	'638' => 'RE',
	'642' => 'RO',
	'643' => 'RU',
	'646' => 'RW',
	'654' => 'SH',
	'659' => 'KN',
	'662' => 'LC',
	'666' => 'PM',
	'670' => 'VC',
	'882' => 'WS',
	'674' => 'SM',
	'678' => 'ST',
	'682' => 'SA',
	'686' => 'SN',
	'688' => 'RS',
	'690' => 'SC',
	'694' => 'SL',
	'702' => 'SG',
	'703' => 'SK',
	'705' => 'SI',
	'090' => 'SB',
	'706' => 'SO',
	'710' => 'ZA',
	'239' => 'GS',
	'728' => 'SS',
	'724' => 'ES',
	'144' => 'LK',
	'729' => 'SD',
	'740' => 'SR',
	'744' => 'SJ',
	'748' => 'SZ',
	'752' => 'SE',
	'756' => 'CH',
	'760' => 'SY',
	'762' => 'TJ',
	'834' => 'TZ',
	'764' => 'TH',
	'626' => 'TL',
	'768' => 'TG',
	'772' => 'TK',
	'776' => 'TO',
	'780' => 'TT',
	'788' => 'TN',
	'792' => 'TR',
	'795' => 'TM',
	'796' => 'TC',
	'798' => 'TV',
	'850' => 'VI',
	'800' => 'UG',
	'804' => 'UA',
	'784' => 'AE',
	'826' => 'GB',
	'840' => 'US',
	'858' => 'UY',
	'860' => 'UZ',
	'548' => 'VU',
	'336' => 'VA',
	'862' => 'VE',
	'704' => 'VN',
	'876' => 'WF',
	'887' => 'YE',
	'894' => 'ZM',
	'716' => 'ZW',
	'000' => 'GB');
	
	return $countryArray[$countryCode];
}
?>