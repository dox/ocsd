<?php
class Persons {
	public static $table_name = 'Person';
	
	public $studentPGTypes = array("UG");
    public $studentUGTypes = array("PG", "GT", "GR");
    public $studentVSTypes = array("VS", "VD", "VV", "VR");
    public $studentTypes;
    public $staffTypes = array("CS");
    
    public function __construct() {
        $this->studentTypes = array_merge(
            $this->studentPGTypes,
            $this->studentUGTypes,
            $this->studentVSTypes
        );
    }

	public function all() {
		global $db;
		
		$sql  = "SELECT cudid FROM " . self::$table_name;
		
		$results = $db->get($sql);
		
		$persons = [];
		foreach ($results as $result) {
			$persons[] = new Person($result['cudid']);
		}
		
		return $persons;
	}
	
	public function allStudents() {
		global $db;
		
		// Build placeholders for each type
		$placeholders = [];
		$params = [];
		foreach ($this->studentTypes as $i => $type) {
			$ph = ":type$i";
			$placeholders[] = $ph;
			$params[$ph] = $type;
		}
		
		$sql  = "SELECT cudid FROM " . self::$table_name;
		$sql .= " WHERE university_card_type IN (" . implode(",", $placeholders) . ")";
		
		$results = $db->get($sql, $params);
		
		$persons = [];
		foreach ($results as $result) {
			$persons[] = new Person($result['cudid']);
		}
		
		return $persons;
	}
}
