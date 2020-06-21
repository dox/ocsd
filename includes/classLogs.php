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

	public function all_by_user($cudid = null, $username = null) {
		global $db;

		if ($cudid != null) {
			$persons = $db->where('cudid', $cudid);
		}
		if ($username != null) {
			$persons = $db->orWhere('username', $username);
		}

		$persons = $db->orderBy('date_created', "DESC");
		$persons = $db->get(self::$table_name, 300);

		return $persons;
	}

	public function insert($type, $result, $cudid, $description, $username = null) {
		global $db;

		if ($username == null) {
			$username = $_SESSION["username"];
		}
		$logSQLInsert = Array (
			"type" => $type,
			"result" => $result,
			"cudid" => $cudid,
			"description" => $description,
			"username" => $username,
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

			if ($logsDeletedCount > 0) {
				$db->where("UNIX_TIMESTAMP(date_created) < " . strtotime('-' . logs_retention . ' days'));
				$db->delete(self::$table_name);

				$logInsert = (new Logs)->insert("purge","success",null,$logsDeletedCount . " logs purged");
			}
		}
	}
} //end of class Person
?>
