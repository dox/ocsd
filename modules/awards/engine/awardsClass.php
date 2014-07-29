<?php
class Awards {
	protected static $table_name = "awards";
	
	public $awdid;
	public $name;
	public $type;
	public $given_by;
	
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
		$sql .= "WHERE awdid = '" . $uid . "' ";
		$sql .= "LIMIT 1";
		
		$results = self::find_by_sql($sql);
		
		return !empty($results) ? array_shift($results) : false;
	}
	
	public static function find_all() {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "ORDER BY name ASC";
		
		$results = self::find_by_sql($sql);
		
		return $results;
	}
	
	public function create() {
		global $database;
		
		$sql  = "INSERT INTO " . self::$table_name . " (";
		$sql .= "name, type, given_by";
		$sql .= ") VALUES ('";
		$sql .= $database->escape_value($this->name) . "', '";
		$sql .= $database->escape_value($this->type) . "', '";
		$sql .= $database->escape_value($this->given_by) . "')";
		
		// check if the database entry was successful (by attempting it)
		if ($database->query($sql)) {
			$this->awdid = $database->insert_id();
		}
	}
	
	public function delete() {
		global $database;
		
		// see if there are any students with this award already
		$check = student_awardsClass::find_all_by_awdkey($this->awdid);
					
		// if there are no students with this award, it's safe to delete
		if (count($check) == 0) {
			$sql  = "DELETE FROM " . self::$table_name . " ";
			$sql .= "WHERE awdid = '" . $database->escape_value($this->awdid) . "' ";
			$sql .= "LIMIT 1";
			
			// check if the database entry was successful (by attempting it)
			if ($database->query($sql)) {
				//$this->uid = $database->insert_id();
			}
		} else {
			$log = new Logs;
			$log->notes			= "Cannot delete award type when there are " . count($check) . " student(s) with the award.";
			$log->prev_value	= $this->sawid;
			$log->type			= "error";
			$log->create();
		}
	}
		
	public function inlineUpdate($awardUID = NULL, $key, $value) {
		global $database;
		
		$sql  = "UPDATE " . self::$table_name . " ";
		$sql .= "SET " . $database->escape_value($key) . " = '" . $database->escape_value($value) . "' ";
		$sql .= "WHERE awdid = '" . $database->escape_value($awardUID) . "' ";
		$sql .= "LIMIT 1";
		
		$results = self::find_by_sql($sql);
		
		$log = new Logs();
		$log->type = "success";
		$log->title = "Award Updated";
		$log->notes = "Award UID " . $this->awdid . " was updated";
		$log->create();
	}
}
?>