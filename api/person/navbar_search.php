<?php
include_once("../../includes/autoload.php");

if ($_POST['api_token'] != api_token) {
  echo "API Token mismatch";
  exit;
}

if (isset($_POST['navbar_search'])) {
	$keyword = $_POST['navbar_search'];
} else {
	$keyword = "andrew";
}

$filter = array('api_token' => api_token, 'filter' => 'navsearch', 'searchterm' => $keyword, 'searchlimit' => '5');
$personsJSON = api_decode("person", "read", $filter);
$personsAll = $personsJSON->body;;

$personsArray = array();
foreach ($personsAll as $person) {
	$name = str_replace("'", "", $person->FullName);
	$value = $name;
	$data = $person->cudid;

  $personsArray[] = array('value'=>$value, 'data'=>$data);
  //$personsArray[] = array('value'=>$_POST['api_token'], 'data'=>$_POST['api_token']);
}
echo "{";
echo "\"query\":\"" . $keyword . "\",";
echo "\"suggestions\":";
echo json_encode($personsArray);
echo "}";
?>
