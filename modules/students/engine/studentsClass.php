<?php
class Students {
	protected static $table_name = "students";
	
	public $studentid;
	public $st_type;		// 'UG','PG','VX'
	public $titlekey;
	public $initials;
	public $forenames;
	public $prefname;
	public $surname;
	public $prev_surname;
	public $suffix;
	public $marital_status;
	public $dt_birth;
	public $gender;
	public $nationality;
	public $birth_cykey;
	public $resid_cykey;
	public $citiz_cykey;
	public $optout;
	public $family;
	public $eng_lang;
	public $occup_bg;
	public $disability;
	public $ethkey;
	public $rskey;
	public $cskey;
	public $relkey;
	public $rckey;
	public $SSNref;
	public $oss_pn;
	public $fee_status;
	public $univ_cardno;
	public $dt_card_exp;
	public $course_yr;
	public $notes;
	public $email1;
	public $email2;
	public $mobile;
	public $dt_start;
	public $dt_end;
	public $dt_matric;
	public $oucs_id;
	public $yr_app;
	public $yr_entry;
	public $yr_cohort;
	public $dt_created;
	public $dt_lastmod;
	public $who_mod;
	public $photo;
	
	public static function find_by_sql($sql="") {
		global $database;
		$result_set = $database->query($sql);
		$object_array = array();
		while ($row = $database->fetch_array($result_set)) {
			global $database;
			$object_array[] = self::instantiate($row);
		}
		return $object_array;
	}
	
	private static function instantiate($record) {
		$object = new self;
		
		foreach ($record as $attribute=>$value) {
			if ($object->has_attribute($attribute)) {
				$object->$attribute = $value;
			}
		}
		return $object;
	}
	
	private function has_attribute($attribute) {
		// get_object_vars returns as associative array with all attributes
		// (incl. private ones!) as the keys and their current values as the value
		$object_vars = get_object_vars($this) ;
		
		// we don't care about the value, we just want to know if the key exists
		// will return true or false
		return array_key_exists($attribute, $object_vars);
	}
	
	public function object_vars() {
        return get_object_vars($this);
    }
	
	///////////////////////
	
	public static function find_by_uid($uid = NULL, $lookupValue = "studentid") {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE " . $lookupValue . " = '" . $uid . "' ";
		$sql .= "LIMIT 1";
		
		$results = self::find_by_sql($sql);
		
		return !empty($results) ? array_shift($results) : false;
	}
	
	public static function find_all() {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "ORDER BY studentid ASC";
		
		$results = self::find_by_sql($sql);
		
		return $results;
	}
	
	public function title() {
		$title = Titles::find_by_titleid($this->titlekey);
		
		return $title->title;
		
	}
	
	public function fullDisplayName() {
		if ($this->initials) {
			$initials = str_replace(" ", ". ", $this->initials);
			$initials = $initials . ". ";
		} else {
			$initials = "";
		}
		
		$firstname = $this->forenames;
		$familyname = $this->surname;
		
		return $this->title() . " " . $firstname . " " . $familyname;
	}
	
	public function bodcard($link = true) {
		$toolTip = "";
		
		if ($this->univ_cardno == "") {
			$bodcard = "UNKNOWN";
		} else {
			$bodcard = $this->univ_cardno;
		}
		
		$bodcardOutput = "";
		
		if ($link == true) {
			if ($bodcard == "UNKNOWN") {
				$labelClass = "label label-default";
				$url = "#";
			} else {
				if (date('U') < date('U', strtotime($this->dt_card_exp))) {
					$labelClass = "label label-primary";
					$toolTip = "Expires on " . convertToDateString($this->dt_card_exp);
				} else {
					$labelClass = "label label-warning";
					$toolTip = "Expired on " . convertToDateString($this->dt_card_exp);
				}
				
				// not used
				$url = "index.php?m=students&n=user.php&studentid=" . $this->studentid;
			}
			
			//$bodcardOutput  = "<span class=\"label " . $labelClass . "\">";
			$bodcardOutput  = "<span class=\"" . $labelClass . "\" data-toggle=\"tooltip\" title=\"" . $toolTip . "\">";
			$bodcardOutput .= $bodcard;
			$bodcardOutput .= "</span>";
		} else {
			$bodcardOutput .= $bodcard;
		}
		
		return $bodcardOutput;
	}
	
	public function id() {
		return $this->studentid;
	}
	
	public function inlineUpdate($studentid = NULL, $key, $value) {
		global $database;
		
		$sql  = "UPDATE students ";
		$sql .= "SET " . $database->escape_value($key) . " = '" . $database->escape_value($value) . "' ";
		$sql .= "WHERE studentid = '" . $database->escape_value($studentid) . "' ";
		$sql .= "LIMIT 1";
		
		$results = self::find_by_sql($sql);
	}
	
	public function imageURL($fullImgTag = false) {
		$pathToFiles = "uploads/userphoto/";
		$pathToFile = $pathToFiles . $this->photo;
		
		if (!isset($this->photo)) {
			$pathToFile = "img/no_user_photo.png";
		}
		if (!file_exists($pathToFile)) {
			$pathToFile = "img/no_user_photo.png";
		}
		//$url = "uploads/2703628.jpg";
		
		if ($fullImgTag == true) {
			$output  = "<img id=\"userPhoto\" src=\"" . $pathToFile . "\" class=\"img-thumbnail \" alt=\"Photograph of " . $this->fullDisplayName() . "\" style=\"max-height: 300px;\">";
		} else {
			$output = $pathToFile;
		}
		
		return $output;
	}
	
	public function getNextAvailableID() {
		//rubbish function to get the next ID availble as the current database doesn't use autoincrement
		global $database;
		
		$sql = "SELECT studentid FROM students ORDER BY studentid DESC LIMIT 0, 1";
		
		$result = self::find_by_sql($sql);
		$result = !empty($result) ? array_shift($result) : false;
		
		return $result->studentid + 1;
	}
	
	public function alreadyExistCheck() {
		// returns true if the user already exists, false if the user doesn't exist
		global $database;
		
		// check if a user already exists with this OSS or OUCS id
		$sql  = "SELECT * FROM " . self::$table_name  ." ";
		$sql .= "WHERE oucs_id = '" . $this->oucs_id . "' ";
		$sql .= "OR oss_pn = '" . $this->oss_pn . "' ";
		
		$results = self::find_by_sql($sql);
		
		if (count($results) == 0) {
			return false;
		} else {
			return true;
		}
	}
	
	public function create() {
		global $database;
		
		$sql  = "INSERT INTO " . self::$table_name . " (";
		$sql .= "studentid, st_type, titlekey, initials, forenames, prefname, surname, prev_surname, suffix, marital_status, dt_birth, gender, nationality, birth_cykey, resid_cykey, citiz_cykey, optout, family, eng_lang, occup_bg, disability, ethkey, rskey, cskey, relkey, rckey, SSNref, oss_pn, fee_status, univ_cardno, dt_card_exp, course_yr, notes, email1, email2, mobile, dt_start, dt_end, dt_matric, oucs_id, yr_app, yr_entry, yr_cohort, dt_created, dt_lastmod, who_mod, photo";
		$sql .= ") VALUES ('";
		$sql .= $this->getNextAvailableID() . "', '";
		$sql .= $database->escape_value($this->st_type) . "', '";
		$sql .= $database->escape_value($this->titlekey) . "', '";
		$sql .= $database->escape_value($this->initials) . "', '";
		$sql .= $database->escape_value($this->forenames) . "', '";
		$sql .= $database->escape_value($this->prefname) . "', '";
		$sql .= $database->escape_value($this->surname) . "', '";
		$sql .= $database->escape_value($this->prev_surname) . "', '";
		$sql .= $database->escape_value($this->suffix) . "', '";
		$sql .= $database->escape_value($this->marital_status) . "', '";
		$sql .= $database->escape_value($this->dt_birth) . "', '";
		$sql .= $database->escape_value($this->gender) . "', '";
		$sql .= $database->escape_value($this->nationality) . "', '";
		$sql .= $database->escape_value($this->birth_cykey) . "', '";
		$sql .= $database->escape_value($this->resid_cykey) . "', '";
		$sql .= $database->escape_value($this->citiz_cykey) . "', '";
		$sql .= $database->escape_value($this->optout) . "', '";
		$sql .= $database->escape_value($this->family) . "', '";
		$sql .= $database->escape_value($this->eng_lang) . "', '";
		$sql .= $database->escape_value($this->occup_bg) . "', '";
		$sql .= $database->escape_value($this->disability) . "', '";
		$sql .= $database->escape_value($this->ethkey) . "', '";
		$sql .= $database->escape_value($this->rskey) . "', '";
		$sql .= $database->escape_value($this->cskey) . "', '";
		$sql .= $database->escape_value($this->relkey) . "', '";
		$sql .= $database->escape_value($this->rckey) . "', '";
		$sql .= $database->escape_value($this->SSNref) . "', '";
		$sql .= $database->escape_value($this->oss_pn) . "', '";
		$sql .= $database->escape_value($this->fee_status) . "', '";
		$sql .= $database->escape_value($this->univ_cardno) . "', '";
		$sql .= $database->escape_value($this->dt_card_exp) . "', '";
		$sql .= $database->escape_value($this->course_yr) . "', '";
		$sql .= $database->escape_value($this->notes) . "', '";
		$sql .= $database->escape_value($this->email1) . "', '";
		$sql .= $database->escape_value($this->email2) . "', '";
		$sql .= $database->escape_value($this->mobile) . "', '";
		$sql .= $database->escape_value($this->dt_start) . "', '";
		$sql .= $database->escape_value($this->dt_end) . "', '";
		$sql .= $database->escape_value($this->dt_matric) . "', '";
		$sql .= $database->escape_value($this->oucs_id) . "', '";
		$sql .= $database->escape_value($this->yr_app) . "', '";
		$sql .= $database->escape_value($this->yr_entry) . "', '";
		$sql .= $database->escape_value($this->yr_cohort) . "', '";
		$sql .= $database->escape_value($this->dt_created) . "', '";
		$sql .= $database->escape_value($this->dt_lastmod) . "', '";
		$sql .= $database->escape_value($this->who_mod) . "', '";
		$sql .= $database->escape_value($this->photo) . "')";
		
		$check = $this->alreadyExistCheck();
		
		// if the user doesn't exist, go ahead and create them
		if ($check == false) {
			// check if the database entry was successful (by attempting it)
			if ($database->query($sql)) {
				$this->uid = $database->insert_id();
				
				$log = new Logs;
				$log->notes			= "User '". $this->forenames . " " . $this->surname . "' was created";
				$log->student_id	= $this->uid;
				$log->type			= "create";
				$log->create();
			}
		} else {
			$log = new Logs;
			$log->notes			= "Unable to create user '". $this->forenames . " " . $this->surname . "' as someone with that OSS/OUSC ID already exists";
			$log->type			= "error";
			$log->create();
		}
	}
}
?>

