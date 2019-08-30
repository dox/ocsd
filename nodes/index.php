<?php
$students = $db->get ("Student");
$studentsCount = $db->count;
$persons = $db->get ("Person");
$personsCount = $db->count;
?>

<div class="bls">
	<div class="blt">
		<h6 class="blv"><a href="index.php">OCSD</a> / </h6>
		<h2 class="blu">Overview</h2>
	</div>
</div>

<!--
<hr class="aah">

<div class="dh ard aav">
	<div class="eo aaq ahj">
		<div class="atv acw">
			<canvas
			class="bkx"
			width="200" height="200"
			data-chart="doughnut"
			data-dataset="[230, 130]"
			data-dataset-options="{ backgroundColor: ['#1ca8dd', '#1bc98e'] }"
			data-labels="['Returning', 'New']">
			</canvas>
		</div>
		
		<strong class="asd">Traffic</strong>
		<h4>New vs Returning</h4>
	</div>
	
	<div class="eo aaq ahj">
		<div class="atv acw">
			<canvas
			class="bkx"
			width="200" height="200"
			data-chart="doughnut"
			data-dataset="[330, 30]"
			data-dataset-options="{ backgroundColor: ['#1ca8dd', '#1bc98e'] }"
			data-labels="['Returning', 'New']">
			</canvas>
		</div>
		<strong class="asd">Revenue</strong>
		<h4>New vs Recurring</h4>
	</div>
	
	<div class="eo aaq ahj">
		<div class="atv acw">
			<canvas
			class="bkx"
			width="200" height="200"
			data-chart="doughnut"
			data-dataset="[100, 260]"
			data-dataset-options="{ backgroundColor: ['#1ca8dd', '#1bc98e'] }"
			data-labels="['Referrals', 'Direct']">
			</canvas>
		</div>
		<strong class="asd">Traffic</strong>
		<h4>Direct vs Referrals</h4>
	</div>
</div>

-->

<div class="bkz aav aaj">
  <h3 class="bla blb">Quick stats</h3>
</div>

<div class="dh bmk">
  <div class="eq fp aaj ahq ano">
    <div class="bml bks">
      <div class="abw">
	       <?php
			$statsStudentTotals = $db->where('name', "student_rows_total");
			$statsStudentTotals = $db->orderBy('date_created', "ASC");
			$statsStudentTotals = $db->get('_stats', '7');
			
			
			foreach ($statsStudentTotals AS $studentTotal) {
				$studentTotalArray[] = $studentTotal['value'];
			}
			$studentTotalArray = array_reverse($studentTotalArray);
			
			?>
        <span class="bkn">Students</span>
       <h2 class="bkm"><?php echo $studentsCount; ?><small class="bko bkq">3.7%</small></h2>
        <hr class="bkw zo">
      </div>
     <canvas id="sparkline1" width="378" height="94"
        class="bmm"
        data-chart="spark-line"
		data-dataset="[[<?php echo implode($studentTotalArray,","); ?>]]"
        data-labels="['a','b','c','d','e','f','g']"
        style="width: 189px; height: 47px;"></canvas>
    </div>
  </div>
  <div class="eq fp aaj ahq ano">
    <div class="bml bkv">
      <div class="abw">
	      <?php
			$statsPersonsTotals = $db->where('name', "person_rows_total");
			$statsPersonsTotals = $db->orderBy('date_created', "ASC");
			$statsPersonsTotals = $db->get('_stats', '7');
			
			foreach ($statsPersonsTotals AS $personTotal) {
				$personTotalArray[] = $personTotal['value'];
			}
			$personTotalArray = array_reverse($personTotalArray);
			?>
        <span class="bkn">Persons</span>
        <h2 class="bkm"><?php echo $personsCount; ?><small class="bko bkq">3.7%</small></h2>
        <hr class="bkw zo">
      </div>
      <canvas id="sparkline1" width="378" height="94"
        class="bmm"
        data-chart="spark-line"
		data-dataset="[[<?php echo implode($personTotalArray,","); ?>]]"
        data-labels="['a','b','c','d','e','f','g']"
        style="width: 189px; height: 47px;"></canvas>
    </div>
  </div>
  <div class="eq fp aaj ahq ano">
    <div class="bml bkt">
      <div class="abw">
	      <?php
			$logonsAll = $db->where('type', "LOGON");
			$logonsAll = $db->get('_logs', '7');
			$logonsAllCount = $db->count;
			
			$logonsByDay = $db->rawQuery("SELECT DATE(date_created) AS date_created, COUNT(*) AS cnt FROM _logs WHERE type = 'LOGON' GROUP BY DATE(date_created) ORDER BY date_created ASC");
			foreach ($logonsByDay AS $day) {
				$logonsCountArray[] = $day['cnt'];
			}
			$logonsCountArray = array_reverse($logonsCountArray);
			?>
        <span class="bkn">Logons</span>
		<h2 class="bkm"><?php echo $logonsAllCount; ?><small class="bko bkq">3.7%</small></h2>
        <hr class="bkw zo">
      </div>
      <canvas id="sparkline1" width="378" height="94"
        class="bmm"
        data-chart="spark-line"
		data-dataset="[[<?php echo implode($logonsCountArray,","); ?>]]"
        data-labels="['a','b','c','d','e','f','g']"
        style="width: 189px; height: 47px;"></canvas>
    </div>
  </div>
  <div class="eq fp aaj ahq ano">
    <div class="bml bku">
		<div class="abw">
			<?php
			$logViewsAll = $db->where('type', "VIEW");
			$logViewsAll = $db->get('_logs');
			$logViewsAllCount = $db->count;
			
			$logViewsByDay = $db->rawQuery("SELECT DATE(date_created) AS date_created, COUNT(*) AS cnt FROM _logs WHERE type = 'VIEW' GROUP BY DATE(date_created) ORDER BY date_created DESC");
			foreach ($logViewsByDay AS $day) {
				$logViewsCountArray[] = $day['cnt'];
			}
			$logViewsCountArray = array_reverse(array_slice($logViewsCountArray, 0, 7));
			?>
			<span class="bkn">Views</span>
			<h2 class="bkm"><?php echo $logViewsAllCount; ?><small class="bko bkq">1.3%</small></h2>
			<hr class="bkw zo">
		</div>
		<canvas id="sparkline1" width="378" height="94" class="bmm"
		data-chart="spark-line"
		data-dataset="[[<?php echo implode($logViewsCountArray,","); ?>]]"
		data-labels="['a','b','c','d','e','f','g']"
		style="width: 189px; height: 47px;"></canvas>
	</div>
</div>
</div>

<hr class="aav">

<div class="dh">
  <div class="eq aax">
    <div class="by aaj">
      <h6 class="atf">Upcoming Meals</h6>
      
      	<?php
	      foreach ($upcomingMeals AS $meal) {
		    $bookings = $db->where ("meal_uid", $meal['uid']);
		    $bookings = $db->get ("bookings");
		    $bookingsCount = $db->count;
		    
		    $mealBookingPercent = ($bookingsCount / $meal['capacity']) * 100 . "%";
		    
		   	$output  = "<a class=\"mo od tc ra\" href=\"index.php?n=meal&mealUID=" . $meal['uid'] . "\">";
		   	$output .= "<span class=\"atg\" style=\"width: " . $mealBookingPercent . ";\"></span>";
		   	$output .= "<span>" . $meal['name'] . "</span>";
		   	$output .= "<span class=\"asd\">" . date('j M G:ia', strtotime($meal['date'])) . "</span>";
		   	$output .= "</a>";
		   	
		   	echo $output;
	      }
	      ?>
            </div>
    <a href="#" class="ce ko acb">All countries</a>
  </div>
  <div class="eq aax">
    <div class="by aaj">
      <h6 class="atf">
        Most visited pages
      </h6>
      
        <a class="mo od tc ra" href="#">
          <span>/ (Logged out)</span>
          <span class="asd">3,929,481</span>
        </a>
      
        <a class="mo od tc ra" href="#">
          <span>/ (Logged in)</span>
          <span class="asd">1,143,393</span>
        </a>
      
        <a class="mo od tc ra" href="#">
          <span>/tour</span>
          <span class="asd">938,287</span>
        </a>
      
        <a class="mo od tc ra" href="#">
          <span>/features/something</span>
          <span class="asd">749,393</span>
        </a>
      
        <a class="mo od tc ra" href="#">
          <span>/features/another-thing</span>
          <span class="asd">695,912</span>
        </a>
      
        <a class="mo od tc ra" href="#">
          <span>/users/username</span>
          <span class="asd">501,938</span>
        </a>
      
        <a class="mo od tc ra" href="#">
          <span>/page-title</span>
          <span class="asd">392,842</span>
        </a>
      
        <a class="mo od tc ra" href="#">
          <span>/some/page-slug</span>
          <span class="asd">298,183</span>
        </a>
      
        <a class="mo od tc ra" href="#">
          <span>/another/directory/and/page-title</span>
          <span class="asd">193,129</span>
        </a>
      
        <a class="mo od tc ra" href="#">
          <span>/one-more/page/directory/file-name</span>
          <span class="asd">93,382</span>
        </a>
      
    </div>
    <a href="#" class="ce ko acb">View all pages</a>
  </div>
</div>

<div class="by aaj">
  <h6 class="atf">
    Devices and resolutions
  </h6>
  
    <a class="mo od tc ra" href="#">
      <span>Desktop (1920x1080)</span>
      <span class="asd">3,929,481</span>
    </a>
  
    <a class="mo od tc ra" href="#">
      <span>Desktop (1366x768)</span>
      <span class="asd">1,143,393</span>
    </a>
  
    <a class="mo od tc ra" href="#">
      <span>Desktop (1440x900)</span>
      <span class="asd">938,287</span>
    </a>
  
    <a class="mo od tc ra" href="#">
      <span>Desktop (1280x800)</span>
      <span class="asd">749,393</span>
    </a>
  
    <a class="mo od tc ra" href="#">
      <span>Tablet (1024x768)</span>
      <span class="asd">695,912</span>
    </a>
  
    <a class="mo od tc ra" href="#">
      <span>Tablet (768x1024)</span>
      <span class="asd">501,938</span>
    </a>
  
    <a class="mo od tc ra" href="#">
      <span>Phone (320x480)</span>
      <span class="asd">392,842</span>
    </a>
  
    <a class="mo od tc ra" href="#">
      <span>Phone (720x450)</span>
      <span class="asd">298,183</span>
    </a>
  
    <a class="mo od tc ra" href="#">
      <span>Desktop (2560x1080)</span>
      <span class="asd">193,129</span>
    </a>
  
    <a class="mo od tc ra" href="#">
      <span>Desktop (2560x1600)</span>
      <span class="asd">93,382</span>
    </a>
  
</div>
<a href="#" class="ce ko acb">View all devices</a>
