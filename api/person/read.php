<?php
header("Content-Type: application/json; charset=UTF-8");
include_once("../../includes/autoload.php");

if ($_POST['api_token'] != api_token) {
  echo "API Token mismatch";
  exit;
}

$personsClass = new Persons();

if (isset($_POST['action'])) {

} else {
  $persons = $personsClass->all();
}
$count = count($persons);

if($count > 0){
    $products = array();
    $products["body"] = array();
    $products["count"] = $count;

    foreach ($persons AS $row) {
      $arrayoutput = null;

      $objectVars = get_object_vars($personsClass);
      foreach ($objectVars AS $key => $value) {
        $arrayoutput[$key] = $row[$key];
      }

      $p = $arrayoutput;

      array_push($products["body"], $p);
    }

    echo json_encode($products);
}

else {

    echo json_encode(array("body" => array(), "count" => 0));
}
?>
