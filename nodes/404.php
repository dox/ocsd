<?php
$logInsert = (new Logs)->insert("view","error",null,"404 for page <code>" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "</code>");
?>
<div class="container-tight py-6">
	<div class="empty">
		<div class="empty-icon">
			<div class="display-4">404</div>
		</div>
		<p class="empty-title h3">Oops… You just found an error page</p>
		<p class="empty-subtitle text-muted">
			We are sorry but the page you are looking for was not found
		</p>
		<div class="empty-action">
			<a href="./." class="btn btn-primary">Take me home</a>
		</div>
	</div>
</div>
