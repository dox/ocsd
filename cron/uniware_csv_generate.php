<?php
/**
 * Nightly users CSV export for Uniware import
 *
 * Fixed-width validation/truncation included
 */

include_once("../inc/autoload.php");

$dateStamp   = date('Ymd_His');

$exportDir   = __DIR__ . '/../exports/';

$currentFile = $exportDir . 'uniware_users.csv';
$overrideFile = __DIR__ . '/../uniware_credit_limit_overrides.csv';

// Optional external overrides. The file is deliberately outside the repository's
// cron folder so it can be maintained independently of the script deployment.
// Format: username,name,credit_limit,user_group_1 (blank lines and lines beginning
// with # are ignored). The name column is informational.
$creditLimitOverrides = [];
$userGroup1Overrides = [];

if (is_readable($overrideFile)) {
	$overrideHandle = fopen($overrideFile, 'r');

	if ($overrideHandle !== false) {
		while (($overrideRow = fgetcsv($overrideHandle)) !== false) {
			if (count($overrideRow) < 2) {
				continue;
			}

			$username = trim((string)$overrideRow[0]);
			$isHeader = strtolower($username) === 'username';
			// Keep accepting the previous username,credit_limit format. In the new
			// format, the name column is informational, the limit is column 3, and
			// User Group 1 is column 4.
			$creditLimit = trim((string)$overrideRow[count($overrideRow) >= 3 ? 2 : 1]);
			$userGroup1 = trim((string)($overrideRow[3] ?? ''));

			if ($username === '' || str_starts_with($username, '#') || $isHeader) {
				continue;
			}

			// Uniware expects a non-negative amount, written to two decimal places.
			if (is_numeric($creditLimit) && (float)$creditLimit >= 0) {
				$creditLimitOverrides[$username] = number_format((float)$creditLimit, 2, '.', '');
			}

			if ($userGroup1 !== '') {
				$userGroup1Overrides[$username] = csvField($userGroup1, 6);
			}
		}

		fclose($overrideHandle);
	}
}

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
			'description' => 'Failed to remove existing uniware_users.csv'
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
		'description' => 'Failed to create uniware_users.csv'
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

	return date('d/m/Y', $timestamp);
}

// --------------------------------------------------
// GET USERS
// --------------------------------------------------

$persons = (new Persons())->all();

$headerWritten = false;
$exportCount   = 0;

// --------------------------------------------------
// EXPORT LOOP
// --------------------------------------------------

foreach ($persons as $person) {
	// skip users without a valid MiFareID
	if (empty($person->MiFareID)) {
		continue;
	}
	
	$homeAddress = $person->addresses()->getHomeAddress();

	$row = [

		// REQUIRED FIELDS

		'User ID'            => csvField($person->sso_username, 10),
		'Card ID'            => csvField($person->MiFareID ?? '', 25),

		'Title'              => csvField($person->titl_cd, 10),
		'Forename'           => csvField($person->firstname, 15),
		'Surname'            => csvField($person->lastname, 20),

		'User Group 1'       => $userGroup1Overrides[$person->sso_username] ?? csvField($person->university_card_type ?? '', 6),
		//'User Group 2'       => '',
		//'User Group 3'       => '',
		//'User Group 4'       => '',

		/*'Gender'             => csvField(
			in_array(strtoupper($person->gnd), ['M', 'F'])
				? strtoupper($person->gnd)
				: '',
			1
		),*/

		'Date of birth (DOB)' => formatDateField($person->dob),

		'Expiry Date'        => formatDateField($person->University_Card_End_Dt),

		'User Group 2'       => csvField($person->courseYear() ?? '', 6),
		'User Group 3'       => csvField($person->sits_student_code, 10),
		//'Free token 3'       => '',
		//'Free token 4'       => '',

		//'Work telephone'     => '',
		//'Home telephone'     => csvField($homeAddress['TelNo'] ?? '', 20),
		//'Fax'                => '',

		'Email'              => csvField($person->oxford_email, 256),

		//'Mobile'             => csvField($homeAddress['MobileNo'] ?? '', 20),

		//'Job title'          => '',

		'Inactive'           => (
			strtolower($person->course_status ?? '') === 'inactive'
				? 'Y'
				: 'N'
		),

		//'Car reg'            => '',

		//'Address 1'          => csvField($homeAddress['Line1'] ?? '', 30),
		//'Address 2'          => csvField($homeAddress['Line2'] ?? '', 30),
		//'Address 3'          => csvField($homeAddress['Line3'] ?? '', 30),
		//'Address 4'          => csvField($homeAddress['Line4'] ?? '', 30),
		//'Address 5'          => csvField($homeAddress['Line5'] ?? '', 30),

		//'Postcode'           => csvField($homeAddress['PostCode'] ?? '', 10),

		//'Discount group 1'   => '',
		//'Discount group 2'   => '',
		//'Discount group 3'   => '',

		'Price List'         => csvField('STD', 3, true),
		
		// An explicit username override takes precedence over the staff/student default.
		'Credit Limit' => $creditLimitOverrides[$person->sso_username] ?? (
			in_array(
				$person->university_card_type,
				['US', 'FS', 'FR', 'FB', 'AV', 'DS', 'CS'],
				true
			) ? '999.99' : '0.00'
		),

		'Start Date'         => formatDateField($person->University_Card_Start_Dt),

		//'Payroll Number'     => '',
		//'Budget Account'     => ''
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

$db->upsertByName('cron_user_csv_export', date('c'));

$log->create([
	'category'    => 'cron',
	'result'      => 'success',
	'description' => 'Exported ' . $exportCount . ' user records'
]);

cliOutput(
	"CSV export complete: {$exportCount} users",
	"green"
);

?>
