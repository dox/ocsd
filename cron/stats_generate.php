<?php
include_once("../includes/autoload.php");

$personsClass = new Persons();
$allPersons = $personsClass->all();
$allStudents = $personsClass->allStudents();


echo "\033[32m Creating stat for person_rows_total = '" . count($allPersons) . "'". ")\n";
$sql  = "INSERT INTO _stats (name, value) VALUES ('person_rows_total', '" . count($allPersons) . "');";
$db->query($sql);

echo "\033[32m Creating stat for student_rows_total = '" . count($allStudents) . "'". ")\n";
$sql  = "INSERT INTO _stats (name, value) VALUES ('student_rows_total', '" . count($allStudents) . "');";
$db->query($sql);



?>