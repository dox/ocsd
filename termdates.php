<html>
<head>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
</head>
<body>
	<div class="row">
		<div class="col-md-4">
			
		</div>
		<div class="col-md-4">
			<div class="alert alert-info text-center">
				<hr />
				<h1>
				<?php
				require_once("engine/datesofterm.php");
				$termArray = ox_term_date();
				
				if ($termArray['term_name'] == "Holiday") {
					echo $termArray['term_name'];
				} else {
					echo $termArray['term_name'] . " " . $termArray['week_name'] . " Week";
				}
				?>
				</h1>
				<hr />
			</div>
		</div>
		<div class="col-md-4">
			
		</div>
	</div>
</body>
</html>