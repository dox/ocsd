<?php
include __DIR__ . '/../../inc/autoload.php';
requireLogin();

$person = new Person(trim((string)($_GET['cudid'] ?? '')));

$homeAddress = $person->addresses()->getHomeAddress();
$contactAddress = $person->addresses()->getContactAddress();
$termAddress = $person->addresses()->getTermAddress();

//printArray($homeAddress);
//printArray($contactAddress);
//printArray($termAddress);

echo $person->addresses()->addressCard($homeAddress);
echo $person->addresses()->addressCard($contactAddress);
echo $person->addresses()->addressCard($termAddress);
?>
