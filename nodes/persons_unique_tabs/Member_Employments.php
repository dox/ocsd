<?php
$filename = basename(__FILE__, '.php');

$dbOutput = $db->where("cudid", $_GET['cudid']);
$dbOutput = $db->getOne($filename);
?>

<h2><?php echo ucwords($filename);?>:</h2>
<?php
if (count($dbOutput) > 0) {
  echo "<pre>";
  printArray($dbOutput);
  echo "</pre>";

  $includeFile = true;
} else {
  $includeFile = false;
}
?>
