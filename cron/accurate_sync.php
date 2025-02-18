<?php
include_once("../includes/autoload.php");

// CONNECT TO ACCURATE STAGING TABLE DB
$connectionInfo = array( "Database"=>accurate_db_name, "UID"=>accurate_db_username, "PWD"=>accurate_db_password, "TrustServerCertificate"=>"Yes");
$conn = sqlsrv_connect(accurate_db_host, $connectionInfo);

if($conn) {
	cliOutput("Connection to ACCURATE db established", "white");
	
	$sql = file_get_contents('/home/itsupport/cud-client/out/generated-sql-out.sql');
	$queries = explode(";", $sql); // Split statements by semicolon
	
	foreach ($queries as $query) {
		$query = trim($query);
		if (!empty($query)) {
			$stmt = sqlsrv_query($conn, $query);
			if ($stmt === false) {
				$logInsert = (new Logs)->insert("cron","error",null,"Execution of SQL query failed: " . $query);
				die(print_r(sqlsrv_errors(), true));
			}
		}
	}
	
	$event = "ACCURATE sync complete for " . count($queries) . " sql statements";
	$logInsert = (new Logs)->insert("cron","success",null,$event);
} else {
	cliOutput("Connection to host " . accurate_db_host . " could not be established", "red");
	$logInsert = (new Logs)->insert("cron","error",null,"Connection to host " . accurate_db_host . " could not be established");
	die(printArray(sqlsrv_errors(), true));
}


//printArray($saltoUsers);
?>
