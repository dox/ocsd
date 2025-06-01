<?php
class Logs {
	public static $table_name = '_logs';
	
	public function create($array = null) {
		global $db;  // Assuming $db is the instance of your Database class
		
		// Sanitize description to prevent issues like SQL injection
		$description = $array['description'];
		$description = str_replace("'", "\'", $description); // Optional, if necessary
		$description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); // Better escape special characters
		
		// Prepare the SQL query with placeholders
		$sql = "INSERT INTO " . self::$table_name . " (date_created, type, result, cudid, ldap, description, username, ip) 
				VALUES (:date_created, :type, :result, :cudid, :ldap, :description, :username, :ip)";
		
		// Use the query method from the Database class to execute the query
		$params = [
			':date_created' => date('c'),
			':type' => $array['type'],
			':result' => $array['result'],
			':cudid' => $array['cudid'],
			':ldap' => $array['ldap'],
			':description' => $array['description'],
			':username' => $_SESSION['username'],
			':ip' => ip2long($_SERVER['REMOTE_ADDR'])
		];
		
		// Execute the query and return the result
		return $db->query($sql, $params);
	}
	
	public function getAll() {
		global $db, $settings;
	
		// Get the maximum log age from settings
		$maximumLogsAge = date('Y-m-d', strtotime('-' . $settings->value('logs_retention') . ' days'));
	
		// Prepare the SQL query
		$sql  = "SELECT date_created, type, result, cudid, description, username, INET_NTOA(ip) AS ip 
				 FROM " . self::$table_name . " 
				 WHERE DATE(date_created) > :maximumLogsAge 
				 ORDER BY date_created DESC";
		
		// Execute the query with the bound parameter
		$results = $db->query($sql, ['maximumLogsAge' => $maximumLogsAge]);
		
		return $results;
	}
	
	public function table($logs = null) {
		$table  = "<table class=\"table\">";
		$table .= "<thead>";
		$table .= "<tr>";
		$table .= "<th scope=\"col\">Date</th>";
		$table .= "<th scope=\"col\">IP</th>";
		$table .= "<th scope=\"col\">Username</th>";
		$table .= "<th scope=\"col\">Type</th>";
		$table .= "<th scope=\"col\">Result</th>";
		$table .= "<th scope=\"col\">CUDID</th>";
		$table .= "<th scope=\"col\">LDAP</th>";
		$table .= "<th scope=\"col\">Description</th>";
		$table .= "</tr>";
		$table .= "</thead>";
		$table .= "<tbody>";
		
		foreach ($logs as $log) {
			$table .= self::tableRow($log);
		}
		
		$table .= "</tbody>";
		$table .= "</table>";
		
		return $table;
	}
	
	private function tableRow($log = null) {
		// Initialize the row class based on the result
		switch ($log['result']) {
			case 'error':
				$class = 'table-danger';
				break;
			case 'warning':
				$class = 'table-warning';
				break;
			case 'success':
				$class = 'table-success';
				break;
			default:
				$class = '';  // No class if result is something else
				break;
		}
	
		// Return the table row as a string, directly building it
		return '<tr class="' . $class . '">'
			. '<th scope="row">' . htmlspecialchars($log['date_created']) . '</th>'
			. '<td>' . htmlspecialchars($log['ip']) . '</td>'
			. '<td>' . htmlspecialchars($log['username']) . '</td>'
			. '<td>' . htmlspecialchars($log['type']) . '</td>'
			. '<td>' . htmlspecialchars($log['result']) . '</td>'
			. '<td>' . htmlspecialchars($log['cudid']) . '</td>'
			. '<td>' . htmlspecialchars($log['ldap']) . '</td>'
			. '<td>' . htmlspecialchars($log['description']) . '</td>'
			. '</tr>';
	}
	
	public function purge() {
		global $db;
		
		$logsAge = setting('logs_retention');
		
		$sql = "DELETE FROM _logs WHERE date_created < NOW() - INTERVAL " . $logsAge . " DAY";
		
		return $db->query($sql);
	}

}

$log = new Logs();