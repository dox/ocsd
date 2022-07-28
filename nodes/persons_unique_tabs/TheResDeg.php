<?php
include_once("../../includes/autoload.php");

$personObject = new Person($_GET['cudid']);

$sql  = "SELECT * FROM TheResDeg";
$sql .= " WHERE cudid = '" . $personObject->cudid . "'";

$dbOutput = $db->query($sql)->fetchAll();
?>

    <ul>
    <?php
    foreach ($dbOutput AS $output) {
      printArray($output);
      //echo "<li>" . $output['CoOwnDptDesc'] . " (" . $output['Code'] . ")</li>";
    }
    ?>
    </ul>