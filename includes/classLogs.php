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
	public $logsPerPage = 100;

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

		$logs = $db->query($sql)->fetchAll();

		return $logs;
	}

	// not fixed anything below this line!
	public function allByUser($cudid = null, $username = null) {
		global $db;

		if (empty($cudid)) {
			$cudid = "never_find";
		}

		if (empty($username)) {
			$username = "never_find";
		}

		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE cudid = '" . $cudid . "'";
		$sql .= " OR username = '" . $username . "'";
		$sql .= " OR description LIKE '%{ldap:" . $username . "}%'";
		$sql .= " OR cudid LIKE '%{cudid:" . $cudid . "}%'";
		$sql .= " ORDER BY date_created DESC";

		$logs = $db->query($sql)->fetchAll();

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
	private function cleanDescription($description = null) {
		//cudid preg_replace
		$pattern = "/\{cudid:(.+?)\}/";
		$cudURL = "./index.php?n=persons_unique&cudid=$1";
		$replacement = "<a href=\"" . $cudURL . "\">$1</a> ";
		$cleanDescription = preg_replace($pattern, $replacement, $description);

		//ldap preg_replace
		$pattern = "/\{ldap:(.+?)\}/";
		$cudURL = "./index.php?n=ldap_unique&samaccountname=$1";
		$replacement = "<a href=\"" . $cudURL . "\">$1</a> ";

		$cleanDescription = preg_replace($pattern, $replacement, $cleanDescription);

		return $cleanDescription;
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
		$output .= "<td>" . $this->cleanDescription($log['description']) . " <span class=\"badge float-right " . $badgeClass . "\">" . $log['type'] . "</span></td>";

		if (!empty($log['username'])){
			$ldapLink = "<a href=\"index.php?n=ldap_unique&samaccountname=" . $log['username'] . "\">" . $log['username'] . "</a>";
		} else {
			$ldapLink = "";
		}
		$output .= "<td>" . $ldapLink . "</td>";
		$output .= "<td>" . $log['ip'] . "</td>";
		$output .= "</tr>";

		if (isset($log['date_created'])) {
			return $output;
		}
	}

	public function makeTable($logs = null) {
		$output  = "<table class=\"table table-sm table-striped\">";
		$output .= "<thead>";
		$output .= "<tr>";
		$output .= "<th scope=\"col\" style=\"width: 180px\">Date</th>";
		$output .= "<th scope=\"col\">Description</th>";
		$output .= "<th scope=\"col\" style=\"width: 140px\">Username</th>";
		$output .= "<th scope=\"col\" style=\"width: 140px\">ip</th>";
		$output .= "</tr>";
		$output .= "</thead>";
		$output .= "<tbody>";

		if (isset($_GET['offset'])) {
		 $offset = $_GET['offset'];
		} else {
		  $offset = 0;
		}

		$i = 0;
		do {
			$output .= $this->makeRow($logs[$offset+$i]);
			$i++;
		} while ($i < $this->logsPerPage);

		$output .= "</tbody>";
		$output .= "</table>";

		if (count($logs) > $this->logsPerPage) {
			$output .= $this->paginateNav(count($logs));
		}

		return $output;
	}

	private function paginateNav($logsCount = null) {
		$x = $_SERVER['REQUEST_URI'];
		$parsed = parse_url($x);
		$query = $parsed['query'];
		parse_str($query, $params);
		unset($params['offset']);
		$cleanURL = $parsed['path'] . "?" . http_build_query($params);

		if (isset($_GET['offset'])) {
		  $from = $_GET['offset'];
		  $to = $from + $this->logsPerPage;
		} else {
		  $from = 0;
		  $to = $from + $this->logsPerPage;
		}

		$output  = "<nav aria-label=\"...\">";
		$output .= "<ul class=\"pagination justify-content-center\">";

		if ($from < $this->logsPerPage) {
			$output .= "<li class=\"page-item disabled\"><a class=\"page-link\" href=\"#\" tabindex=\"-1\" aria-disabled=\"true\">Previous</a></li>";
		} else {
			$output .= "<li class=\"page-item\"><a class=\"page-link\" href=\"./index.php?n=admin_logs&offset=" . ($from - 100) . "\" tabindex=\"-1\">Previous</a></li>";
		}

		$i = 1;
		do {
			$offset = ($i * $this->logsPerPage) - $this->logsPerPage;

			$url = $cleanURL . "&offset=" . $offset;

      $logsCountBlocks = ceil($logsCount / $this->logsPerPage);

			if ($_GET['offset'] == $offset) {
				$output .= "<li class=\"page-item active\"><a class=\"page-link\" href=\"" . $url . "\">" . $i . " <span class=\"sr-only\">(current)</span></a></li>";
			} else {
				$output .= "<li class=\"page-item\"><a class=\"page-link\" href=\"" . $url . "\">" . $i . "</a></li>";
			}

			$i++;
		} while ($i <= $logsCountBlocks);

		if ($from > ($logsCount-$this->logsPerPage)) {
			$output .= "<li class=\"page-item disabled\"><a class=\"page-link\" href=\"#\" tabindex=\"-1\" aria-disabled=\"true\">Next</a></li>";
		} else {
			$output .= "<li class=\"page-item\"><a class=\"page-link\" href=\"./index.php?n=admin_logs&offset=" . ($from + $this->logsPerPage) . "\" tabindex=\"-1\">Next</a></li>";
		}
		$output .= "</ul>";
		$output .= "</nav>";

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
