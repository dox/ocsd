<?php
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"images/icons.svg#bell\"/></svg> Test Button", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteMealModal\"");
$icons[] = array("class" => "btn-warning", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"images/icons.svg#email\"/></svg> Test Button", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteMealModal\"");

echo displayTitle("Administrative Settings", "Coming Soon");

?>
<h2 class="m-3">Icons Availabe in <code>./img/icon.svg</code></h2>

<?php
$iconsArray = array(
  "ocsd-logo" => "OCSD Logo (applicaton)",
  "home" => "Home (navigation)",
  "person" => "Person (navigation)",
  "photo" => "Photo (navigation)",
  "ldap" => "",
  "email" => "",
  "logs" => "",
  "signout" => "",
  "bell" => "",
  "geo" => "",
  "telephone" => "",
  "settings" => "Admin. Settings (navigation)"
);



echo "<ul class=\"list-group\">";
foreach ($iconsArray AS $icon => $name) {
  $output  = "<li class=\"list-group-item list-group-item-action\">";
  $output .= "<div class=\"d-flex w-100 justify-content-between\">";
  $output .= "<span><svg width=\"2em\" height=\"2em\" class=\"me-3\">";
  $output .= "<use xlink:href=\"images/icons.svg#" . $icon . "\"/>";
  $output .= "</svg> " . $name . "</span>";
  $output .= "<small>[" . $icon . "]</small>";
  $output .= "</div>";
  $output .= "</li>";

  echo $output;
}

echo "</ul>";
?>
