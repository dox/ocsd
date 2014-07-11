<?php
class Logs {
	protected static $table_name = "ocsd_logs";
	
	public $uid;
	public $date_stamp;
	public $username;
	public $student_id;
	public $notes;
	public $prev_value;
	public $updated_value;
	public $type;
	public $ip;
	
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
		
		$sql  = "SELECT uid, date_stamp, username, student_id, notes, prev_value, updated_value, type, INET_NTOA(ip) AS ip ";
		$sql .= "FROM " . self::$table_name . " ";
		$sql .= "WHERE uid = '" . $uid . "' ";
		$sql .= "LIMIT 1";
		
		$results = self::find_by_sql($sql);
		
		return !empty($results) ? array_shift($results) : false;
	}
	
	public static function find_all() {
		global $database;
		
		$sql  = "SELECT uid, date_stamp, username, student_id, notes, prev_value, updated_value, type, INET_NTOA(ip) AS ip ";
		$sql .= "FROM " . self::$table_name . " ";
		$sql .= "ORDER BY date_stamp DESC";
		
		$results = self::find_by_sql($sql);
		
		return $results;
	}
	
	public function create() {
		global $database;
		
		if (!isset($this->date_stamp)) {
			$this->date_stamp = date("Y-m-d H:i:s");
		}
		
		if (!isset($this->username)) {
			$this->username = $_SESSION['username'];
		}
		
		if (!isset($this->ip)) {
			$this->username = $_SESSION['username'];
		}
		
		$sql  = "INSERT INTO " . self::$table_name . " (";
		$sql .= "date_stamp, username, student_id, notes, prev_value, updated_value, type, ip";
		$sql .= ") VALUES ('";
		$sql .= $database->escape_value($this->date_stamp) . "', '";
		$sql .= $database->escape_value($this->username) . "', '";
		$sql .= $database->escape_value($this->student_id) . "', '";
		$sql .= $database->escape_value($this->notes) . "', '";
		$sql .= $database->escape_value($this->prev_value) . "', '";
		$sql .= $database->escape_value($this->updated_value) . "', '";
		$sql .= $database->escape_value($this->type) . "', ";
		$sql .= "INET_ATON('" . $_SERVER['REMOTE_ADDR'] . "'))";
		
		// check if the database entry was successful (by attempting it)
		if ($database->query($sql)) {
			//$this->uid = $database->insert_id();
		}
	}
	
	public function delete() {
		global $database;
		
		$sql  = "DELETE FROM " . self::$table_name . " ";
		$sql .= "WHERE uid = '" . $database->escape_value($this->uid) . "' ";
		$sql .= "LIMIT 1";
		
		// check if the database entry was successful (by attempting it)
		if ($database->query($sql)) {
			//$this->uid = $database->insert_id();
		}
	}
	
	public function purge_old_logs() {
		global $database;
		
		$sql  = "DELETE FROM " . self::$table_name . " ";
		$sql .= "WHERE DATE_SUB(CURDATE(),INTERVAL 180 DAY) >= date_stamp ";
		
		// check if the database entry was successful (by attempting it)
		if ($database->query($sql)) {
			//$this->uid = $database->insert_id();
		}
	}
}
?>