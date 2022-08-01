<?php
# ----------------------------------------------- #
#               DEFINE VARIABLES                  #
# ----------------------------------------------- #

include_once("../includes/autoload.php");

$startTime = date('Y-m-d H:i:s');

$i_users = 0; // total users count
$i_created = 0; // total users created count
$i_updated = 0; // total users updated count

$emailOutput = array();

$sessionURL = 'https://api.iplicit.com/api/session/create/api';
$searchContactURL = "https://api.iplicit.com/api/ContactAccount/"; //needs CUDID appended
$createContactURL = 'https://api.iplicit.com/api/ContactAccount';
$updateContactURL = 'https://api.iplicit.com/api/ContactAccount/'; //needs CUDID appended
//$url2 = "https://api.iplicit.com/api/ContactAccount/1199238/contact"; // testing URL to get contact

# ----------------------------------------------- #
#      ESTABLISH SESSION AND SECURE TOKEN         #
# ----------------------------------------------- #
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
 
curl_setopt($curl_session, CURLOPT_URL, $sessionURL);
curl_setopt($curl_session, CURLOPT_POST, TRUE);
curl_setopt($curl_session, CURLOPT_POSTFIELDS, json_encode($sessionFields));
curl_setopt($curl_session, CURLOPT_HTTPHEADER, $sessionHeaders);
curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, TRUE);

$session = json_decode(curl_exec($curl_session));

// get a session token (and expiry) to be used in all following API calls
$sessionToken = $session->sessionToken;
$tokenDue = $session->tokenDue;

debug("Session Token: " . $sessionToken);
debug("Token Due: " . $tokenDue, true);

curl_close($curl_session);

// open new curl to iPlicit API with bearer authentication (sessionToken) from earlier


$headers2 = array(
  'Content-Type:application/json',
  'Domain:' . iplicit_api_domain,
  'Authorization: Bearer ' . $sessionToken
);


# ----------------------------------------------- #
#            SYNC CUD USERS TO iPLICIT            #
# ----------------------------------------------- #
$personsClass = new Persons();
//$allCUDUsers = $personsClass->all();
$allCUDUsers = $personsClass->allStudents();


foreach ($allCUDUsers AS $CUDUser) {
  // only perform lookup on CUD users with a SITS code
  //&& $CUDUser['sits_student_code'] == "1511328"
  if (isset($CUDUser['sits_student_code'])) {
    $i_users++; // count how many students we're processing
    
    $CUDUser = new Person($CUDUser['cudid']);
    
    $sql  = "SELECT * FROM Enrolments WHERE cudid = '" . $CUDUser->cudid . "'";
    $enrolments = $db->query($sql)->fetchAll()[0];
    //printArray($enrolments);
    
    //check if customer already exists
    $curl_check = curl_init();
    $searchContactURLSpecific = $searchContactURL . $CUDUser->sits_student_code;
    curl_setopt($curl_check, CURLOPT_URL, $searchContactURLSpecific);
    curl_setopt($curl_check, CURLOPT_HTTPHEADER, $headers2);
    curl_setopt($curl_check, CURLOPT_RETURNTRANSFER, true );
    debug($searchContactURLSpecific);
    $existingUser = json_decode(curl_exec($curl_check));
    //printArray($existingUser);
    curl_close($curl_check);
    
    
    // build the array for iPlicit API
    $customerUpdateArray = null;
    
    $customerUpdateArray['description'] = $CUDUser->FullName;
    $customerUpdateArray['code'] = $CUDUser->sits_student_code;
    $customerUpdateArray['contact']['intRef'] = $CUDUser->sits_student_code;
    $customerUpdateArray['contact']['title'] = $CUDUser->titl_cd;
    $customerUpdateArray['contact']['firstName'] = $CUDUser->firstname;
    $customerUpdateArray['contact']['middleName'] = $CUDUser->middlenames;
    $customerUpdateArray['contact']['lastName'] = $CUDUser->lastname;
    
    if (isset($CUDUser->oxford_email)) {
      $customerUpdateArray['contact']['emails'][0]['type'] = "R";
      $customerUpdateArray['contact']['emails'][0]['email'] = $CUDUser->oxford_email;
    }
    if (isset($CUDUser->alt_email)) {
      $customerUpdateArray['contact']['emails'][1]['type'] = "P";
      $customerUpdateArray['contact']['emails'][1]['email'] = $CUDUser->alt_email;
    }
    
    $cudAddress = $CUDUser->address('C');
    
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
      
      /* LEAVING THIS OFF UNTIL CUD COUNTRY CODE CAN BE RESOLVED
      $customerUpdateArray['contact']['addresses'][0]['type'] = "R";
      $customerUpdateArray['contact']['addresses'][0]['address'] = $cleanAddress;
      $customerUpdateArray['contact']['addresses'][0]['postcode'] = $cudAddress['PostCode'];
      $customerUpdateArray['contact']['addresses'][0]['city'] = $cudAddress['City'] . " " . $cudAddress['State'];
      $customerUpdateArray['contact']['addresses'][0]['county'] = $cudAddress['County'];
      $customerUpdateArray['contact']['addresses'][0]['countryCode'] = $cudAddress['AddressCtryDesc'];
      $customerUpdateArray['contact']['addresses'][0]['description'] = "Last updated: " . $cudAddress['LastUpdateDt'];
      */
    }
    
    $phonei = 0;
    if (!empty($cudAddress['TelNo'])) {
      $customerUpdateArray['contact']['phones'][$phonei]['type'] = "H";
      $customerUpdateArray['contact']['phones'][$phonei]['phone'] = $cudAddress['TelNo'];
      $phonei++;
    }
    if (!empty($cudAddress['MobileNo'])) {
      $customerUpdateArray['contact']['phones'][$phonei]['type'] = "M";
      $customerUpdateArray['contact']['phones'][$phonei]['phone'] = $cudAddress['MobileNo'];
    }
    
    if (!empty($CUDUser->sso_username)) {
      $customerUpdateArray['customer']['Ext']['SSO'] = $CUDUser->sso_username;
    }
    
    if (!empty($enrolments['SCJStatusName'])) {
      $customerUpdateArray['customer']['Ext']['Activestatus'] = $enrolments['SCJStatusName'];
    }
    
    
    $customerUpdateArray['customer']['contactGroupCustomerId'] = cudCardTypeToiPlicitGroup($CUDUser->university_card_type);
    //printArray($customerUpdateArray);
    
    $curl_updatecreate = curl_init();
    if (isset($existingUser->id)) {
      $i_updated++;
      // user already exists
      
      $updateContactURLSpecific = $updateContactURL . $existingUser->id;
      curl_setopt($curl_updatecreate, CURLOPT_URL, $updateContactURLSpecific);
      curl_setopt($curl_updatecreate, CURLOPT_CUSTOMREQUEST, 'PATCH');
      curl_setopt($curl_updatecreate, CURLOPT_POSTFIELDS, json_encode($customerUpdateArray));
      curl_setopt($curl_updatecreate, CURLOPT_HTTPHEADER, $headers2);
      curl_setopt($curl_updatecreate, CURLOPT_RETURNTRANSFER, true );
      
      $data = json_decode(curl_exec($curl_updatecreate));
      if (isset($data->type)) {
       echo "\033[31m Error updating iPlicit record for " . $CUDUser->FullName . " (" . $CUDUser->sits_student_code . ")\n";
        debug(json_encode($data));
        $logInsert = (new Logs)->insert("cron","error",null,"Error updating iPlicit record for " . $CUDUser->FullName . " (" . $CUDUser->sits_student_code . ") - " . json_encode($data));
      } else {
        echo "\033[32m Updated iPlicit record for " . $CUDUser->FullName . " (" . $CUDUser->sits_student_code . ")\n";
        
        if (debug) {
          $logInsert = (new Logs)->insert("cron","success",null,"Updated iPlicit record for " . $CUDUser->FullName . " (" . $CUDUser->sits_student_code . ")");
        }
      }
      
      //$emailOutput[] = "Updated iPlicit record for " . $CUDUser->FullName . " (" . $CUDUser->sits_student_code . ")";
    } else {
      $i_created++;
      // user needs to be created
      
      $customerUpdateArray['customer']['paymentMethodId'] = "BC";
      
      curl_setopt($curl_updatecreate, CURLOPT_URL, $createContactURL);
      curl_setopt($curl_updatecreate, CURLOPT_POST, TRUE);
      curl_setopt($curl_updatecreate, CURLOPT_POSTFIELDS, json_encode($customerUpdateArray));
      curl_setopt($curl_updatecreate, CURLOPT_HTTPHEADER, $headers2);
      curl_setopt($curl_updatecreate, CURLOPT_RETURNTRANSFER, true );
      
      $data = json_decode(curl_exec($curl_updatecreate));
      if (isset($data->type)) {
       echo "\033[31m Error creating iPlicit record for " . $CUDUser->FullName . " (" . $CUDUser->sits_student_code . ")\n";
        debug(json_encode($data));
        $logInsert = (new Logs)->insert("cron","error",null,"Error creating iPlicit record for " . $CUDUser->FullName . " (" . $CUDUser->sits_student_code . ") - " . json_encode($data));
      } else {
        echo "\033[33m Created iPlicit record for " . $CUDUser->FullName . " (" . $CUDUser->sits_student_code . ")\n";
        $logInsert = (new Logs)->insert("cron","success",null,"Created iPlicit record for " . $CUDUser->FullName . " (" . $CUDUser->sits_student_code . ") - " . json_encode($data));
        
        $emailOutput[] = $CUDUser->FullName . " (" . $CUDUser->sits_student_code . ")";
      }
    }
    curl_close($curl_updatecreate);
    //printArray(json_decode($data));
  }
  
  // user did not have a SITS code
}
curl_close($curl_updatecreate);
debug("", true);

debug($i_users . " students processed of " . count($allCUDUsers) . " CUD users");
debug($i_updated . " students updated");
debug($i_created . " students created", true);


# ----------------------------------------------- #
#           PROCESS EMAIL NOTIFICATION            #
# ----------------------------------------------- #
//email here!
$mail_body  = "<p>iPlicit/CUD sync complete for " . $i_users . autoPluralise(" user ", " users ", $i_users) . "with SITS IDs (of a total of " . count($allCUDUsers) . " CUD users) at " . date('Y-m-d H:i:s') . "</p>";
$mail_body .= "<p>" . $i_updated . autoPluralise(" account was ", " accounts were ", $i_updated) . "updated.</p>";
$mail_body .= "<p>The following " . $i_created . autoPluralise(" account was ", " accounts were ", $i_created) . "created:</p>";
$mail_body .= "<ul>";

foreach ($emailOutput AS $transaction) {
  $mail_body .= "<li>" . $transaction . "</li>";
}
$mail_body . "</ul>";

$mail_subject = "iPlicit/CUD sync";
$mail_recipients = iplicit_api_notifications;

// only email if accounts were created
if ($i_created > 0) {
  sendMail($mail_subject, $mail_recipients, $mail_body);
  debug("Sending email to: " . implode(", ", $mail_recipients));
}
?>
