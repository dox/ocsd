<?php
include_once("../../includes/autoload.php");

$personObject = new Person($_GET['cudid']);

$sql  = "SELECT * FROM Qualifications";
$sql .= " WHERE cudid = '" . $personObject->cudid . "'";

$dbOutput = $db->query($sql)->fetchAll();
?>


    <table class="table card-table table-vcenter">
      <tbody>
        <?php
        foreach ($dbOutput AS $qual) {
          $id = $qual['QualificationID'];

          $icon = "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\"></path><rect x=\"4\" y=\"5\" width=\"16\" height=\"16\" rx=\"2\"></rect><line x1=\"16\" y1=\"3\" x2=\"16\" y2=\"7\"></line><line x1=\"8\" y1=\"3\" x2=\"8\" y2=\"7\"></line><line x1=\"4\" y1=\"11\" x2=\"20\" y2=\"11\"></line><line x1=\"11\" y1=\"15\" x2=\"12\" y2=\"15\"></line><line x1=\"12\" y1=\"15\" x2=\"12\" y2=\"18\"></line></svg>";

          $awardIcon = "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon icon-md\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\"></path><circle cx=\"12\" cy=\"9\" r=\"6\"></circle><polyline points=\"9 14.2 9 21 12 19 15 21 15 14.2\" transform=\"rotate(-30 12 9)\"></polyline><polyline points=\"9 14.2 9 21 12 19 15 21 15 14.2\" transform=\"rotate(30 12 9)\"></polyline></svg>";

          $bodyIcon = "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon icon-md\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\"></path><line x1=\"3\" y1=\"21\" x2=\"21\" y2=\"21\"></line><path d=\"M4 21v-15a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v15\"></path><path d=\"M9 21v-8a3 3 0 0 1 6 0v8\"></path></svg>";

          $output  = "<tr>";
          $output .= "<td class=\"w-80\">" . $qual['QualDesc'] . "</td>";
          $output .= "<td class=\"text-nowrap text-muted\">" . $icon . " " . $qual['QualCode'] . "</td>";
          $output .= "<td class=\"text-nowrap text-muted\">" . $qual['QualYear'] . "-" . $qual['QualSitting'] . "</td>";
          $output .= "<td>" . $bodyIcon . $qual['AwdBody'] . " " . $qual['AwdBodyDescUCAS'] . "</td>";
          $output .= "<td>" . $awardIcon . $qual['ApprovedResult'] . "</td>";
          $output .= "<td>" . $qual['PredictedResult'] . "/" . $qual['ClaimedResult'] . "</td>";
          $output .= "</tr>";

          echo $output;
        }
        ?>
      </tbody>
    </table>
