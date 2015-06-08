<div class="tab-pane" id="reports">
	<p>
	<div class="btn-group">
		<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">Generate Transcript <span class="caret"></span></button>
		<ul class="dropdown-menu" role="menu">
			<li><a href="report_pdf.php?n=transcript.php&studentid=<?php echo $user->id(); ?>">Letterhead and exam paper details</a></li>
			<li><a href="report_pdf.php?n=transcript.php&exams=false&studentid=<?php echo $user->id(); ?>">Letterhead without exam paper details</a></li>
			<li class="divider"></li>
			<li><a href="report_pdf.php?n=transcript.php&studentid=<?php echo $user->id(); ?>&header=false">Without letterhead and with exam paper details</a></li>
			<li><a href="report_pdf.php?n=transcript.php&exams=false&studentid=<?php echo $user->id(); ?>&header=false">Without letterhead and without exam paper details</a></li>
		</ul>
	</div>
	</p>
	<p><button class="btn">Cert. College Membership</button></p>
	<p><button class="btn">Cert. College Membership v.2</button></p>
	<p><button class="btn">Council Tax Exemption</button></p>
	<p><button class="btn">Immigration Permit Confirmation</button></p>
</div>