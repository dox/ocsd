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
	
	// clear addresses of all types
	$mail->clearAllRecipients();

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
	}
	
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
		if (is_array($message) || is_object($message)) {
			printArray($message);
		} else {
			echo "<code>" . $message . "</code><br />";
		}
		
		
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
		'609' => 'CX',
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
		'609' => 'NF',
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
		'780' => 'QN',
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
		'678' => 'VA',
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

function cliOutput($message = null, $colour = null) {
	if ($colour == "black") {
		$colour = "30m";
	} elseif ($colour == "red") {
		$colour = "31m";
	} elseif ($colour == "green") {
		$colour = "32m";
	} elseif ($colour == "yellow") {
		$colour = "33m";
	} elseif ($colour == "blue") {
		$colour = "34m";
	} elseif ($colour == "magenta") {
		$colour = "35m";
	} elseif ($colour == "cyan") {
		$colour = "36m";
	} elseif ($colour == "white") {
		$colour = "97m";
	} else {
		$colour = "39m";
	}
	
	$message = "\033[" . $colour . $message . "\n";
	
	echo $message;
}
?>