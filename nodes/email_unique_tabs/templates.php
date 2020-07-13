<div class="card">
  <div class="card-header">
    <h3 class="card-title">Templates</h3>
  </div>
  <div class="card-body">
    <div class="list list-row list-hoverable">
      <?php
      foreach ($templatesAll AS $template) {
        $url = "index.php?n=emergency_email&tab=template_edit&uid=" . $template['uid'];

        if ($template['type'] == "email") {
          $icon = "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon icon-md\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\"></path><rect x=\"3\" y=\"5\" width=\"18\" height=\"14\" rx=\"2\"></rect><polyline points=\"3 7 12 13 21 7\"></polyline></svg>";
        } elseif ($template['type'] == "unused") {
          $icon = "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon icon-md\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\"></path><circle cx=\"12\" cy=\"12\" r=\"9\"></circle><path d=\"M10 10l4 4m0 -4l-4 4\"></path></svg>";
        } else {
          $icon = "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon icon-md\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\"></path><circle cx=\"12\" cy=\"12\" r=\"9\"></circle><line x1=\"12\" y1=\"17\" x2=\"12\" y2=\"17.01\"></line><path d=\"M12 13.5a1.5 1.5 0 0 1 1 -1.5a2.6 2.6 0 1 0 -3 -4\"></path></svg>";
        }
        $output  = "<div class=\"list-item\">";
        $output .= "<div><span class=\"badge bg-green\"></span></div>";
        $output .= "<div class=\"text-truncate\">";
        $output .= "<a href=\"" . $url . "\" class=\"text-body d-block\">" . $template['name'] . " (" . $template['subject'] . ")</a>";
        $output .= "<small class=\"d-block text-muted text-truncate mt-n1\">" . $template['description'] . "</small>";
        $output .= "</div>";
        $output .= "<a href=\"" . $url . "\" class=\"list-item-actions show\">" . $icon;
        $output .= "</a>";
        $output .= "</div>";

        echo $output;
      }
      ?>
    </div>
  </div>
</div>
