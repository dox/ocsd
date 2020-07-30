<?php
$filename = basename(__FILE__, '.php');

$sql  = "SELECT * FROM " . $filename;
$sql .= " WHERE cudid = '" . $person->cudid . "'";

$dbOutput = $db->query($sql, 'test', 'test')->fetchAll();
?>

<?php if ($dbOutput) {
  $matricDate = date("Y-m-d", strtotime($dbOutput[0]['MatricDt']));
  ?>

<div class="card card-sm">
  <div class="card-body d-flex align-items-center">
    <span class="bg-green text-white stamp mr-3"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><path d="M15 21h-9a3 3 0 0 1 -3 -3v-1h10v2a2 2 0 0 0 4 0v-14a2 2 0 1 1 2 2h-2m2 -4h-11a3 3 0 0 0 -3 3v11"></path><line x1="9" y1="7" x2="13" y2="7"></line><line x1="9" y1="11" x2="13" y2="11"></line></svg></span>
    <div class="mr-3 lh-sm">
      <?php
      if ($dbOutput[0]['Fresher'] == "Y") {
        echo "<span class=\"badge bg-lime float-right\">Fresher</span>";
      }
      ?>
      <div class="strong">Student ID</div>
      <div class="text-muted"><?php echo $dbOutput[0]['Student_Id'] . " (" . $matricDate . ")"; ?></div>
    </div>
  </div>
</div>

<?php } ?>
