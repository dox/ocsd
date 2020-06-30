<?php
$filter = array('api_token' => api_token, 'filter' => 'one', 'cudid' => $_GET['cudid']);
$personsJSON = api_decode("person", "read", $filter);
if ($personsJSON->count == 1) {
	$personJSON = $personsJSON->body[0];
}



if (isset($personJSON->cudid)) {
	$logInsert = (new Logs)->insert("view","success",$personJSON->cudid,$personJSON->FullName . " record viewed");
} else {
	$logInsert = (new Logs)->insert("view","error",null,"<code>" . $_GET['cudid'] . "</code> record viewed but doesn't exist");
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2"><?php echo cardTypeBadge($personJSON->university_card_type) . " " . $personJSON->FullName; ?></h1>
	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="btn-group mr-2">
			<button type="button" class="btn btn-sm btn-outline-secondary">void</button>
			<button type="button" class="btn btn-sm btn-outline-secondary">void</button>
		</div>

		<div class="btn-group" role="group">
			<button id="btnGroupDrop1" type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
			<div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1">
				<a class="dropdown-item emailParcelButton1" href="#" id="<?php echo $personJSON->cudid; ?>"><strong>Email</strong> "You have a delivery"</a>
				<a class="dropdown-item" href="#">Dropdown link</a>
			</div>
		</div>
	</div>
</div>

<nav>
	<div class="nav nav-tabs" id="nav-tab" role="tablist">
		<a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Home</a>
		<?php
		$dir = 'nodes/persons_unique_tabs/';
		$files = scandir($dir);

		foreach ($files AS $file) {
			$extension = end(explode('.', $file));
			$filename = reset(explode('.', $file));

			if ($extension == "php") {
				$includeFile == false;

				$navBarOutput  = "<a class=\"nav-item nav-link\" id=\"nav-" . $filename . "-tab\" data-toggle=\"tab\" href=\"#nav-" . $filename . "\" role=\"tab\" aria-controls=\"nav-" . $filename . "\" aria-selected=\"false\">";
				$navBarOutput .= ucwords($filename);
				$navBarOutput .= "</a>";

				$tabOutput  = "<div class=\"tab-pane fade\" id=\"nav-" . $filename . "\" role=\"tabpanel\" aria-labelledby=\"nav-" . $filename . "-tab\">";
				ob_start();
				include_once($dir . "/" . $file);
				if ($includeFile == true) {
					$tabOutput .= ob_get_contents();
				}
				ob_end_clean();
				$tabOutput .= "</div>";

				if ($includeFile == true) {
					echo $navBarOutput;
				}

				$tabs[] = $tabOutput;
		  }
		}
		?>
	</div>
</nav>

<br />

<?php


?>

<div class="tab-content" id="nav-tabContent">
	<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
		<?php echo photoCard($personJSON->university_card_sysis); ?>
		<table class="table">
			<thead>
				<tr>
					<th scope="col">Key</th>
					<th scope="col">Value</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($personJSON as $key => $value) {
					if (isset($value)) {
						$output  = "<tr>";
						$output  .= "<td>" . $key . "</td>";
						$output .= "<td>" . $value . "</td>";
						$output .= "</tr>";

						echo $output;
					}
				}
				?>
			</tbody>
		</table>
		<p>Student Number: <?php echo $personJSON->sits_student_code; ?></p>
		<p>SSO: <?php echo $personJSON->sso_username; ?></p>
		<p>Oxford Email: <?php echo makeEmail($personJSON->oxford_email); ?></p>
		<p>Bodcard: <?php echo $personJSON->barcode7; ?></p>
		<p>Nationality: <?php echo nationality($personJSON->cudid); ?></p>
		<?php
			if (isset($personJSON->internal_tel)) {
				echo "<p>Chorus Telephone Number: " . $personJSON->internal_tel . "</p>";
			}
		?>
			</div>

	<?php
	foreach ($tabs AS $tab) {
		echo $tab;
	}
	?>

	<div class="tab-pane fade" id="nav-signpass" role="tabpanel" aria-labelledby="nav-signpass-tab">
		<h2>Sign Pass</h2>
		<pre><?php include_once("signpass.php"); ?></pre>
	</div>
	<div class="tab-pane fade" id="nav-ldap" role="tabpanel" aria-labelledby="nav-ldap-tab">
		<?php include_once("persons_unique-ldap.php"); ?>
	</div>

</div>
</div>
