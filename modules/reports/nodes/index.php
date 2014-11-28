<div class="page-header">
	<h1>Reports</h1>
</div>

<div class="row">
	<div class="col-md-4">
		<div class="well">
		<h4>Quick Reports</h4>
		<div class="btn-group">
			<a href="report_pdf.php?n=report_lodgingslist.php&header=false" class="btn btn-default">Lodgings List</a>
			<button class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li><a href="report_pdf.php?n=report_lodgingslist.php&header=false"><i class="fa fa-file-pdf-o"></i> Generate as PDF</a></li>
				<li><a href="report_pdf.php?n=report_lodgingslist.php&header=false&type=csv"><i class="fa fa-file-excel-o"></i> Generate as CSV</a></li>
				<li><a href="#"><i class="fa fa-wrench"></i> Edit Report</a></li>
			</ul>
		</div>
		<div class="clearfix"></div>
		<div class="btn-group">
			<a href="report_pdf.php?n=report_deanslist.php&header=false&type=csv" class="btn btn-default">Dean's List</a>
			<button class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li><a href="report_pdf.php?n=report_deanslist.php&header=false"><i class="fa fa-file-pdf-o"></i> Generate as PDF</a></li>
				<li><a href="report_pdf.php?n=report_deanslist.php&header=false&type=csv"><i class="fa fa-file-excel-o"></i> Generate as CSV</a></li>
				<li><a href="#"><i class="fa fa-wrench"></i> Edit Report</a></li>
			</ul>
		</div>
		<div class="clearfix"></div>
		<div class="btn-group">
			<span class="btn btn-default">Photo List</span>
			<button class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
			<ul class="dropdown-menu">
				<?php
				$year = date('Y');
				$i = 0;
				do {
					echo "<li><a href=\"report_pdf.php?n=report_photos.php&header=false&cohort=" . $year . "\"><i class=\"fa fa-file-pdf-o\"></i> " . $year . " Cohort</a></li>";
					
					$year --;
					$i ++;
				} while ($i <= 10);
				?>
			</ul>
		</div>
		<div class="clearfix"></div>
		<div class="btn-group">
			<a href="report_pdf.php?n=report_nationalities.php&header=false" class="btn btn-default">Nationalities</a>
			<button class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li><a href="report_pdf.php?n=report_nationalities.php&header=false&studenttype=ug"><i class="fa fa-file-pdf-o"></i> Only Undergraduates</a></li>
				<li><a href="report_pdf.php?n=report_nationalities.php&header=false&studenttype=pg"><i class="fa fa-file-pdf-o"></i> Only Postgraduates</a></li>
				<li><a href="report_pdf.php?n=report_nationalities.php&header=false&studenttype=vx"><i class="fa fa-file-pdf-o"></i> Only Visiting Students</a></li>
			</ul>
		</div>
		<div class="clearfix"></div>
		<div class="btn-group">
			<a href="#" class="btn btn-default">Address Lists</a>
			<button class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li><a href="report_pdf.php?n=report_addressList.php&type=csv&studenttype=ug"><i class="fa fa-file-pdf-o"></i> Only Undergraduates</a></li>
				<li><a href="report_pdf.php?n=report_addressList.php&type=csv&studenttype=pg"><i class="fa fa-file-pdf-o"></i> Only Postgrads</a></li>
				<li><a href="report_pdf.php?n=report_addressList.php&type=csv&studenttype=vx"><i class="fa fa-file-pdf-o"></i> Only Visiting Students</a></li>
			</ul>
		</div>
		<div class="clearfix"></div>
		<div class="btn-group">
			<a href="#" class="btn btn-default">Awards List</a>
			<button class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li><a href="report_pdf.php?n=report_awardsList.php&type=csv&sencompass=year"><i class="fa fa-file-excel-o"></i> All Awards</a></li>
			</ul>
		</div>
		</div>
	</div>
	<div class="col-md-8">
		<h2>Guided Report</h2>
		<form class="form-horizontal" action="index.php?m=reports&n=guidedReport.php" method="post">
		<h3>Step 1 <small> Select fields to include</small></h3>
		<div class="control-group">
			<label class="control-label" for="inputEmail">Fields</label>
			<div class="controls">
				<select multiple="multiple" name="fields[]">
					<option selected value="studentid">studentid</option>
					<option selected value="st_type">st_type</option>
					<option value="titlekey">titlekey</option>
					<option value="initials">initials</option>
					<option selected value="forenames">forenames</option>
					<option value="prefname">prefname</option>
					<option selected value="surname">surname</option>
					<option value="prev_surname">prev_surname</option>
					<option value="suffix">suffix</option>
					<option value="marital_status">marital_status</option>
					<option selected value="dt_birth">dt_birth</option>
					<option selected value="gender">gender</option>
					<option value="nationality">nationality</option>
					<option value="birth_cykey">birth_cykey</option>
					<option value="resid_cykey">resid_cykey</option>
					<option value="citiz_cykey">citiz_cykey</option>
					<option value="optout">optout</option>
					<option value="family">family</option>
					<option value="eng_lang">eng_lang</option>
					<option value="occup_bg">occup_bg</option>
					<option value="disability">disability</option>
					<option value="ethkey">ethkey</option>
					<option value="rskey">rskey</option>
					<option value="cskey">cskey</option>
					<option value="relkey">relkey</option>
					<option value="rckey">rckey</option>
					<option value="SSNref">SSNref</option>
					<option value="fee_status">fee_status</option>
					<option value="univ_cardno">univ_cardno</option>
					<option value="dt_card_exp">dt_card_exp</option>
					<option selected value="course_yr">course_yr</option>
					<option value="notes">notes</option>
					<option value="email1">email1</option>
					<option value="email2">email2</option>
					<option value="mobile">mobile</option>
					<option value="dt_start">dt_start</option>
					<option value="dt_end">dt_end</option>
					<option value="dt_matric">dt_matric</option>
					<option value="oucs_id">oucs_id</option>
					<option value="yr_app">yr_app</option>
					<option value="yr_entry">yr_entry</option>
					<option value="yr_cohort">yr_cohort</option>
					<option value="dt_created">dt_created</option>
					<option value="dt_lastmod">dt_lastmod</option>
					<option value="who_mod">who_mod</option>
					<option value="photo">photo</option>
				</select>
			</div>
		</div>
		
		<h3>Step 2 <small> Select course years</small></h3>
		
		<div class="control-group">
			<label class="control-label" for="inputEmail">Course Year</label>
			<div class="controls">
				<select multiple="multiple" name="course_yr[]">
					<option selected value="1st">1st</option>
					<option value="2nd">2nd</option>
					<option value="3rd">3rd</option>
					<option value="4th">4th</option>
					<option value="5th">5th</option>
					<option value="6th">6th</option>
					<option value="7th">7th</option>
					<option value="8th">8th</option>
				</select>
			</div>
		</div>
		<button type="submit" class="btn">Run Report</button>
    	</form>
		
		<h2>Custom Report</h2>
		<form class="form-horizontal" action="index.php?m=reports&n=customReport.php" method="post">
		<div class="control-group">
			<label class="control-label" for="textareaSQL">SQL</label>
			<div class="controls">
				<textarea rows="3" id="textareaSQL" name="textareaSQL">SELECT * FROM students</textarea>
			</div>
		</div>
		<button type="submit" class="btn">Run Report</button>
		</form>

	</div>
</div>