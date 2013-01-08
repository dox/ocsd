<?php
class Grads {
	protected static $table_name = "undergrads";
	protected static $table_name2 = "postgrads";
	protected static $qual_table_name = "qual_types";
	
	public $ugid;
	public $studentkey;
	public $coursekey;
	public $qtkey;
	public $qskey;
	public $options;
	public $qckey;
	public $dt_confer;
	public $dt_MA;
	public $oxford_appno;
	public $ucas_appno;
	public $app_type;
	public $collkey;
	public $def_entry;
	public $ic_pool;
	public $drop_cond_offer;
	
	//postgrad only
	public $pgid;
	public $thesis_title;
	public $fackey;
	public $dt_submit;
	public $dt_lv_supp;
	public $dt_transfer;
	public $cont_dphil;
	public $cont_mphil;
	
	public $qtid;
	public $abbrv;
	public $name;
	public $oxqual;
	public $stype;
	
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
	
	public static function find_by_studentkey($studentkey = NULL) {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "INNER JOIN " . self::$qual_table_name . " ON undergrads.qtkey = qual_types.qtid ";
		$sql .= "WHERE studentkey = '" . $studentkey . "' ";
		$sql .= "LIMIT 1";
		
		$results = self::find_by_sql($sql);
		
		if (count($results) < 1) {
			$sql  = "SELECT * FROM " . self::$table_name2 . " ";
			$sql .= "INNER JOIN " . self::$qual_table_name . " ON postgrads.qtkey = qual_types.qtid ";
			$sql .= "WHERE studentkey = '" . $studentkey . "' ";
			$sql .= "LIMIT 1";
			
			$results = self::find_by_sql($sql);
		}
		return !empty($results) ? array_shift($results) : false;
	}
	
	
}
?>

