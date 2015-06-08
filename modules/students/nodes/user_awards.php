<div class="tab-pane" id="awards">
	<div id="awardsFormAdd">
		<form class="form-horizontal" role="form">
			<div class="form-group">
				<label class="col-sm-2 control-label" for="inputAward">Award</label>
				<div class="col-sm-10">
					<select id="inputAward" class="form-control">
					
						<?php
						$awards = Awards::find_all();
						foreach($awards AS $award) {
							$output  = "<option value=\"" . $award->awdid . "\">";
							$output .= $award->name . " (" . $award->type . ")";
							$output .= "</option>";
							
							echo $output;
						}
						?>
						
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="inputDateAwarded">Date Awarded</label>
				<div class="col-sm-10">
					<input type="date" class="form-control" id="inputDateAwarded" placeholder="YYYY-MM-DD" value="<?php echo convertToDateString(null,false); ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="inputDateFrom">Date From</label>
				<div class="col-sm-10">
					<input type="date" class="form-control" id="inputDateFrom" placeholder="YYYY-MM-DD" value="<?php echo convertToDateString(null,false); ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="inputDateTo">Date To</label>
				<div class="col-sm-10">
					<input type="date" class="form-control" id="inputDateTo" placeholder="YYYY-MM-DD" value="<?php echo convertToDateString(null,false); ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="inputAwardValue">Value (£)</label>
				<div class="col-sm-10">
					<input class="form-control" id="inputAwardValue" type="number">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="inputNotes">Notes</label>
				<div class="col-sm-10">
					<textarea class="form-control" rows="3" id="inputNotes"></textarea>
				</div>
			</div>
			<button id="awardAddButton" type="button" class="btn btn-primary">Submit</button>
			<input type="hidden" id="inputStudentkey" value="<?php echo $user->studentid; ?>">
		</form>
		<div id="response_added"></div>
		<div class="clearfix"></div>
	</div>
	<?php
	foreach ($studentAwards AS $studentAward) {
		$award = Awards::find_by_uid($studentAward->awdkey);
		
		echo "<div>";
		$button  = "<button class=\"btn btn-mini btn-danger pull-right awardDeleteButton\" id=\"" . $studentAward->sawid . "\">Delete</button>";
		//$button .= "<button class=\"btn btn-mini pull-right\">Edit</button>";
		$button .= "";
		
		echo $button;
		
		echo "<h3>" . $award->name . " <span class=\"label\">" . $award->given_by . " " . $award->type . "</span></h3>";
		
		echo "<p>Awarded: " . $studentAward->dt_awarded . "</p>";
		echo "<p>From: " . convertToDateString($studentAward->dt_from) . " - To: " . convertToDateString($studentAward->dt_to) . "</p>";
		echo "<p>Value (£): " . $studentAward->value . "</p>";
		
		if (isset($studentAward->notes)) {
			echo "<p>Notes: " . $studentAward->notes . "</p>";
		}
		echo "</div>";
		echo "<hr />";
	}
	?>
</div>