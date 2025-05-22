<?php
class Suspensions implements \IteratorAggregate {
	protected $cudid;
	protected $records = [];
	
	public function __construct(string $cudid) {
		global $db;
		
		$this->cudid = $cudid;
		
		$sql = "SELECT * FROM Suspensions WHERE cudid = :cudid ORDER BY SuspendStrDt";
		$this->records = $db->get($sql, [':cudid' => $cudid]);
	}
	
	public function getIterator(): \Traversable {
		return new \ArrayIterator($this->records);
	}
	
	public function all(): array {
		return $this->records;
	}
	
	public function isCurrentlySuspended(): bool {
		$today = date('Ymd');
		
		foreach ($this->all() as $suspension) {
			$start = $suspension['SuspendStrDt'] ?? null;
			$end = $suspension['SuspendEndDt'] ?? $suspension['SuspendExpEndDt'] ?? null;
			
			if ($start && $end && $today >= $start && $today <= $end) {
				return true;
			}
		}
		
		return false;
	}
}

?>