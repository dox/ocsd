<?php
$filename = basename(__FILE__, '.php');

$sql  = "SELECT * FROM " . $filename;
$sql .= " WHERE cudid = '" . $person->cudid . "'";

$dbOutput = $db->query($sql, 'test', 'test')->fetchAll();
?>

<?php if ($dbOutput) { ?>



<div class="card">
  <div class="card-header">
    <h4 class="card-title">Years of Award Program</h4>
  </div>
  <div class="table-responsive">
    <table class="table card-table table-vcenter">
      <tbody>
        <?php
        /*
           [Code] => 633505/1
           [SCEStatusUpdateDt] => 20140703
           [DeptCd] => 3C07CU
           [FeeStatusCd] => O
           [DivCd] => 3C
           [UnitSetCd] => 1
           [Block] => 1
           FundingType
           YearOfStudy
           SCEStatusCd
           FeeDueDt
           SCESequence
        */
        foreach ($dbOutput AS $qual) {
          $bodyIcon = "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon icon-md\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\"></path><line x1=\"3\" y1=\"21\" x2=\"21\" y2=\"21\"></line><path d=\"M4 21v-15a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v15\"></path><path d=\"M9 21v-8a3 3 0 0 1 6 0v8\"></path></svg>";

          if ($qual['FeesDesc'] == "Home") {
            $FeesDescIcon = "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon icon-md\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\"></path><polyline points=\"5 12 3 12 12 3 21 12 19 12\"></polyline><path d=\"M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7\"></path><path d=\"M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6\"></path></svg>";
          } elseif ($qual['FeesDesc'] == "Overseas") {
            $FeesDescIcon = "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon icon-md\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\"></path><circle cx=\"12\" cy=\"12\" r=\"9\"></circle><line x1=\"3.6\" y1=\"9\" x2=\"20.4\" y2=\"9\"></line><line x1=\"3.6\" y1=\"15\" x2=\"20.4\" y2=\"15\"></line><path d=\"M11.5 3a17 17 0 0 0 0 18\"></path><path d=\"M12.5 3a17 17 0 0 1 0 18\"></path></svg>";
          } elseif ($qual['FeesDesc'] == "European Union") {
            $FeesDescIcon = "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon icon-md\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\"></path><path d=\"M17.2 7a6 7 0 1 0 0 10\"></path><path d=\"M13 10h-8m0 4h8\"></path></svg>";
          } else {
            $FeesDescIcon = "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon icon-md\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\"></path><line x1=\"18\" y1=\"6\" x2=\"18\" y2=\"6.01\"></line><path d=\"M18 13l-3.5 -5a4 4 0 1 1 7 0l-3.5 5\"></path><polyline points=\"10.5 4.75 9 4 3 7 3 20 9 17 15 20 21 17 21 15\"></polyline><line x1=\"9\" y1=\"4\" x2=\"9\" y2=\"17\"></line><line x1=\"15\" y1=\"15\" x2=\"15\" y2=\"20\"></line></svg>";
          }

          $output  = "<tr>";
          $output .= "<td class=\"w-80\">" . $bodyIcon . $qual['DivDesc'] . " - " . $qual['DeptName'] . "</td>";
          $output .= "<td class=\"text-nowrap text-muted\">" . $qual['AcademicYear'] . " (" . $qual['Occur'] . ")</td>";
          $output .= "<td>" . $FeesDescIcon . $qual['AttendModeDesc'] . "</td>";
          $output .= "<td>" . $qual['SCEStatusDesc'] . "</td>";
          $output .= "<td>" . $qual['ExternalLocDesc'] . "</td>";
          $output .= "<td>£" . $qual['TuitionFeeValue'] . "</td>";
          $output .= "<td>" . "</td>";
          $output .= "</tr>";

          echo $output;
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<?php } ?>
