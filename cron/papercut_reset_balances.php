<?php
include_once("../inc/autoload.php");

$serverUrl = papercut_url;
$authToken = papercut_api;

function callPaperCutApi($methodName, $params = []) {
	global $serverUrl, $authToken;

	// Start XML request
	$xml = new SimpleXMLElement('<methodCall></methodCall>');
	$xml->addChild('methodName', $methodName);

	$paramsNode = $xml->addChild('params');

	// Always add the auth token as the first param
	$paramNode = $paramsNode->addChild('param');
	$valueNode = $paramNode->addChild('value');
	$valueNode->addChild('string', $authToken);

	// Add other parameters
	foreach ($params as $paramValue) {
		$paramNode = $paramsNode->addChild('param');
		$valueNode = $paramNode->addChild('value');

		if (is_int($paramValue)) {
			$valueNode->addChild('int', $paramValue);
		} elseif (is_float($paramValue)) {
			$valueNode->addChild('double', $paramValue);
		} else {
			$valueNode->addChild('string', $paramValue);
		}
	}

	$requestXml = $xml->asXML();

	$ch = curl_init($serverUrl);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $requestXml);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Content-Type: text/xml'
	]);

	$response = curl_exec($ch);

	if (curl_errno($ch)) {
		echo "cURL Error: " . curl_error($ch) . "\n";
		exit;
	}

	curl_close($ch);

	$xmlResponse = simplexml_load_string($response);

	// Check for fault
	if (isset($xmlResponse->fault)) {
		$fault = $xmlResponse->fault->value->struct->member;

		$faultCode = null;
		$faultString = null;

		foreach ($fault as $member) {
			if ((string)$member->name === 'faultCode') {
				$faultCode = (string)$member->value->int;
			}
			if ((string)$member->name === 'faultString') {
				$faultString = (string)$member->value->string;
			}
		}

		echo "PaperCut API Fault:\n";
		echo "Fault Code: $faultCode\n";
		echo "Fault String: $faultString\n";
		exit;
	}

	// Extract the returned value
	$returnNode = $xmlResponse->params->param->value;

	// Sometimes the API returns an array, sometimes a string/double/boolean
	if (isset($returnNode->array)) {
		$data = [];
		foreach ($returnNode->array->data->value as $val) {
			$data[] = (string)$val;
		}
		return $data;
	}
	
	if (isset($returnNode->double)) {
		return (float)$returnNode->double;
	}
	
	if (isset($returnNode->boolean)) {
		// XML-RPC boolean is 0 or 1
		return ((string)$returnNode->boolean === '1');
	}
	
	if (isset($returnNode->string)) {
		return (string)$returnNode->string;
	}

	return null;
}

$users = callPaperCutApi('api.getGroupMembers', [papercut_group, 0, 2000]);

$ignoredUsersCount = 0;
$adjustedUsersCount = 0;
$adjustedUsersTotal = 0;

foreach ($users AS $username) {
	$userBalence = callPaperCutApi('api.getUserAccountBalance', [$username]);
	
	if ($userBalence < 0) {
		echo $username . " = " . $userBalence . "<br >";
		
		// SET BALANCE HERE!
		$adjustmentComment = "Balance transfered to Batels";
		//$sharedAccounts = callPaperCutApi('api.listSharedAccounts', [0,100]);
		$setUserBalance = callPaperCutApi('api.setUserAccountBalance', [$username, 0.00, $adjustmentComment]);
		
		$logData = [
			'category' => 'cron',
			'result'   => 'success',
			'description' => 'Printing balance for ' . $username . ' reset from £' . $userBalence . ' to £0'
		];
		$log->create($logData);
		
		$adjustedUsersCount ++;
		$adjustedUsersTotal += $userBalence;
	} else {
		$ignoredUsersCount ++;
	}
}

echo "<hr >";
echo "<p>Ignored " . $ignoredUsersCount . " users</p>";
echo "<p>Adjusted " . $adjustedUsersCount . " users</p>";
echo "<p>Total adjustement £" . $adjustedUsersTotal . "</p>";
