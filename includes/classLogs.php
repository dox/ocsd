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

	public $data;

	function __construct() {
	}

	public function all($limit = null) {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " ORDER BY date_created DESC";

		if ($limit != null) {
			$sql .= " LIMIT " . $limit;
		}

		$logs = $db->query($sql)->fetchAll();

		return $logs;
	}

	public function paginatedAll($from = 0, $to = 100) {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " ORDER BY date_created DESC";
		$sql .= " LIMIT " . $from . ", " . $to;

		if ($limit != null) {
			$sql .= " LIMIT " . $limit;
		}

		$logs = $db->query($sql)->fetchAll();

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

	public function all2() {
		global $db;

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " ORDER BY date_created DESC";

		//$logs = $db->query($sql)->fetchAll();
		$logs = $db->ObjectBuilder()->getOne("_logs");

		return $logs;
	}

	private function makeRow($log = null) {
		$logDate = date('Y-m-d H:i:s', strtotime($log['date_created']));

		if ($log['result'] == "success") {
			$class = "table-success";
		} else if ($log['result'] == "warning") {
			$class = "table-warning";
		} else if ($log['result'] == "error") {
			$class = "table-danger";
		} else if ($log['result'] == "info") {
			$class = "table-primary";
		} else if ($log['result'] == "debug") {
			$class = "table-info";
		} else {
			$class = "";
		}

		if ($log['type'] == "ldap") {
			$badgeClass = "bg-indigo";
		} else if ($log['type'] == "logon" || $log['type'] == "logoff") {
			$badgeClass = "bg-green";
		} else if ($log['type'] == "view") {
			$badgeClass = "bg-lime";
		} else if ($log['type'] == "cron") {
			$badgeClass = "bg-blue";
		} else if ($log['type'] == "purge") {
			$badgeClass = "bg-pink";
		} else if ($log['type'] == "email") {
			$badgeClass = "bg-yellow";
		} else {
			$badgeClass = "";
		}

		$output  = "<tr class=\"" . $class . "\">";
		$output .= "<td>" . $logDate . " </td>";
		$output .= "<td>" . $log['description'] . " <span class=\"badge float-right " . $badgeClass . "\">" . $log['type'] . "</span></td>";

		if (!empty($log['cudid'])){
			$cudLink = "<a href=\"index.php?n=persons_unique&cudid=" . $log['cudid'] . "\">" . $log['cudid'] . "</a>";
		} else {
			$cudLink = "";
		}
		$output .= "<td>" . $cudLink . "</td>";

		if (!empty($log['username'])){
			$ldapLink = "<a href=\"index.php?n=ldap_unique&samaccountname=" . $log['username'] . "\">" . $log['username'] . "</a>";
		} else {
			$ldapLink = "";
		}
		$output .= "<td>" . $ldapLink . "</td>";
		$output .= "<td>" . $log['ip'] . "</td>";
		$output .= "</tr>";

		return $output;
	}

	public function makeTable($logs = null) {
		$output  = "<table class=\"table table-sm table-striped\">";
		$output .= "<thead>";
		$output .= "<tr>";
		$output .= "<th scope=\"col\" style=\"width: 180px\">Date</th>";
		$output .= "<th scope=\"col\">Description</th>";
		$output .= "<th scope=\"col\" style=\"width: 330px\">CUDID</th>";
		$output .= "<th scope=\"col\" style=\"width: 140px\">Username</th>";
		$output .= "<th scope=\"col\" style=\"width: 140px\">ip</th>";
		$output .= "</tr>";
		$output .= "</thead>";
		$output .= "<tbody>";

		foreach ($logs AS $log) {
			$output .= $this->makeRow($log);
		}

		$output .= "</tbody>";
		$output .= "</table>";

		return $output;
	}

	private function logTypeBadge() {
		if ($log['type'] == "ldap") {
			$badgeClass = "bg-indigo";
		} else if ($log['type'] == "logon" || $log['type'] == "logoff") {
			$badgeClass = "bg-green";
		} else if ($log['type'] == "view") {
			$badgeClass = "bg-lime";
		} else if ($log['type'] == "cron") {
			$badgeClass = "bg-blue";
		} else if ($log['type'] == "purge") {
			$badgeClass = "bg-pink";
		} else if ($log['type'] == "email") {
			$badgeClass = "bg-yellow";
		} else {
			$badgeClass = "";
		}

		return $badge;
	}
} //end of class Logs
?>
