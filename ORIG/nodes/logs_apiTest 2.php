<?php
$url = "http://" . $_SERVER[HTTP_HOST] . "/api/log/read.php";
$json = file_get_contents($url);
$obj = json_decode($json);
//echo $obj->access_token;

printArray($obj);

?>
