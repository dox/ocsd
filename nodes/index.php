<?php

$logsClass = new Logs();

$totalPersons = $logsClass->totalPersons();

echo "Total Persons:";
printArray($totalPersons);
?>