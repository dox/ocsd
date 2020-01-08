<?php
$timeFirst  = strtotime("now");

function _s_has_letters( $string ) {
    return preg_match( '/[a-zA-Z]/', $string );
}

require_once('config.php');
require_once('database/MysqliDb.php');
$db = new MysqliDb ($db_host, $db_username, $db_password, $db_name);

$sqlContents = file_get_contents(IMPORT_FILE);
$sqlContentsArray = preg_split('/;\r\n|;\r|;\n/', $sqlContents);

foreach ($sqlContentsArray AS $sqlCommand) {
	if (strpos($sqlCommand, ';') !== true AND _s_has_letters($sqlCommand)) {
		$sqlContentsQuery = $db->rawQuery($sqlCommand);
		//echo $sqlCommand . "<br />";
	}
}


$studentsAll = $db->get ("Student");
$studentsAllCount = $db->count;

$personsAll = $db->get ("Person");
$personsAllCount = $db->count;

//$stats = $db->get("_stats");
$stats_student_rows_total = Array ("name" => "student_rows_total", "value" => $studentsAllCount);
$id = $db->insert ('_stats', $stats_student_rows_total);

$stats_person_rows_total = Array ("name" => "person_rows_total", "value" => $personsAllCount);
$id = $db->insert ('_stats', $stats_person_rows_total);


$timeSecond = strtotime("now");
$differenceInSeconds = $timeSecond - $timeFirst;

$logSQLInsert = Array ("type" => "CRON", "description" => "'cron.php' task run successfully (" . $differenceInSeconds . " seconds)");
print_r($logSQLInsert);
$id = $db->insert ('_logs', $logSQLInsert);

?>
