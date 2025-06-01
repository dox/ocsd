<?php
include_once("../inc/autoload.php");

// CONNECT TO ACCURATE STAGING TABLE DB
$connectionInfo = array( "Database"=>accurate_db_name, "UID"=>accurate_db_username, "PWD"=>accurate_db_password, "TrustServerCertificate"=>"Yes");
$conn = sqlsrv_connect(accurate_db_host, $connectionInfo);

if($conn) {
	cliOutput("Connection to Accurate db established", "white");
	
	$sql = file_get_contents(accurate_cud_file);
	$queries = explode(";", $sql); // Split statements by semicolon
	printArray($queries);
	
	foreach ($queries as $query) {
		$query = trim($query);
		if (!empty($query)) {
			$stmt = sqlsrv_query($conn, $query);
			if ($stmt === false) {
				$logData = [
					'category' => 'cron',
					'result'   => 'error',
					'description' => 'Execution of SQL query failed: ' . $query
				];
				$log->create($logData);
				die(print_r(sqlsrv_errors(), true));
			}
		}
	}
	
	$db->upsertByName('cron_accurate_sync', date('c'));
	
	$event = "Accurate sync complete for " . count($queries) . " sql statements";
	$logData = [
		'category' => 'cron',
		'result'   => 'success',
		'description' => $event
	];
	$log->create($logData);
} else {
	cliOutput("Connection to host " . accurate_db_host . " could not be established", "red");
	$logData = [
		'category' => 'cron',
		'result'   => 'error',
		'description' => 'Connection to host ' . accurate_db_host . ' could not be established'
	];
	$log->create($logData);
	die(printArray(sqlsrv_errors(), true));
}
?>
