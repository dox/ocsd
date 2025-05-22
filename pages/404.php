<?php
$logData = [
	'category' => 'system',
	'result'   => 'error',
	'description' => '404 for ' . $_SERVER['REQUEST_URI']
];
$log->create($logData);
?>

<div class="card text-bg-dark">
	<img src="images/mick.jpg" class="card-img" alt="Mick Jagger singing">
	<div class="card-img-overlay">
		<h5 class="card-title">404: Page Not Found</h5>
		<p class="card-text">You can't always get what you want...</p>
		<p class="card-text"><span class="text-danger font-monospace"><?php echo $_SERVER['REQUEST_URI']; ?></span></p>
		<p class="card-text">
			<button class="btn btn-primary px-5 mb-5" type="button" onclick="history.back()">
			  Go back
			</button>
		</p>
	</div>
</div>