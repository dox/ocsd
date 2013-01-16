<?php
class ArchGrads {

	protected static $qual_table_name = "qual_types";
	
	public $ar_sacmid;
	public $ar_studentkey;
	public $acinstkey;
	public $collkey;
	public $dt_start;
	public $dt_end;
	public $isSEH;
	public $ar_sarid;
	public $ar_sacmkey;
	public $yr_det;
	public $qtkey;
	public $qlkey;
	public $qskey;
	public $qckey;
	public $grade;
	
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
	
	public static function find_academic_record_by_studentkey($studentkey = NULL) {
		global $database;
		
		$sql  = "SELECT * FROM arch_acadinst_map ";
		$sql .= "INNER JOIN arch_academic_record ON arch_acadinst_map.ar_sacmid = arch_academic_record.ar_sacmkey ";
		$sql .= "WHERE arch_acadinst_map.ar_studentkey = '" . $studentkey . "' ";
		$sql .= "AND collkey = '29' ";
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

