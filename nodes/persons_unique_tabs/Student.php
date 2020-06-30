<?php
$filename = basename(__FILE__, '.php');

$dbOutput = $db->where("cudid", $_GET['cudid']);
$dbOutput = $db->getOne($filename);
?>

<h2><?php echo ucwords($filename);?>:</h2>
<?php
if (count($dbOutput) > 0) {
  echo "<table class=\"table\">";
  echo "<thead>";
  echo "<tr>";
  echo "<th scope=\"col\">Key</th>";
  echo "<th scope=\"col\">Value</th>";
  echo "</tr>";
  echo "</thead>";
  echo "<tbody>";

  foreach ($dbOutput as $key => $value) {
      if (isset($value)) {
        $output  = "<tr>";
        $output  .= "<td>" . $key . "</td>";
        $output .= "<td>" . $value . "</td>";
        $output .= "</tr>";

        echo $output;
      }
    }

  echo "</tbody>";
  echo "</table>";

  $includeFile = true;
} else {
  $includeFile = false;
}
?>
