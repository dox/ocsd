<?php
class Addresses {
	protected static $table_name = "student_address";
	
	public $addrid;
	public $studentkey;
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
	public $defalt;			// yes / no
	public $atkey;
	
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
		$addressType = AddressTypes::find_by_uid($this->atkey);
		
		if ($this->line1) {
			$address .= $this->line1 . "<br />";
			$googleSearchString = $this->line1;
		}
		if ($this->line2) {
			$address .= $this->line2 . "<br />";
			$googleSearchString = $googleSearchString . "+" . $this->line2;
		}
		if ($this->line3) {
			$address .= $this->line3 . "<br />";
			$googleSearchString = $googleSearchString . "+" . $this->line3;
		}
		if ($this->line4) {
			$address .= $this->line4 . "<br />";
			$googleSearchString = $googleSearchString . "+" . $this->line4;
		}
		if ($this->town) {
			$address .= $this->town . "<br />";
			$googleSearchString = $googleSearchString . "+" . $this->town;
		}
		if ($this->county) {
			$address .= $this->county . "<br />";
			$googleSearchString = $googleSearchString . "+" . $this->county;
		}
		if ($this->postcode) {
			$address .= $this->postcode . "<br />";
			$googleSearchString = $googleSearchString . "+" . $this->postcode;
		}
		
		$thumb_googleMapsURL = "http://maps.googleapis.com/maps/api/staticmap?center=" . $googleSearchString . "&zoom=15&size=200x200";
		$googleMapsURL = "http://maps.google.com/?q=" . $googleSearchString;
		
		$output  = "<div class=\"col-sm-6 col-md-4\">";
		$output .= "<div class=\"thumbnail\">";
		$output .= "<a href=\"" . $googleMapsURL . "\"><img src=\"" . $thumb_googleMapsURL . "\"></a>";
		$output .= "<div class=\"caption\">";
		$output .= "<h3>" . $addressType->type . "</h3>";
		$output .= "<p>" . $address . "</p>";
		$output .= "<p><a href=\"" . $googleMapsURL . "\" class=\"btn btn-primary btn-block\" role=\"button\"><i class=\"fa fa-map-marker\"></i> Google Maps</a> <a href=\"modules/addresses/views/addressDymo.php?uid=" . $this->addrid . "\" class=\"btn btn-default btn-block\" role=\"button\"><i class=\"fa fa-print\"></i> Dymo Print</a></p>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		
		return $output;
	}
	
	public function create() {
		global $database;
		
		$sql  = "INSERT INTO " . self::$table_name . " (";
		$sql .= "studentkey, line1, line2, line3, line4, town, county, postcode, cykey, phone, email, mobile, fax, defalt, atkey";
		$sql .= ") VALUES ('";
		$sql .= $database->escape_value($this->studentkey) . "', '";
		$sql .= $database->escape_value($this->line1) . "', '";
		$sql .= $database->escape_value($this->line2) . "', '";
		$sql .= $database->escape_value($this->line3) . "', '";
		$sql .= $database->escape_value($this->line4) . "', '";
		$sql .= $database->escape_value($this->town) . "', '";
		$sql .= $database->escape_value($this->county) . "', '";
		$sql .= $database->escape_value($this->postcode) . "', '";
		$sql .= $database->escape_value($this->cykey) . "', '";
		$sql .= $database->escape_value($this->phone) . "', '";
		$sql .= $database->escape_value($this->email) . "', '";
		$sql .= $database->escape_value($this->mobile) . "', '";
		$sql .= $database->escape_value($this->fax) . "', '";
		$sql .= $database->escape_value($this->defalt) . "', '";
		$sql .= $database->escape_value($this->atkey) . "')";
		
		echo $sql;
		
		// check if the database entry was successful (by attempting it)
		if ($database->query($sql)) {
			//$this->uid = $database->insert_id();
		}
	}
}
?>

