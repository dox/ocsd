<div class="tab-pane" id="addresses">
	<div id="contactFormAdd">
		<form class="form-horizontal" role="form">
			<div class="form-group">
				<div class="col-sm-10">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="inputAdd1">Address 1</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="inputAdd1">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="inputAdd2">Address 2</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="inputAdd2">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="inputAdd3">Address 3</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="inputAdd3">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="inputAdd4">Address 4</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="inputAdd4">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="inputAdd1">Town</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="inputTown">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="inputAdd1">County</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="inputCounty">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="inputAdd1">Postcode</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="inputPostcode">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="inputCykey">Country</label>
						<div class="col-sm-10">
							<select id="inputCykey" class="form-control">
							    <?php
							    $countries = Countries::find_all();
							    foreach($countries AS $country) {
							    	$output  = "<option value=\"" . $country->cyid . "\">";
							    	$output .= $country->formal;
							    	$output .= "</option>";
							    	
							    	echo $output;
							    }
							    ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="inputTelephone">Telephone</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="inputTelephone">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="inputMobile">Mobile</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="inputMobile">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="inputFax">Fax</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="inputFax">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="inputEmail">E-Mail</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="inputEmail">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="inputAtkey">Address Type</label>
						<div class="col-sm-10">
							<select id="inputAtkey" class="form-control">
							    <?php
							    $addressTypes = AddressTypes::find_all();
							    foreach($addressTypes AS $addressType) {
							    	$output  = "<option value=\"" . $addressType->atid . "\">";
							    	$output .= $addressType->type;
							    	$output .= "</option>";
							    	
							    	echo $output;
							    }
							    ?>
							</select>
						</div>
					</div>
				</div>
			</div>
			<button id="addressAddButton" type="button" class="btn btn-primary">Submit</button>
			<input type="hidden" id="inputStudentkey" value="<?php echo $user->studentid; ?>">
			<input type="hidden" class="form-control" id="inputDefault" value="No">
		</form>
	</div>
	<?
	$resStatusClass = new resStatus;
	$resStatuses = $resStatusClass->find_all();
	$resStatus = $resStatusClass->find_by_uid($user->rskey);
	?>
	<p class="lead">Resident Status: <?php echo $resStatus->status; ?></p>
	<h3>Home Residence</h3>
	<div class="row">
		<?php
		foreach ($addresses AS $address) {
			echo $address->displayAddress();
		}
		?>
	</div>
	<h3>College Residence</h3>
	<?php
	foreach ($residences AS $resAddress) {
		$output  = "<div class=\"row\">";
		$output .= "<div class=\"col-md-4\">";
		$output .= $resAddress->displayAddress();
		$output .= "</div>";
		$output .= "</div>";

		echo $output;
	}
	?>
</div>