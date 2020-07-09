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

$personsClass = new Persons();
$persons = $personsClass->search($keyword, 5);

$personsArray = array();
foreach ($persons as $person) {
	$name = str_replace("'", "", $person['FullName']);
	$value = $name;
	$data = $person['cudid'];

  $personsArray[] = array('value'=>$value, 'data'=>$data);
}
echo "{";
echo "\"query\":\"" . $keyword . "\",";
echo "\"suggestions\":";
echo json_encode($personsArray);
echo "}";
?>
