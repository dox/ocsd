<?php
class ExternalIds implements \IteratorAggregate {
	protected $cudid;
	protected $records = [];
	
	public function __construct(string $cudid) {
		global $db;
		
		$this->cudid = $cudid;
		
		$sql = "SELECT * FROM ExternalIds WHERE cudid = :cudid";
		$this->records = $db->get($sql, [':cudid' => $cudid]);
	}
	
	public function getIterator(): \Traversable {
		return new \ArrayIterator($this->records);
	}
	
	public function all(): array {
		return $this->records;
	}
}

?>