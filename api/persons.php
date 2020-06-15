<?php
include_once("../includes/autoload.php");

if (isset($_POST['navbar_search'])) {
	$keyword = $_POST['navbar_search'];
} else {
	$keyword = "andrew";
}

$persons = new Persons();
$personsAll = $persons->search($keyword, 5);

$personsArray = array();
foreach ($personsAll as $person) {
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
