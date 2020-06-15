<?php
$personsClass = new Person();
	
$personsAll = $personsClass->allPersons ("Person");
$personsAllCount = $personsClass->allPersonsCount();

$studentArrayTypes = array('GT', 'GR', 'UG', 'VR', 'PT', 'VD', 'VV', 'VC');

$studentOutput = "";
$studentOutputCount = 0;
$otherOutput = "";
$otherOutputCount = 0;

?>
<div class="bls">
	<div class="blt">
		<h6 class="blv"><a class="breadcrumb-item" href="index.php">OCSD</a> / <a href="index.php?n=ad_mifare">AD MiFare</a> / </h6>
		<h2 class="blu">ER Contacts</h2>
	</div>
</div>

<div class="container">
	<table class="table table-striped table-dark">
  <thead>
    <tr>
      <th>CUDID</th>
      <th>Number</th>
    </tr>
  </thead>
  <tbody>
	<?php
	$i = 1;
	
	foreach ($personsAll AS $person) {
		$numbersArray = array();
		
		$contactDetails = $db->where ("cudid", $person['cudid']);
		$contactDetails = $db->get("ContactDetails");
		$addresses = $db->where ("cudid", $person['cudid']);
		$addresses = $db->get("Addresses");
		
		foreach ($contactDetails AS $contact) {
			if ($contact['Type'] == "Phone") {
				$numbersArray[] = $contact['Value'];
				//echo $person['cudid'] . "," . $contact['Value'] . "<br />";
			}
		}
		
		foreach ($addresses AS $address) {
			if (isset($address['MobileNo'])) {
				$numbersArray[] = $address['MobileNo'];
			}
		}
		
		$numbersArray = array_unique($numbersArray);
		
		if (count($numbersArray) > 0) {
			foreach ($numbersArray AS $number) {
				echo "<tr><td>CUD-" . $i . "</td><td>" . $number . "</td></tr>";
				$i++;
			}
		}
	}
	

	?>
  </tbody>
	</table>
</div>