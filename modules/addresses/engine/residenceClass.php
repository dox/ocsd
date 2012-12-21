<?php
class ResidenceAddresses {
	protected static $table_name = "student_resaddress";
	
	public $resid;
	public $studentkey;
	public $radkey;
	public $roomno;
	public $phone;
	public $dt_updated;
	
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
		$sql .= "WHERE studentkey = '" . $uid . "' ";
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
		$resAddress = ResAddress::find_by_uid($this->radkey);
		 
		$output  = "<address class=\"well\">";
		
		if ($resAddress->line1) {
			$output .= $resAddress->line1 . "<br />";
		}
		if ($resAddress->line2) {
			$output .= $resAddress->line2 . "<br />";
		}
		if ($resAddress->town) {
			$output .= $resAddress->town . "<br />";
		}
		if ($resAddress->postcode) {
			$output .= $resAddress->postcode . "<br />";
		}
		if ($resAddress->phone) {
			$output .= $resAddress->phone . "<br />";
		}
		
		if ($this->roomno) {
			$output .= "Room Number: " . $this->roomno . "<br />";
		}
		if ($this->phone) {
			$output .= "Phone: " . $this->phone . "<br />";
		}
		$output .= "</address>";

		return $output;
	}
}
?>

