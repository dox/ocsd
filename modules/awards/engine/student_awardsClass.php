<?php
class student_awardsClass {
	protected static $table_name = "student_awards";
	
	public $sawid;
	public $studentkey;
	public $awdkey;
	public $dt_awarded;
	public $dt_from;
	public $dt_to;
	public $value;
	public $notes;
	
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
		$sql .= "WHERE sawid = '" . $uid . "' ";
		$sql .= "LIMIT 1";
		
		$results = self::find_by_sql($sql);
		
		return !empty($results) ? array_shift($results) : false;
	}
	
	public static function find_by_studentkey($studentkey = NULL) {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE studentkey = '" . $studentkey . "' ";
		$sql .= "ORDER BY dt_awarded DESC";
		
		$results = self::find_by_sql($sql);
		
		return $results;
	}
	
	public static function find_all() {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "ORDER BY dt_awarded ASC";
		
		$results = self::find_by_sql($sql);
		
		return $results;
	}
	
	public function create() {
		global $database;
		
		$sql  = "INSERT INTO " . self::$table_name . " (";
		$sql .= "studentkey, awdkey, dt_awarded, dt_from, dt_to, value, notes";
		$sql .= ") VALUES ('";
		$sql .= $database->escape_value($this->studentkey) . "', '";
		$sql .= $database->escape_value($this->awdkey) . "', '";
		$sql .= $database->escape_value($this->dt_awarded) . "', '";
		$sql .= $database->escape_value($this->dt_from) . "', '";
		$sql .= $database->escape_value($this->dt_to) . "', '";
		$sql .= $database->escape_value($this->value) . "', '";
		$sql .= $database->escape_value($this->notes) . "')";
		
		// check if the database entry was successful (by attempting it)
		if ($database->query($sql)) {
			//$this->uid = $database->insert_id();
		}
	}
	
	public function delete() {
		global $database;
		
		$sql  = "DELETE FROM " . self::$table_name . " ";
		$sql .= "WHERE sawid = '" . $database->escape_value($this->sawid) . "' ";
		$sql .= "LIMIT 1";
		
		// check if the database entry was successful (by attempting it)
		if ($database->query($sql)) {
			//$this->uid = $database->insert_id();
		}
	}
}
?>