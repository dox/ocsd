<div class="row">
	<div class="span12">
		<div class="page-header">
			<h1>Reports</h1>
		</div>
	</div>
</div>
<div class="row">
	<div class="span12">
		<h2>Step 1 <small> Select your report</small></h2>
		<table class="table table-bordered table-hover table-striped">
		<thead>
			<tr>
				<th>Report Name</th>
				<th>Description</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Lodgings List</td>
				<td>...</td>
				<td>
					<div class="btn-group">
						<button class="btn btn-small"><a href="report_pdf.php?n=report_lodgingslist.php&header=false">Generate Report</a></button>
						<button class="btn btn-small dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
						<ul class="dropdown-menu">
							<li><a href="report_pdf.php?n=report_lodgingslist.php&header=false"><i class="icon-file"></i> Generate as PDF</a></li>
							<li><a href="#"><i class="icon-th"></i> Generate as CSV</a></li>
							<li><a href="#"><i class="icon-wrench"></i> Edit Report</a></li>
						</ul>
					</div>
				</td>
			</tr>
			<tr>
				<td>Test Report</td>
				<td>...</td>
				<td>
					<div class="btn-group">
						<button class="btn btn-small">Generate Report</button>
						<button class="btn btn-small dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
						<ul class="dropdown-menu">
							<li><a href="#"><i class="icon-file"></i> Generate as PDF</a></li>
							<li><a href="#"><i class="icon-th"></i> Generate as CSV</a></li>
							<li><a href="#"><i class="icon-wrench"></i> Edit Report</a></li>
						</ul>
					</div>
				</td>
			</tr>
		</tbody>
		</table>
	</div>
	<div class="span12">
		<h2>Step 2 <small> Select your date range</small></h2>
		<form class="form-horizontal">
			<div class="control-group">
				<label class="control-label" for="inputDateFrom">Date From:</label>
				<div class="controls">
					<input type="text" id="inputDateFrom" placeholder="Date From">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="inputDateTo">Date To:</label>
				<div class="controls">
					<input type="text" id="inputDateTo" placeholder="Date To">
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<label class="checkbox"><input type="checkbox">test</label>
					<button type="submit" class="btn">test</button>
				</div>
			</div>
		</form>
	</div>
	<div class="span12">
		<h2>Step 3 <small> Configure your report options</small></h2>
	</div>
	<div class="span12">
		<h2>Step 4 <small> Generate your report</small></h2>
	</div>
</div>