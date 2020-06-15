<?php
class Log extends Logs {
	function __construct($uid) {
		global $db;
		
		$result = $db->where("uid", $uid);
		$result = $db->getOne(self::$table_name);
		
		foreach ($result AS $key => $value) {
			$this->$key = $value;
		}
	}
	
	public function tableRow () {
		$logDate = date('Y-m-d H:i:s', strtotime($this->date_created));
	
		if ($this->result == "success") {
			$class = "table-success";
		} else if ($this->result == "error") {
			$class = "table-danger";
		} else if ($this->result == "info") {
			$class = "table-primary";
		} else if ($this->result == "debug") {
			$class = "table-info";
		} else {
			$class = "";
		}
		
		if (in_array($_SESSION['username'], admin_usernames)) {
			$output  = "<tr class=\"" . $class . "\">";
			$output .= "<td>" . $logDate . " </td>";
			$output .= "<td>" . $this->description . " <span class=\"badge badge-info float-right\">" . $this->type . "</span></td>";
			$output .= "<td>" . "<a href=\"index.php?n=persons_unique&cudid=" . $this->cudid . "\">" . $this->cudid . "</a></td>";
			$output .= "<td>" . $this->username . "</td>";
			$output .= "<td>" . $this->ip . "</td>";
			$output .= "</tr>";
		} else {
			$output  = "<tr class=\"blurry\">";
			$output .= "<td>" . generateRandomString($logDate) . " </td>";
			$output .= "<td>" . generateRandomString($this->description) . " <span class=\"badge blurry badge-info float-right\">" . generateRandomString($this->type) . "</span></td>";
			$output .= "<td>" . generateRandomString($this->cudid) . "</td>";
			$output .= "<td>" . generateRandomString($this->username) . "</td>";
			$output .= "<td>" . generateRandomString($this->ip) . "</td>";
			$output .= "</tr>";
		}
		return $output;
	}
} //end of class Log
?>