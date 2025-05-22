<?php
include_once("../inc/autoload.php");

$sql_persons  = "SELECT cudid FROM Person";
$allPersons = $db->query($sql_persons);

$sql_students  = "SELECT cudid FROM Person WHERE university_card_type IN ('PG', 'GT', 'GR')";
$allStudents = $db->query($sql_students);


$sql  = "INSERT INTO _stats (name, value) VALUES ('person_rows_total', '" . count($allPersons) . "');";
$db->query($sql);

$sql  = "INSERT INTO _stats (name, value) VALUES ('student_rows_total', '" . count($allStudents) . "');";
$db->query($sql);
?>