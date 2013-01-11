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
	
	///////////////////////
	
	public static function find_by_uid($uid = NULL) {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE studentid = '" . $uid . "' ";
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
	
	public function fullDisplayName() {
		$title = Titles::find_by_titleid($this->titlekey);
		$firstname = $this->forenames;
		
		if ($this->initials) {
			$initials = str_replace(" ", ". ", $this->initials);
			$initials = $initials . ". ";
		} else {
			$initials = "";
		}
		
		$familyname = $this->surname;
		
		return $title->title . " " . $firstname . " " . $initials . $familyname;
	}
	
	public function bodcard($link = true) {
		$subMessage = "";
		
		if ($this->univ_cardno == "") {
			$bodcard = "UNKNOWN";
		} else {
			$bodcard = $this->univ_cardno;
		}
		
		if ($link == true) {
			if ($bodcard == "UNKNOWN") {
				$labelClass = "";
				$url = "#";
			} else {
				if (date('Y-m-d') < convertToDateString($this->dt_card_exp)) {
					$labelClass = "label-info";
					$subMessage = "";
				} else {
					$labelClass = "label-important";
					$subMessage = " <i>(card expired)</i>";
				}
				
				$url = "#";
			}
			
			$bodcard = "<span class=\"label " . $labelClass . "\">" . $bodcard . "</a>";
			$bodcard = "<a href=\"" . $url . "\">" . $bodcard . "</a>" . $subMessage;
		}
		
		return $bodcard;
	}
	
	public function imageURL($fullImgTag = false) {
		$pathToFiles = "uploads/";
		$pathToFile = $pathToFiles . $this->univ_cardno . ".png";
		
		if (!file_exists($pathToFile)) {
			$pathToFile = "img/no_user_photo.png";
		}
		//$url = "uploads/2703628.jpg";
		
		if ($fullImgTag == true) {
			$output  = "<img src=\"" . $pathToFile . "\" class=\"img-polaroid \" style=\"max-height: 300px;\">";
			$output .= "<hr />";
		} else {
			$output = $pathToFile;
		}
		
		return $output;
	}

}
?>
