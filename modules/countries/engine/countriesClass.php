<?php
class Countries {
	protected static $table_name = "countries";
	
	public $cyid;
	public $abbrv;
	public $short;
	public $formal;
	public $nationality;
	public $eu_member;
	public $elec_roll;
	public $commonwealth;
	
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
		$sql .= "WHERE cyid = '" . $uid . "' ";
		$sql .= "LIMIT 1";
		
		$results = self::find_by_sql($sql);
		
		return !empty($results) ? array_shift($results) : false;
	}
	
	public static function find_all() {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "ORDER BY short ASC";
		
		$results = self::find_by_sql($sql);
		
		return $results;
	}
	
	public function fullDisplayName($googleMaps = false) {
		$url = "http://maps.google.co.uk/maps?q=" . $this->formal;
		
		if ($googleMaps == true) {
			$output = "<a href =\"" . $url . "\">" . $this->formal . "</a>";
		} else {
			$output = $this->formal;
		}
		
		
		return $output;
	}
	
	public function bodcard($link = true) {
		if ($this->univ_cardno == "") {
			$bodcard = "UNKNOWN";
		} else {
			$bodcard = $this->univ_cardno;
		}
		
		if ($link == true) {
			if ($bodcard == "UNKNOWN") {
				$labelClass = "";
				$url = "#";
			} else {
				if (date('Y-m-d') < date('Y-m-d', strtotime($this->dt_card_exp))){
					$labelClass = "label-info";
					$subMessage = "";
				} else {
					$labelClass = "label-important";
					$subMessage = " (card expired)";
				}
				
				$url = "#";
			}
			
			$bodcard = "<span class=\"label " . $labelClass . "\">" . $bodcard . "</a>";
			$bodcard = "<a href=\"" . $url . "\">" . $bodcard . "</a>" . $subMessage;
		}
		
		return $bodcard;
	}
	
	public function imageURL($fullImgTag = false) {
		$url = "uploads/2703628.jpg";
		
		if ($fullImgTag == true) {
			$output  = "<img src=\"" . $url . "\" class=\"img-polaroid pull-right clearfix\" >";
		} else {
			$output = $url;
		}
		
		return $output;
	}

}
?>