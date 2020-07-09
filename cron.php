<?php
include_once("includes/autoload.php");

$dir    = 'cron';
$files = scandir($dir);

echo "<h2>File Directory:</h2>";
printArray($files);

foreach ($files AS $file) {
  $extension = end(explode('.', $file));

  if ($extension == "php") {
    echo "<h3><strong>Executing " . $file . "</strong></h3>";
    include_once($dir . "/" . $file);
  }
}
?>
