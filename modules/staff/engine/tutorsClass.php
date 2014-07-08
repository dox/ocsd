<?php
class Tutors {
	protected static $table_name = "tutors";
	
	public $tutid;
	public $titlekey;
	public $initials;
	public $forenames;
	public $surname;
	public $identifier;
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
	
	///////////////////////
	
	public static function find_by_uid($uid = NULL) {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE tutid = '" . $uid . "' ";
		$sql .= "LIMIT 1";
		
		$results = self::find_by_sql($sql);
		
		return !empty($results) ? array_shift($results) : false;
	}
	
	public static function find_all() {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "ORDER BY tutid ASC";
		
		$results = self::find_by_sql($sql);
		
		return $results;
	}
	
	public function title() {
		$title = Titles::find_by_titleid($this->titlekey);
		
		return $title->title;
		
	}
	
	public function fullDisplayName() {
		$firstname = $this->forenames;
		$initials = $this->initials;
		$familyname = $this->surname;
		
		return $this->title() . " " . $firstname . " " . $familyname;
	}
	
	public function id() {
		return $this->tutid;
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
			$output  = "<img id=\"userPhoto\" src=\"" . $pathToFile . "\" class=\"img-polaroid \" style=\"max-height: 300px;\">";
		} else {
			$output = $pathToFile;
		}
		
		return $output;
	}
	
	public function inlineUpdate($tutorid = NULL, $key, $value) {
		global $database;
		
		$sql  = "UPDATE " . self::$table_name . " ";
		$sql .= "SET " . $database->escape_value($key) . " = '" . $database->escape_value($value) . "' ";
		$sql .= "WHERE tutid = '" . $database->escape_value($tutorid) . "' ";
		$sql .= "LIMIT 1";
		
		$results = self::find_by_sql($sql);
	}
	
	public function create() {
		global $database;
		
		$sql  = "INSERT INTO " . self::$table_name . " (";
		$sql .= "titlekey, initials, forenames, surname, identifier, photo";
		$sql .= ") VALUES ('";
		$sql .= $database->escape_value($this->titlekey) . "', '";
		$sql .= $database->escape_value($this->initials) . "', '";
		$sql .= $database->escape_value($this->forenames) . "', '";
		$sql .= $database->escape_value($this->surname) . "', '";
		$sql .= $database->escape_value($this->identifier) . "', '";
		$sql .= $database->escape_value($this->photo) . "')";
		
		// check if the database entry was successful (by attempting it)
		if ($database->query($sql)) {
			$log = new Logs;
			$log->notes			= "New tutor created";
			$log->prev_value	= $this->tutid;
			$log->type			= "create";
			$log->create();
			
			$this->tutid = $database->insert_id();
		}
	}
}
?>

