<?php
class Addresses {
	protected static $table_name = "student_address";
	
	public $addrid;
	public $studentkey;		// 'UG','PG','VX'
	public $type;
	public $line1;
	public $line2;
	public $line3;
	public $line4;
	public $town;
	public $county;
	public $postcode;
	public $cykey;
	public $phone;
	public $email;
	public $mobile;
	public $fax;
	public $defalt;
	
	public $radkey;
	
	
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
		$sql .= "WHERE addrid = '" . $uid . "' ";
		$sql .= "LIMIT 1";
		
		$results = self::find_by_sql($sql);
		
		return !empty($results) ? array_shift($results) : false;
	}
	
	public static function find_all_by_student($studentUID) {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE studentkey = '" . $studentUID . "' ";
		$sql .= "ORDER BY studentkey ASC";
		
		$results = self::find_by_sql($sql);
		
		return $results;
	}
	
	public function displayAddress() {
		$output  = "<address class=\"well\">";
		
		$output .= "<a href=\"modules/addresses/views/addressDymo.php?uid=" . $this->addrid . "\"><i class=\"icon-print pull-right\"></i></a>";
		
		if ($this->line1) {
			$output .= $this->line1 . "<br />";
		}
		if ($this->line2) {
			$output .= $this->line2 . "<br />";
		}
		if ($this->line3) {
			$output .= $this->line3 . "<br />";
		}
		if ($this->line4) {
			$output .= $this->line4 . "<br />";
		}
		if ($this->town) {
			$output .= $this->town . "<br />";
		}
		if ($this->county) {
			$output .= $this->county . "<br />";
		}
		if ($this->postcode) {
			$output .= $this->postcode . "<br />";
		}
		$output .= "</address>";

		return $output;
	}
}
?>
