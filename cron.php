<?php
include_once("includes/autoload.php");

$dir    = 'cron';
$files = scandir($dir);

printArray($files);

foreach ($files AS $file) {
  $extension = end(explode('.', $file));

  if ($extension == "php") {
    echo "<p><strong>Executing " . $file . "</strong></p>";
    include_once($dir . "/" . $file);
  }
}
?>
