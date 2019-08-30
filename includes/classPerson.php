<?php
class Person {
	protected static $table_name = "Person";
	
	function test() {
		global $database;
		
		return "Andrew Breakspear";
	}
	
	function allPersonsCount() {
		global $db;
		
		$persons = $db->get(self::$table_name);
		$personsCount = $db->count;
		
		return $personsCount;
	}
	
	function allPersons() {
		global $db;
		
		$persons = $db->orderBy('lastname', "ASC");
		$persons = $db->get(self::$table_name);
		
		return $persons;
	}
	
	function fullName() {
		$output = "unknown";
		
		return $output;
;	}
	
} //end of class Person
?>