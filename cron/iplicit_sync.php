<?php
include_once("../includes/autoload.php");


$personsClass = new Persons();

$allCUDUsers = $personsClass->all();

foreach ($allCUDUsers AS $CUDUser) {
  printArray($CUDUser);
}
?>


// set post fields
$post = [
    'username' => 'user1',
    'password' => 'passuser1',
    'gender'   => 1,
];

$ch = curl_init('http://www.example.com');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

// execute!
$response = curl_exec($ch);

// close the connection, release resources used
curl_close($ch);

// do anything you want with your response
var_dump($response);