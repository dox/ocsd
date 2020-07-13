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

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " ORDER BY date_created DESC";

		$logs = $db->query($sql, 'test', 'test')->fetchAll();

		return $logs;
	}

	// not fixed anything below this line!
	public function allByUser($cudid = null, $username = null) {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name;

		if ($cudid != null) {
			$sql .= " WHERE cudid = '" . $cudid . "'";
			if ($username != null) {
				$sql .= " OR username = '" . $username . "'";
			}
		} elseif ($username != null) {
			$sql .= " WHERE username = '" . $username . "'";
			if ($username != null) {
				$sql .= " OR cudid = '" . $cudid . "'";
			}
		}

		$sql .= " ORDER BY date_created DESC";

		$logs = $db->query($sql, 'test', 'test')->fetchAll();

		return $logs;
	}

	public function allByType($type = null, $ageLimitDay = 7) {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE type = '" . $type . "'";
		$sql .= " AND date_created BETWEEN CURDATE() - INTERVAL " . $ageLimitDay . " DAY AND CURDATE()";
		$sql .= " ORDER BY date_created DESC";
		$logs = $db->query($sql)->fetchAll();

		return $logs;
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

		$sql = "INSERT INTO " . self::$table_name . " (" . implode(", ",array_keys($logSQLInsert)) . ") VALUES ('" . implode("', '", $logSQLInsert) . "')";

		$insert = $db->query($sql);
	}

	public function purge() {
		global $db;

		$sql = "SELECT * FROM " . self::$table_name . " WHERE type = 'purge' AND DATE(date_created) = '" . date('Y-m-d') . "' LIMIT 1";
		$lastPurge = $db->query($sql)->fetchArray();

		if (empty($lastPurge)) {
			$sql = "SELECT * FROM " . self::$table_name . " WHERE UNIX_TIMESTAMP(date_created) < '" . strtotime('-' . logs_retention . ' days') . "'";
			$logsToDelete = $db->query($sql)->fetchAll();

			if (count($logsToDelete) > 0) {
				$sql = "DELETE FROM " . self::$table_name . " WHERE UNIX_TIMESTAMP(date_created) < '" . strtotime('-' . logs_retention . ' days') . "'";
				$logsToDelete = $db->query($sql)->fetchAll();

				$logInsert = (new Logs)->insert("purge","success",null,count($logsToDelete) . " log(s) purged");
			}
		}
	}
} //end of class Person
?>
