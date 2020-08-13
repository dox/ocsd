<?php
class Person extends Persons {
	function __construct($cudid = null) {
		global $db;
		$sql = "SELECT * FROM " . self::$table_name . " WHERE cudid = '" . $cudid . "'";
		$person = $db->query($sql, 'test', 'test')->fetchArray();

		foreach ($person AS $key => $value) {
			$this->$key = $value;
		}

		$ldapPerson = new LDAPPerson($this->sso_username, $this->oxford_email);

		if (count($ldapPerson)) {
			$this->ldap_samaccountname = $ldapPerson->samaccountname;
			$this->ldap_isEnabled = $ldapPerson->isEnabled();
		}
	}

	public function bodcardDaysLeft() {
		$now = time(); // or your date as well
		$your_date = strtotime($this->University_Card_End_Dt);
		$datediff = round(($your_date - $now) / (60 * 60 * 24));

		return $datediff;
	}
	public function makeListItem() {
		if (obscure == true) {
			$obscure = " obscure";
		}

		$cudURL = "./index.php?n=persons_unique&cudid=" . $this->cudid;
		$ldapURL = "./index.php?n=ldap_unique&samaccountname=" . $this->ldap_samaccountname;

		$output  = "<div class=\"card\">";
		$output .= "<div class=\"card-body\">";
		$output .= "<div class=\"row row-sm align-items-center\">";
		$output .= "<div class=\"col-auto\">";
		$output .= $this->avatar();
		$output .= "</div>";
		$output .= "<div class=\"col\">";
		$output .= "<h3 class=\"mb-0 text-truncate" . $obscure . "\"><a href=\"" . $cudURL . "\">" . $this->FullName . "</a></h3>";
		$output .= "<div class=\"text-muted text-h5\">" . $this->bodcardType() . "</div>";
		$output .= "<div class=\"text-muted text-h5\">SSO: <span class=\"" . $obscure . "\">" . $this->sso_username . "</span></div>";
		$output .= "</div>";
		//$output .= "<div class=\"col-auto lh-1 align-self-start\">";
		//$output .= "<span class=\"badge bg-gray-lt\">";
		//$output .= $this->sso_username;
		//$output .= "</span>";
		//$output .= "</div>";
		$output .= "</div>";
		$output .= "<div class=\"row align-items-center mt-4\">";
		$output .= "<div class=\"col\">";
		$output .= "<div>";

		$datediff = $this->bodcardDaysLeft();
		if ($datediff > 365) {
			$width = "100%";
			$class = "bg-green";
		} elseif ($datediff <= 365 && $datediff > 100) {
			$width = $datediff . "%";
			$class = "bg-blue";
		} elseif ($datediff <= 100 && $datediff > 30) {
			$width = $datediff . "%";
			$class = "bg-yellow";
		} elseif ($datediff <= 30 && $datediff > 0) {
			$width = $datediff . "%";
			$class = "bg-red";
		} else {
			$width = "0%";
			$class = "bg-grey";
		}

		$output .= "<div class=\"d-flex mb-1 align-items-center lh-1\">";
		$output .= "<div class=\"text-h5 font-weight-bolder m-0\"><span class=\"" . $obscure . "\">" . $this->barcode7 . "</span></div>";
		$output .= "<span class=\"ml-auto text-h6 strong\">" . $datediff . " days left</span>";
		$output .= "</div>";
		$output .= "<div class=\"progress progress-sm\">";
		$output .= "<div class=\"progress-bar " . $class . "\" style=\"width: " . $width . "\" role=\"progressbar\" aria-valuenow=\"84\" aria-valuemin=\"0\" aria-valuemax=\"100\">";
		$output .= "<span class=\"sr-only\">" . $width . "% Complete</span>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";

		$output .= "<div class=\"col-auto\">";
		$output .= "<div class=\"btn-list\">";
		$output .= "<a href=\"" . $ldapURL . "\" class=\"btn btn-white btn-sm\">";
		$output .= "LDAP";
		$output .= "</a>";
		$output .= "<a href=\"" . $cudURL . "\" class=\"btn btn-white btn-sm\">";
		$output .= "Profile";
		$output .= "</a>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";

		return $output;
	}

	public function cardTypeBadge() {
		$cardType = $this->university_card_type;

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

	public function bodcardType() {
		$cardType = $this->university_card_type;
		$types = bodcardTypes();

		return $types[$cardType];
	}

	public function avatar() {
		$imgSrc = $this->photo();

		$output  = "<a href=\"index.php?n=persons_unique&cudid=" . $this->cudid . "\" class=\"circle\">";

		$class = "avatar rounded-lg avatar-lg";
		$style = "background-image: url(" . $imgSrc . ")";
		if (obscure == true) {
			$class = $class . " obscureImg";
		}

		$output .= "<span alt=\"this is a test\" class=\"" . $class . "\" style=\"" . $style . "\">";

		if ($this->ldap_isEnabled == true) {
			$class = "bg-success";
		} else {
			$class = "bg-danger";
		}

		$output .= "<span class=\"badge " . $class . "\"></span>";

		$output .="</span>";
		$output .= "</a>";

		return $output;
	}

	public function photo() {
		$imgSrc = "photos/UAS_UniversityCard-" . $this->university_card_sysis . ".jpg";

		if (!file_exists($imgSrc)) {
			$imgSrc = "images/blank_avatar.png";
		}

		return $imgSrc;
	}

	public function bodcardBadge($displayText = false) {
		if (strtotime($this->University_Card_End_Dt) < strtotime("now")) {
			$bodcardCardBadeClass = "badge-danger";
			$bodcardCardText = "Expired: ";
		} else if (strtotime($this->University_Card_End_Dt) < strtotime("+30 days") && strtotime($this->University_Card_End_Dt) > strtotime("now")) {
			$bodcardCardBadeClass = "badge-warning";
			$bodcardCardText = "Expires Soon: " . date('Y-m-d', strtotime($this->University_Card_End_Dt));
		} else if (strtotime($this->University_Card_End_Dt) > strtotime("now")) {
			$bodcardCardBadeClass = "badge-success";
			$bodcardCardText = "Expires: " . date('Y-m-d', strtotime($this->University_Card_End_Dt));
		} else {
			$bodcardCardBadeClass = "badge-dark";
			$bodcardCardText = "Error: " . date('Y-m-d', strtotime($this->University_Card_End_Dt));
		}

		if ($displayText !== true) {
			$bodcardCardText = "";
		}

		$bodcardOutput  = "<span class=\"badge badge-pill " . $bodcardCardBadeClass . "\">";
		$bodcardOutput .=  $this->barcode7;

		if ($displayText == true) {
			$bodcardOutput .= " <span class=\"badge badge-pill badge-light\">" . $bodcardCardText . "</span>";
		}
		$bodcardOutput .= "</span>";

		return $bodcardOutput;
	}

	public function nationality() {
		global $db;

		$sql  = "SELECT * FROM Nationalities";
		$sql .= " WHERE cudid = '" . $this->cudid . "'";

		$nationality = $db->query($sql, 'test', 'test')->fetchArray();

		return $nationality['NatName'];
	}

	public function address($AddressTyp = "C") {
		global $db;

		$sql  = "SELECT * FROM Addresses";
		$sql .= " WHERE cudid = '" . $this->cudid . "'";
		$sql .= " AND AddressTyp = '" . $AddressTyp . "'";
		$sql .= " ORDER BY LastUpdateDt DESC";
		$sql .= " LIMIT 1";

		$addresses = $db->query($sql, 'test', 'test')->fetchArray();

		return $addresses;
	}

	public function addresses() {
		global $db;

		$sql  = "SELECT * FROM Addresses";
		$sql .= " WHERE cudid = '" . $this->cudid . "'";

		$addresses = $db->query($sql, 'test', 'test')->fetchAll();

		return $addresses;
	}

	public function contactDetails() {
		global $db;

		$sql  = "SELECT * FROM ContactDetails";
		$sql .= " WHERE cudid = '" . $this->cudid . "'";

		$contactDetails = $db->query($sql, 'test', 'test')->fetchAll();

		return $contactDetails;
	}
} //end of class Person

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

	public $ldap_samaccountname;

	private $studentArrayTypes = array('GT', 'GR', 'UG', 'VR', 'PT', 'VD', 'VV', 'VC');

	public function all() {
		global $db;

		$sql = "SELECT * FROM " . self::$table_name;
		$persons = $db->query($sql, 'test', 'test')->fetchAll();

		return $persons;
	}

	public function allStudents() {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE university_card_type IN ('" . implode("', '", $this->studentArrayTypes) . "')";

		$persons = $db->query($sql, 'test', 'test')->fetchAll();

		return $persons;
	}

	public function allStudentsByCohort($unit_set_cd = null) {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE university_card_type IN ('" . implode("', '", $this->studentArrayTypes) . "')";
		$sql .= " AND unit_set_cd = '" . $unit_set_cd . "'";

		$persons = $db->query($sql, 'test', 'test')->fetchAll();

		return $persons;
	}

	public function allStaff() {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE university_card_type NOT IN ('" . implode("', '", $this->studentArrayTypes) . "')";

		$persons = $db->query($sql, 'test', 'test')->fetchAll();

		return $persons;
	}

	public function search($searchTerm = null, $limit = null) {
		global $db;

		if (!empty($searchTerm)) {
			$sql  = "SELECT * FROM " . self::$table_name;
			$sql .= " WHERE lastname LIKE '%" . $searchTerm . "%'";
			$sql .= " OR sso_username LIKE '%" . $searchTerm . "%'";
			$sql .= " OR cudid LIKE '%" . $searchTerm . "%'";
			$sql .= " OR barcode7 LIKE '%" . $searchTerm . "%'";
			$sql .= " OR oxford_email LIKE '%" . $searchTerm . "%'";

			if (!$limit == null) {
				$sql .= " LIMIT " . $limit;
			}

			$persons = $db->query($sql, 'test', 'test')->fetchAll();

			if (count($persons) > 0) {
				return $persons;
			} else {
				return false;
			}
		} else {
			return false;
		}


	}
} //end of class Persons



function bodcardTypes() {
	$bodcardTypeArray = array(
		"MC" => "Congregation",
		"US" => "University Staff",
		"FS" => "Retiree",
		"FR" => "Retiree",
		"FB" => "Retiree",
		"AV" => "Academic Visitor",
		"DS" => "Departmental Staff",
		"CS" => "College Staff",
		"GT" => "Postgraduate",
		"GR" => "Postgraduate",
		"UG" => "Undergraduate",
		"VR" => "Visiting/Recognized Student",
		"PT" => "Part Time (unmatriculated)",
		"VD" => "Departmental Visiting Student",
		"VV" => "Departmental Visiting Student",
		"VC" => "College Visiting Student",
		"CL" => "Cardholder (not a University member)",
		"CB" => "Cardholder (not a University member)",
		"VA" => "Virtual Access",
		"VX" => "Virtual Access",
		"leaver" => "Leaver"
	);

	return $bodcardTypeArray;
}
?>
