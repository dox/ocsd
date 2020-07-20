<?php
include_once("../includes/autoload.php");

$timeFirst  = strtotime("now");

function _s_has_letters( $string ) {
    return preg_match( '/[a-zA-Z]/', $string );
}

$sqlContents = file_get_contents(cron_import_file);
$sqlContentsArray = preg_split('/;\r\n|;\r|;\n/', $sqlContents);

foreach ($sqlContentsArray AS $sqlCommand) {
	if (strpos($sqlCommand, ';') !== true AND _s_has_letters($sqlCommand)) {
    $sqlContentsQuery = $db->query($sqlCommand);
	}
}

$personsClass = new Persons();
$studentsAllCount = count($personsClass->allStudents());
$personsAllCount = count($personsClass->all());


$sql = "INSERT INTO _stats (name, value) VALUES ('student_rows_total', '" . $studentsAllCount . "')";
$insert = $db->query($sql);
$sql = "INSERT INTO _stats (name, value) VALUES ('person_rows_total', '" . $personsAllCount . "')";
$insert = $db->query($sql);

$timeSecond = strtotime("now");
$differenceInSeconds = $timeSecond - $timeFirst;

$logInsert = (new Logs)->insert("cron","success",null,"<code>cron.php</code> run successfully in " . $differenceInSeconds . " seconds");
?>
