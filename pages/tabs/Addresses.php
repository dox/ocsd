<?php
include __DIR__ . '/../../inc/autoload.php';

$person = new Person(filter_var($_GET['cudid'], FILTER_SANITIZE_STRING));

$homeAddress = $person->addresses()->getHomeAddress();
$contactAddress = $person->addresses()->getContactAddress();
$termAddress = $person->addresses()->getTermAddress();

echo $person->addresses()->addressCard($homeAddress);
echo $person->addresses()->addressCard($contactAddress);
echo $person->addresses()->addressCard($termAddress);
?>