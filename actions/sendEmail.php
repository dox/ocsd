<?php
include_once("../includes/autoload.php");

$person = new Person($_POST['cudid']);

if (isset($person->oxford_email)) {
	$subject = "Your delivery is ready to collect";
	
	$messageBody  = "<h1>A parcel/package has arrived for you today at the St Edmund Hall Lodge</h1>";
	//$messageBody .= "<h2>" . $person['oxford_email'] . "</h2>";
	$messageBody .= "<h2>Please collect this from the lodge within 24 hours (in and out of term)</h2>";
	$messageBody .= "<p>Note: you may be required to present some form of identification (e.g. Bodcard) upon collection.</p>";
	$messageBody .= "<p>Due to space restrictions, all large deliveries will be stored in the pigeon hole area on arrival and smaller items will be moved from the Lodge to the pigeon hole area after 24 hours.</p>";
	$messageBody .= "<p>Lodge staff are not able to monitor items in the pigeon hole area, so you are responsible for items in this area.</p>";
	$messageBody .= "<p>If you are unable to collect in person, a friend may collect on your behalf.  Please email authorisation to the Lodge (lodge@seh.ox.ac.uk) prior to collection.</p>";
	$messageBody .= "<p>Thank you<br /><br /><strong>Lionel Knight</strong><br />Head Porter</p>";
	
	//sendMail($person['oxford_email'], "Confirmation of Booking", $messageBody);
	sendMail($subject, array($person->oxford_email), $messageBody, "noreply@seh.ox.ac.uk", "No Reply");
	
	$logInsert = (new Logs)->insert("email","success",$person->cudid,"Lodge email sent to <code>" . $person->oxford_email . "</code>");
} else {
	$logInsert = (new Logs)->insert("email","error",$person->cudid,"Lodge email failed to send to <code>" . $person->oxford_email . "</code>");
}
?>