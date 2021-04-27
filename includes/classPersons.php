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

	public function suspendedPersons() {
		global $db;

		$sqlCurrent  = "SELECT cudid FROM Enrolments";
		$sqlCurrent .= " WHERE Status = 'Suspended'";

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE cudid IN (" . $sqlCurrent . ")";

		$persons = $db->query($sql)->fetchAll();

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
