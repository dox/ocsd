<?php
class ArchExams {
	protected static $arch_exams_table = "arch_exams";
	protected static $exampapers_table_name = "arch_exam_papers";
	
	public $ar_examid;
	public $ar_sarkey;
	public $etkey;
	public $dt_exam;
	public $qc_key;
	public $notes;
	
	public $ar_papid;
	public $ar_examkey;
	public $paperno;
	public $title;
	public $grade;
	
	public $edid;
	public $name;
	
	public $qcid;
	public $ugclass;
	public $pgclass;
	public $exclass;
	
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
		
		/*
		$sql  = "SELECT student_exams.*, exam_types.*, qual_classes.name AS qualname FROM " . self::$studentexams_table_name . ", exam_types, qual_classes ";
		//$sql .= "INNER JOIN " . self::$examtypes_table_name . " ON student_exams.etkey = exam_types.etid ";
		//$sql .= "INNER JOIN " . self::$qualclasses_table_name . " ON student_exams.qckey = qual_classes.qcid ";
		$sql .= "WHERE student_exams.etkey = exam_types.etid ";
		$sql .= "AND student_exams.qckey = qual_classes.qcid ";
		$sql .= "AND student_exams.studentkey = '" . $studentkey . "' ";
		*/
		$sql  = "SELECT * FROM " . self::$arch_exams_table . " ";
		$sql .= "INNER JOIN qual_classes ON arch_exams.qckey = qual_classes.qcid ";
		$sql .= "WHERE ar_sarkey = '" . $ar_sarkey . "' ";
		$results = self::find_by_sql($sql);
		
		return $results;
	}
	
	public static function find_papers_by_examkey($examkey = NULL) {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$exampapers_table_name . " ";
		$sql .= "WHERE ar_examkey = '" . $examkey . "' ";
		
		$results = self::find_by_sql($sql);
		
		return $results;
	}
	
	public static function examType($etid = NULL) {
		global $database;
		
		$sql  = "SELECT * FROM exam_types ";
		$sql .= "WHERE etid = '" . $etid . "' ";
		$sql .= "LIMIT 1";
		
		$results = self::find_by_sql($sql);
		
		return !empty($results) ? array_shift($results) : false;
	}
	
}
?>