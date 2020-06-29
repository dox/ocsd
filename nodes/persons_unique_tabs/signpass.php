<?php

$student = $db->where("cudid", $_GET['cudid']);
$student = $db->getOne("Student");
$personSignPass = $db->where("cudid", $_GET['cudid']);
$personSignPass = $db->getOne("Person");

$applications = $db->where("cudid", $_GET['cudid']);
$applications = $db->getOne("Applications");

$supervisors = $db->where("cudid", $_GET['cudid']);
$supervisors = $db->getOne("Supervisors");

$contactDetails = $db->where ("cudid", $_GET['cudid']);
$contactDetails = $db->get("ContactDetails");
$addresses = $db->where ("cudid", $_GET['cudid']);
$addresses = $db->get("Addresses");

if ($personSignPass['firstname'] <> $personSignPass['known_as']) {
	$name = $personSignPass['FullName'] . " (" . $personSignPass['known_as'] . ")";
} else {
	$name = $personSignPass['FullName'];
}

function monthName($num = 1) {
	$month_name = date("F", mktime(0, 0, 0, $num, 10));
	return $month_name;
}
?>
<pre>
{
  "formatVersion" : 1,
  "passTypeIdentifier" : "pass.seh.ox.ac.uk.testpass",
  "serialNumber" : "<?php echo "bod-" . $personSignPass['barcode']; ?>",
  "webServiceURL" : "https://www.seh.ox.ac.uk",
  "authenticationToken" : "vxwxd7J8AlNNFPS8k0a0FfUFtq0ewzFdc",
  "teamIdentifier" : "24SGZVWX7R",
  "locations" : [
    {
      "longitude" : 51.752457,
      "latitude" : -1.248005
    },
    {
      "longitude" : 51.763996,
      "latitude" : -1.255295
    }
  ],
  "barcode" : {
    "message" : "<?php echo $personSignPass['barcode']; ?>",
    "format" : "PKBarcodeFormatQR",
    "messageEncoding" : "iso-8859-1"
  },
  "organizationName" : "Bodcard",
  "description" : "University of Oxford Bodcard",
  "foregroundColor" : "rgb(255, 255, 255)",
  "backgroundColor" : "rgb(1, 24, 55)",
  "labelColor" : "rgb(255, 255, 255)",
  "generic" : {
    "primaryFields" : [
      {
        "key" : "member",
        "value" : "<?php echo $personSignPass['FullName']; ?>"
      }
    ],
    "secondaryFields" : [
      {
        "key" : "subtitle",
        "label" : "VALID UNTIL",
        "value" : "<?php echo substr($personSignPass['University_Card_End_Dt'], 6, 2) . " " . monthName(substr($personSignPass['University_Card_End_Dt'], 4, 2)) . " " . substr($personSignPass['University_Card_End_Dt'], 0, 4); ?>"
      }
    ],
    "auxiliaryFields" : [
      {
        "key" : "TYPE",
        "label" : "TYPE",
        "value" : "<?php echo $personSignPass['university_card_type']; ?>"
      },
      {
        "key" : "college",
        "value" : "St Edmund Hall",
        "textAlignment" : "PKTextAlignmentRight"
      }
    ],
    "backFields" : [
      {
        "label" : "Full Name",
        "key" : "name",
        "value" : "<?php echo $personSignPass['FullName']; ?>",
      },
      {
        "label" : "SSO",
        "key" : "sso",
        "value" : "<?php echo $personSignPass['sso_username']; ?>"
      },
      {
        "label" : "Bodcard",
        "key" : "bodcard",
        "value" : "<?php echo $personSignPass['barcode']; ?>",
      },
      {
        "label" : "Telephone Number",
        "key" : "telephone",
        "value" : "<?php echo $personSignPass['internal_tel']; ?>"
      },
      {
        "label" : "E-Mail Address",
        "key" : "email",
        "value" : "<?php echo $personSignPass['oxford_email']; ?>",
      },
      {
        "label" : "Card Type",
        "key" : "type",
        "value" : "College Staff",
      },
      {
        "dateStyle" : "PKDateStyleShort",
        "label" : "Bodcard Start Date",
        "key" : "startdate",
        "value" : "<?php echo substr($personSignPass['University_Card_Start_Dt'], 0, 4) . "-" . substr($personSignPass['University_Card_Start_Dt'], 4, 2) . "-" . substr($personSignPass['University_Card_Start_Dt'], 6, 2) . "T00:00-00:00"; ?>",
      }
    ]
  }
}
</pre>

<?php
$includeFile = true;
?>
