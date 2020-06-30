<?php
class Persons {
	protected static $table_name = "Person";
	public $cudid;
	public $sits_student_code;
	public $oss_student_number;
	public $firstname;
	public $middlenames;
	public $lastname;
	public $FullName;
	public $known_as;
	public $prev_surnm;
	public $titl_cd;
	public $initials;
	public $gnd;
	public $dob;
	public $dom_cd;
	public $dom_hesa_cd;
	public $dom_name;
	public $birth_ctry_cd;
	public $birth_ctry_name;
	public $alt_email;
	public $oxford_email;
	public $sso_username;
	public $university_card_status;
	public $university_card_sysis;
	public $university_card_type;
	public $barcode;
	public $barcode7;
	public $University_Card_Start_Dt;
	public $University_Card_End_Dt;
	public $MiFareID;
	public $PaxonID;
	public $dept_cd;
	public $dept_desc;
	public $college_cd;
	public $co_owning_dept_code;
	public $div_cd;
	public $div_desc;
	public $rout_cd;
	public $rout_name;
	public $course_join_status;
	public $course_status;
	public $course_block;
	public $crs_start_dt;
	public $crs_exp_end_dt;
	public $crs_end_dt;
	public $mode_of_attendance;
	public $unit_set_cd;
	public $award_aim;
	public $universitycard_college;
	public $universitycard_type_expanded;
	public $universitycard_number_cards_issued;
	public $internal_tel;
	public $universitycard_isoiec_14443_uid;
	public $bodleian_mifare_id;
	public $bodleian_isoiec_14443_uid;

	function __construct() {
	}

	public function test() {
		return $this->cudid;
	}

	function allPersonsCount() {
		global $db;

		$persons = $db->get(self::$table_name);
		$personsCount = $db->count;

		return $personsCount;
	}

	public function all() {
		global $db;

		$persons = $db->orderBy('lastname', "ASC");
		$persons = $db->get(self::$table_name);
		return $persons;
	}

	public function search($searchTerm = null, $limit = null) {
		global $db;

		$persons = $db->orderBy('lastname', "ASC");
		$persons = $db->orWhere('sso_username', $searchTerm);
		$persons = $db->orWhere('cudid', $searchTerm);
		$persons = $db->orWhere('barcode7', $searchTerm);
		$persons = $db->orWhere('oxford_email', $searchTerm);

		if (!$limit == null) {
			$persons = $db->get(self::$table_name, $limit);
		} else {
			$persons = $db->get(self::$table_name);
		}

		return $persons;
	}

	public function navsearch($searchTerm = null, $limit = null) {
		global $db;

		$persons = $db->orderBy('lastname', "ASC");
		$persons = $db->orWhere('fullname', "%" . $searchTerm . "%", 'like');
		$persons = $db->orWhere('sso_username', "%" . $searchTerm . "%", 'like');
		$persons = $db->orWhere('cudid', "%" . $searchTerm . "%", 'like');
		$persons = $db->orWhere('barcode7', "%" . $searchTerm . "%", 'like');
		$persons = $db->orWhere('oxford_email', "%" . $searchTerm . "%", 'like');

		if (!$limit == null) {
			$persons = $db->get(self::$table_name, $limit);
		} else {
			$persons = $db->get(self::$table_name);
		}

		return $persons;
	}
} //end of class Persons

function nationality($cudid = null) {
	global $db;
	
	$dbOutput = $db->where("cudid", $cudid);
	$dbOutput = $db->getOne("Nationalities");

	return $dbOutput['NatName'];
}

function bodcardTypes() {
	$bodcardTypeArray = array(
		"MC" => "Congregation (from Register of Congregation)",
		"US" => "University Staff (on payroll)",
		"FS" => "Retiree (on University Pension) approved by a dept or college",
		"FR" => "Retiree (on University pension) approved by Pensions",
		"FB" => "Retiree (on University pension) approved by Pensions (no service entitlements)",
		"AV" => "Academic Visitor",
		"DS" => "Departmental Staff",
		"CS" => "College Staff",
		"GT" => "Postgraduate (from SITS)",
		"GR" => "Postgraduate (from SITS)",
		"UG" => "Undergraduate (from SITS)",
		"VR" => "Visiting/Recognized Student (from SITS)",
		"PT" => "Part Time (Continuing Education - unmatriculated)",
		"VD" => "Departmental Visiting Student (@dept.ox.ac.uk email address)",
		"VV" => "Departmental Visiting Student (@visiting.ox.ac.uk email address)",
		"VC (1)" => "College Visiting Student (@college.ox.ac.uk email address)",
		"CL" => "Cardholder (unit member, not a University member)",
		"CB" => "Cardholder (unit member, not a University member)",
		"VA" => "Virtual Access (neither unit nor University member)",
		"VX" => "Virtual Access (neither unit nor University member)",
		"leaver" => "Non-card status: leaver	Students in the 11 months after their University Card has expired (neither unit nor University member)"
	);

	return $bodcardTypeArray;
}

function cardTypeBadge($cardType = null) {
	if ($cardType == "GT" || $cardType == "GR" || $cardType == "PT") {
		$class = "badge-primary";
	} else if ($cardType == "UG" ) {
		$class = "badge-success";
	} else if ($cardType == "VR" || $cardType == "VD" || $cardType == "VV" || $cardType == "VC") {
		$class = "badge-warning";
	} else if ($cardType == "CS") {
		$class = "badge-info";
	} else {
		$class = "badge-secondary";
	}

	$output  = "<a href=\"index.php?n=card_types\" class=\"badge " . $class . "\">" . $cardType . "</a>";

	return $output;
}

function bodcardBadge($barcode7 = null, $University_Card_End_Dt = null, $displayText = false) {
	if (strtotime($University_Card_End_Dt) < strtotime("now")) {
		$bodcardCardBadeClass = "badge-danger";
		$bodcardCardText = "Expired: ";
	} else if (strtotime($University_Card_End_Dt) < strtotime("+30 days") && strtotime($University_Card_End_Dt) > strtotime("now")) {
		$bodcardCardBadeClass = "badge-warning";
		$bodcardCardText = "Expires Soon: " . date('Y-m-d', strtotime($University_Card_End_Dt));
	} else if (strtotime($University_Card_End_Dt) > strtotime("now")) {
		$bodcardCardBadeClass = "badge-success";
		$bodcardCardText = "Expires: " . date('Y-m-d', strtotime($University_Card_End_Dt));
	} else {
		$bodcardCardBadeClass = "badge-dark";
		$bodcardCardText = "Error: " . date('Y-m-d', strtotime($University_Card_End_Dt));
	}

	if ($displayText !== true) {
		$bodcardCardText = "";
	}

	$bodcardOutput  = "<span class=\"badge badge-pill " . $bodcardCardBadeClass . "\">";
	$bodcardOutput .=  $barcode7;

	if ($displayText == true) {
		$bodcardOutput .= " <span class=\"badge badge-pill badge-light\">" . $bodcardCardText . "</span>";
	}
	$bodcardOutput .= "</span>";

	return $bodcardOutput;
}

function photoAvatar() {
	$imgSrc = "../photos/UAS_UniversityCard-" . $this->university_card_sysis . ".jpg\"";

	$output  = "<a href=\"index.php?n=persons_unique&cudid=" . $this->cudid . "\" class=\"circle\">";
	//$output  = "<img src=\"" . $imgSrc . "\" class=\"rounded-circle\" alt=\"...\">";
	$output .= "<img height=\"100\" width=\"100\" alt=\"100x100\" src=\"" . $imgSrc . "\">";
	$output .= "</a>";

	return $output;
}

function photoCard($university_card_sysis = null) {
	$imgSrc = "../photos/UAS_UniversityCard-" . $university_card_sysis . ".jpg\"";

	$output  = "<div class=\"card float-right\" style=\"width: 18rem;\">";
	$output .= "<img src=\"" . $imgSrc . "\" class=\"card-img-top\" alt=\"...\">";
	//$output .= "<div class=\"card-body\">";
	//$output .= bodcardBadge(true);
	//$output .= "</div>";
	$output .= "</div>";

	return $output;
}
?>
