<?php
/**
 * Nightly student CSV export
 *
 * Fixed-width validation/truncation included
 * Archives previous file before creating a new one
 */

include_once("../inc/autoload.php");

$dateStamp   = date('Ymd_His');

$exportDir   = __DIR__ . '/../exports/';

$currentFile = $exportDir . 'students.csv';

// --------------------------------------------------
// ENSURE DIRECTORIES EXIST
// --------------------------------------------------

if (!is_dir($exportDir)) {
	mkdir($exportDir, 0775, true);
}

// --------------------------------------------------
// REMOVE EXISTING FILE
// --------------------------------------------------

if (file_exists($currentFile)) {

	if (!unlink($currentFile)) {

		cliOutput("FAILED to remove existing CSV", "red");

		$log->create([
			'category'    => 'cron',
			'result'      => 'error',
			'description' => 'Failed to remove existing students.csv'
		]);

		die();
	}

	cliOutput("Removed existing CSV", "yellow");
}

// --------------------------------------------------
// OPEN CSV
// --------------------------------------------------

$fp = fopen($currentFile, 'w');

if (!$fp) {

	cliOutput("FAILED to create CSV file", "red");

	$log->create([
		'category'    => 'cron',
		'result'      => 'error',
		'description' => 'Failed to create students.csv'
	]);

	die();
}

// UTF-8 BOM for Excel
fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

// --------------------------------------------------
// FIELD FORMATTER
// --------------------------------------------------

function csvField($value, $length = null, $uppercase = false)
{
	$value = trim((string)$value);

	if ($uppercase) {
		$value = strtoupper($value);
	}

	if ($length !== null) {
		$value = mb_substr($value, 0, $length);
	}

	return $value;
}

function formatDateField($date)
{
	if (empty($date)) {
		return '';
	}

	$timestamp = strtotime($date);

	if (!$timestamp) {
		return '';
	}

	return date('d/m/y', $timestamp);
}

// --------------------------------------------------
// GET STUDENTS
// --------------------------------------------------

$persons = (new Persons())->all();

$headerWritten = false;
$exportCount   = 0;

// --------------------------------------------------
// EXPORT LOOP
// --------------------------------------------------

foreach ($persons as $person) {

	$homeAddress = $person->addresses()->getHomeAddress();

	$row = [

		// REQUIRED FIELDS

		'Account Number'     => csvField($person->sso_username, 10),
		'Card Number'        => csvField($person->MiFareID ?? '', 25),

		'Title'              => csvField($person->titl_cd, 10),
		'Forename'           => csvField($person->firstname, 15),
		'Initial'            => csvField(substr($person->middlenames ?? '', 0, 1), 4),
		'Surname'            => csvField($person->lastname, 20),

		'User Group 1'       => csvField($person->university_card_type ?? '', 6),
		'User Group 2'       => '',
		'User Group 3'       => '',
		'User Group 4'       => '',

		'Gender'             => csvField(
			in_array(strtoupper($person->gnd), ['M', 'F'])
				? strtoupper($person->gnd)
				: '',
			1
		),

		'DOB'                => formatDateField($person->dob),

		'Expiry date'        => formatDateField($person->University_Card_End_Dt),

		'Free token 1'       => csvField($person->courseYear() ?? '', 6),
		'Free token 2'       => '',
		'Free token 3'       => '',
		'Free token 4'       => '',

		'Work telephone'     => '',
		'Home telephone'     => csvField($homeAddress['TelNo'] ?? '', 20),
		'Fax'                => '',

		'Email'              => csvField($person->oxford_email, 256),

		'Mobile'             => csvField($homeAddress['MobileNo'] ?? '', 20),

		'Job title'          => '',

		'Inactive Flag'      => (
			strtolower($person->course_status ?? '') === 'inactive'
				? 'Y'
				: 'N'
		),

		'Car reg'            => '',

		'Address 1'          => csvField($homeAddress['Line1'] ?? '', 30),
		'Address 2'          => csvField($homeAddress['Line2'] ?? '', 30),
		'Address 3'          => csvField($homeAddress['Line3'] ?? '', 30),
		'Address 4'          => csvField($homeAddress['Line4'] ?? '', 30),
		'Address 5'          => csvField($homeAddress['Line5'] ?? '', 30),

		'Postcode'           => csvField($homeAddress['PostCode'] ?? '', 10),

		'Discount group 1'   => '',
		'Discount group 2'   => '',
		'Discount group 3'   => '',

		'Price List'         => csvField('STD', 3, true),

		'Credit Limit'       => number_format(0, 2, '.', ''),

		'Start date'         => formatDateField($person->University_Card_Start_Dt),

		'Payroll Number'     => '',
		'Budget Account'     => ''
	];

	// --------------------------------------------------
	// WRITE HEADER
	// --------------------------------------------------

	if (!$headerWritten) {

		fputcsv($fp, array_keys($row));

		$headerWritten = true;
	}

	// --------------------------------------------------
	// WRITE ROW
	// --------------------------------------------------

	fputcsv($fp, $row);

	$exportCount++;
}

// --------------------------------------------------
// CLOSE FILE
// --------------------------------------------------

fclose($fp);

// --------------------------------------------------
// LOGGING
// --------------------------------------------------

$db->upsertByName('cron_student_csv_export', date('c'));

$log->create([
	'category'    => 'cron',
	'result'      => 'success',
	'description' => 'Exported ' . $exportCount . ' student records'
]);

cliOutput(
	"CSV export complete: {$exportCount} students",
	"green"
);

?>