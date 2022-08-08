<?php
class Persons {
	protected static $table_name = "Person";
	private $studentArrayTypes = array('GT', 'GR', 'UG', 'VR', 'PT', 'VD', 'VV', 'VC');
	
	public function all() {
		global $db;
	
		$sql = "SELECT * FROM " . self::$table_name;
		$persons = $db->query($sql)->fetchAll();
	
		return $persons;
	}
	
	public function allStudents() {
		global $db;
	
		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE university_card_type IN ('" . implode("', '", $this->studentArrayTypes) . "')";
	
		$persons = $db->query($sql)->fetchAll();
	
		return $persons;
	}
	
	public function allStudentsiPlicit() {
		global $db;
		
		$sql  = "SELECT * FROM Student";
		//$sql .= " WHERE university_card_type IN ('" . implode("', '", $this->studentArrayTypes) . "')";
		
		$persons = $db->query($sql)->fetchAll();
		
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
	
	public function allStaff() {
		global $db;
	
		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE university_card_type NOT IN ('" . implode("', '", $this->studentArrayTypes) . "')";
	
		$persons = $db->query($sql)->fetchAll();
	
		return $persons;
	}
	
	public function search($searchTerm = null, $limit = null) {
		global $db;
	
		if (!empty($searchTerm)) {
			$sql  = "SELECT * FROM " . self::$table_name;
			$sql .= " WHERE fullname LIKE '%" . $searchTerm . "%'";
			$sql .= " OR sso_username LIKE '%" . $searchTerm . "%'";
			$sql .= " OR cudid LIKE '%" . $searchTerm . "%'";
			$sql .= " OR barcode7 LIKE '%" . $searchTerm . "%'";
			$sql .= " OR oxford_email LIKE '%" . $searchTerm . "%'";
			$sql .= " OR sits_student_code LIKE '" . $searchTerm . "%'";
	
			if (!$limit == null) {
				$sql .= " LIMIT " . $limit;
			}
	
			$persons = $db->query($sql)->fetchAll();
	
			if (count($persons) > 0) {
				return $persons;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	public function searchStrict($searchTerm = null) {
		global $db;
	
		if (!empty($searchTerm)) {
			$sql  = "SELECT * FROM " . self::$table_name;
			$sql .= " WHERE sso_username = '" . $searchTerm . "'";
			$sql .= " OR cudid = '" . $searchTerm . "'";
			$sql .= " OR barcode7 = '" . $searchTerm . "'";
			$sql .= " OR oxford_email = '" . $searchTerm . "'";
			$sql .= " LIMIT 1";
	
			$persons = $db->query($sql)->fetchAll();
	
			if (count($persons) > 0) {
				return $persons;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	public function allStudentsByCohort($unit_set_cd = null) {
		global $db;
	
		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE university_card_type IN ('" . implode("', '", $this->studentArrayTypes) . "')";
		$sql .= " AND unit_set_cd = '" . $unit_set_cd . "'";
	
		$persons = $db->query($sql)->fetchAll();
	
		return $persons;
	}
}	

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