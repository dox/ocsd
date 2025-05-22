<?php
class Suspensions implements \IteratorAggregate {
	protected $cudid;
	protected $records = [];
	
	public function __construct(string $cudid) {
		global $db;
		
		$this->cudid = $cudid;
		
		$sql = "SELECT * FROM Suspensions WHERE cudid = :cudid ORDER BY SuspendStrDt DESC";
		$this->records = $db->get($sql, [':cudid' => $cudid]);
	}
	
	public function getIterator(): \Traversable {
		return new \ArrayIterator($this->records);
	}
	
	public function all(): array {
		return $this->records;
	}
	
	public function currentSuspension(): ?array {
		$today = date('Ymd');
		
		foreach ($this->all() as $suspension) {
			$start = $suspension['SuspendStrDt'] ?? null;
			$end = $suspension['SuspendEndDt'] ?? $suspension['SuspendExpEndDt'] ?? null;
			
			if ($start && $end && $today >= $start && $today <= $end) {
				return $suspension;
			}
		}
		
		return null;
	}
	
	public function currentSuspensionEndDate() {
		if ($this->isCurrentlySuspended()) {
			$effectiveEndDate = !empty($this->currentSuspension()['SuspendEndDt']) ? $this->currentSuspension()['SuspendEndDt'] : $this->currentSuspension()['SuspendExpEndDt'];
			
			return date('Y-m-d', strtotime($effectiveEndDate));
		}
		
		return false;
	}
	
	
	
	public function isCurrentlySuspended(): bool {
		if (isset($this->currentSuspension()['cudid'])) {
			return true;
		}
		
		return false;
	}
}

?>