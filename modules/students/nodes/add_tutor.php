<?php
$titles = Titles::find_all();

if (isset($_POST['form_submit'])) {
	printArray($_POST);

	$newTutor = new Tutors();
	$newTutor->titlekey = $_POST['addTitle'];
	$newTutor->initials = $_POST['addForenames'];
	$newTutor->forenames = $_POST['addForenames'];
	$newTutor->surname = $_POST['addSurname'];
	$newTutor->identifier = $_POST['addIdentifier'];
	$newTutor->photo = $_POST['addPhoto'];
	
	$newTutor->create();
}

?>
<div class="page-header">
	<h1>Add New Tutor</h1>
</div>

	
	
<form role="form" action="index.php?m=students&n=add_tutor.php" method="post">
	<div class="form-group">
		<label for="addTitle">Title</label>
		<select class="form-control" id="addTitle" name="addTitle">
			<?php
			foreach ($titles AS $title) {
				echo "<option value=\"" . $title->titleid . "\">" . $title->title . "</option>";
			}
			?>
			
		</select>
	</div>
	<div class="form-group">
		<label for="addInitials">Initials</label>
		<input type="text" class="form-control" id="addInitials" name="addInitials" placeholder="Initials">
	</div>
	<div class="form-group">
		<label for="addForenames">Forename(s)</label>
		<input type="text" class="form-control" id="addForenames" name="addForenames" placeholder="Forename(s)">
	</div>
	<div class="form-group">
		<label for="addSurname">Surname</label>
		<input type="text" class="form-control" id="addSurname" name="addSurname" placeholder="Surname">
	</div>
	<div class="form-group">
		<label for="addIdentifier">Identifier</label>
		<input type="text" class="form-control" id="addIdentifier" name="addIdentifier" placeholder="Identifier">
	</div>
	<div class="form-group">
		<label for="addPhoto">Photo</label>
		<input type="file" id="addPhoto" name="addPhoto">
		<p class="help-block">Do not use yet!</p>
	</div>
	<button type="submit" class="btn btn-default">Submit</button>
	<input type="hidden" value="true" id="form_submit" name="form_submit">
</form>