<?php
class Settings {
	public static $table_name = '_settings';
	
	public function value($name = null) {
		global $db;
	
		// Prepare the SQL query with placeholders
		$sql  = "SELECT * FROM " . self::$table_name . " 
				 WHERE name = :name 
				 OR uid = :name";
	
		// Execute the query with the bound parameter
		$setting = $db->query($sql, ['name' => $name]);
	
		// Return the value if the setting exists, otherwise return null
		return $setting ? $setting[0]['value'] : null;
	}
	
	public function getAll() {
		global $db;
	
		// Prepare the SQL query
		$sql  = "SELECT * 
				 FROM " . self::$table_name . " 
				 ORDER BY name DESC";
		
		// Execute the query
		$settings = $db->query($sql);
		
		return $settings;
	}
}

$settings = new Settings();