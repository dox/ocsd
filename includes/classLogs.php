<?php
class Logs {
	protected static $table_name = "_logs";
	public $uid;
	public $date_created;
	public $type;
	public $result;
	public $cudid;
	public $description;
	public $username;
	public $ip;
	
	function __construct() {
	}
	
	public function all() {
		global $db;
		
		$persons = $db->orderBy('date_created', "DESC");
		$persons = $db->get(self::$table_name, 300);
		
		return $persons;
	}
	
	public function insert($type, $result, $cudid, $description) {
		global $db;
		
		$logSQLInsert = Array (
			"type" => $type,
			"result" => $result,
			"cudid" => $cudid,
			"description" => $description,
			"username" => $_SESSION["username"],
			"ip" => $_SERVER['REMOTE_ADDR']
		);
		$db->insert ('_logs', $logSQLInsert);
	}
	
	public function purge() {
		global $db;
		
		$lastPurge = $db->where("type", "purge");
		$lastPurge = $db->where("DATE(date_created)", date('Y-m-d'));
		$lastPurge = $db->getOne(self::$table_name);
		
		if (empty($lastPurge)) {
			$db->where("UNIX_TIMESTAMP(date_created) < " . strtotime('-' . logs_retention . ' days'));
			$logsDeletedCount = count($db->get(self::$table_name));
			$db->where("UNIX_TIMESTAMP(date_created) < " . strtotime('-' . logs_retention . ' days'));
			$db->delete(self::$table_name);
			
			$logInsert = (new Logs)->insert("purge","success",null,$logsDeletedCount . " logs purged");
		} else {
			// logs already purged today
		}
	}
} //end of class Person
?>