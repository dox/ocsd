<?php
class Addresses implements \IteratorAggregate {
	protected $cudid;
	protected $records = [];
	
	public function __construct(string $cudid) {
		global $db;
		
		$this->cudid = $cudid;
		
		$sql = "SELECT *
		FROM (
			SELECT *,
				   ROW_NUMBER() OVER (
					   PARTITION BY AddressTyp
					   ORDER BY LastUpdateDt DESC
				   ) AS rn
			FROM Addresses
			WHERE cudid = :cudid
		) ranked
		WHERE rn = 1";
		$sql = "SELECT * FROM Addresses WHERE cudid = :cudid ORDER BY AddressSeq ASC";
		$this->records = $db->get($sql, [':cudid' => $cudid]);
	}
	
	public function getIterator(): \Traversable {
		return new \ArrayIterator($this->records);
	}
	
	public function all(): array {
		return $this->records;
	}
	
	public function getHomeAddress(): array {
		global $db;
		
		$sql = "SELECT * FROM Addresses WHERE cudid = :cudid AND AddressTyp = 'H' ORDER BY AddressSeq DESC LIMIT 1";
		$address = $db->get($sql, [':cudid' => $this->cudid]);
		
		if (!$address) {
			return array();
		}
		return $address[0];
	}
	
	public function getTermAddress(): array {
		global $db;
		
		$sql = "SELECT * FROM Addresses WHERE cudid = :cudid AND AddressTyp = 'T' ORDER BY AddressSeq DESC LIMIT 1";
		$address = $db->get($sql, [':cudid' => $this->cudid]);
		
		if (!$address) {
			return array();
		}
		return $address[0];
	}
	
	public function getContactAddress(): array {
		global $db;
		
		$sql = "SELECT * FROM Addresses WHERE cudid = :cudid AND AddressTyp = 'C' ORDER BY AddressSeq DESC LIMIT 1";
		$address = $db->get($sql, [':cudid' => $this->cudid]);
		
		if (!$address) {
			return array();
		}
		return $address[0];
	}
	
	public function addressCard($address = null) {
		// Initialize the output
		$output  = "<div class=\"card mb-3\">";
		$output .= "<div class=\"card-body\">";
		$output .= "<h5 class=\"card-title\">Address Type: " . htmlspecialchars($address['AddressTyp']) . "</h5>";
		$output .= "<p class=\"card-subtitle text-muted\">Last updated: " . date('Y-m-d', strtotime($address['LastUpdateDt'])) . " by " . htmlspecialchars($address['AddressEntity']) . "</p>";
		$output .= "</div>";
		
		// List group for address details
		$output .= "<ul class=\"list-group list-group-flush\">";
		
		// Render address lines
		$output .= "<li class=\"list-group-item\">";
		$output .= $this->renderAddressLines($address);
		$output .= "</li>";
	
		// Render additional info if exists
		$output .= $this->renderAdditionalInfo($address);
	
		$output .= "</ul>";
		$output .= "</div>";
		
		return $output;
	}
	
	// Helper function to render address lines
	private function renderAddressLines($address) {
		$lines = [
			$address["Line1"],
			$address["Line2"],
			$address["Line3"],
			$address["Line4"],
			$address["Line5"],
			$address["City"],
			$address["PostCode"],
			$address["State"],
			$address["County"],
			$address["AddressCtryDesc"]
		];
		
		// Filter out empty lines and join them with <br />
		return implode("<br />", array_filter($lines));
	}
	
	// Helper function to render additional contact info
	private function renderAdditionalInfo($address) {
		$info = [];
		if ($address["AddressEmail"]) {
			$info[] = htmlspecialchars($address["AddressEmail"]);
		}
		if ($address["TelNo"]) {
			$info[] = htmlspecialchars($address["TelNo"]);
		}
		if ($address["MobileNo"]) {
			$info[] = htmlspecialchars($address["MobileNo"]);
		}
	
		// Return as list items
		$output = '';
		foreach ($info as $item) {
			$output .= "<li class=\"list-group-item\">" . $item . "</li>";
		}
	
		return $output;
	}
}

?>