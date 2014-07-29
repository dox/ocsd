<?php
class Titles {
	protected static $table_name = "titles";
	
	public $titleid;
	public $abbrv;
	public $title;
	
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
	
	public static function find_by_titleid($titleid = NULL) {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE titleid = '" . $titleid . "' ";
		$sql .= "LIMIT 1";
		
		$results = self::find_by_sql($sql);
		
		return !empty($results) ? array_shift($results) : false;
	}
	
	public static function find_by_title_name($titleName = NULL) {
		global $database;
		
		$titleName = ucwords($titleName);
		$titleName = strtr($titleName, array('.' => '', ',' => ''));
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE UPPER(abbrv) = '" . $titleName . "' ";
		$sql .= "OR UPPER(title) = '" . $titleName . "' ";
		$sql .= "LIMIT 1";
		
		$results = self::find_by_sql($sql);
		
		return !empty($results) ? array_shift($results) : false;
	}
	
	public static function find_all() {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "ORDER BY titleid ASC";
		
		$results = self::find_by_sql($sql);
		
		return $results;
	}
}
?>

