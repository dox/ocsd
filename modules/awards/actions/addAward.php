<?php
header('content-type: application/json; charset=utf-8');

include_once("../../../engine/initialise.php");

if (isset($_POST['new_award_name'])) {
	if ($_POST['new_award_name'] != "") {
		$award = new Awards;
		$award->name		= $_POST['new_award_name'];
		$award->type		= $_POST['new_award_type'];
		$award->given_by	= $_POST['new_award_given_by'];
		$award->create();
		
		$newlyAddedAward = Awards::find_by_uid($award->awdid);
		$addArray['id'] = $newlyAddedAward->awdid;
		$addArray['id'] = "1";
		$addArray['name'] = $newlyAddedAward->name;
		$addArray['type'] = $newlyAddedAward->type;
		$addArray['given_by'] = $newlyAddedAward->given_by;
		
		$log = new Logs;
		$log->notes			= "New Award '" . $_POST['new_award_name'] . "' added";
		$log->updated_value	= $newlyAddedAward->awdid;
		$log->type			= "create";
		$log->create();
	} else {
		$addArray['errors'] = "Unknown error";
	}
}
echo json_encode($addArray);
?>