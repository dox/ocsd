<?php
class ugpgvxClass {

	protected static $ug_table_name = "arch_ugcourses";
	protected static $pg_table_name = "arch_pgcourses";
	protected static $vx_table_name = "arch_vxcourses";
	
	public $ar_ugid;
	public $ar_sarkey;
	public $coursekey;
	public $options;
	public $dt_confer;
	public $dt_MA;
	public $app_type;
	public $collkey;
	public $def_entry;
	public $ic_pool;
	public $drop_cond_offer;
//	public $;
//	public $;
	
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
	
	public static function find_by_ar_sarkey($ar_sarkey = NULL) {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$ug_table_name . " ";
		$sql .= "WHERE ar_sarkey = '" . $ar_sarkey . "' ";
		$sql .= "LIMIT 1";
		
		$results = self::find_by_sql($sql);
		
		return !empty($results) ? array_shift($results) : false;
	}
	
	public static function qualtype($qtid = NULL) {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$qual_table_name . " ";
		$sql .= "WHERE qtid = '" . $qtid . "' ";
		$sql .= "LIMIT 1";
		
		$results = self::find_by_sql($sql);
		
		return !empty($results) ? array_shift($results) : false;
	}
}
?>

