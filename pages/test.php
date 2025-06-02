<?php
$data = array(
		'icon'		=> 'moon',
		'title'		=> 'Test Page',
		'subtitle'	=> 'This page is not meant for normal use'
);
echo pageTitle($data);

$recipients = [
	'to' => [
		'email' => ''
	]
];
sendMail('Test', $recipients, 'Test message');

//$db->upsertByName('example', '42');
	
?>