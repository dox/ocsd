<?php
$user = Students::find_by_uid($_GET['studentid']);
$degree = Grads::find_by_studentkey($user->studentid);
$subject = QualSubjects::find_by_qsid($degree->qskey);
?>

<p><u>To Whom It May Concern</u></p>

<p>Oxford University does not issue official transcript, but this is to certify that</p>

<p class="lead pagination-centered"><strong><?php echo $user->fullDisplayName(); ?></strong></p>

is a full-time member of this College, in the Univsersity of Oxford, studying for the Degree Course detailed below:



<br />
<p class="lead pull-right">Start: <?php echo date('Y M', strtotime($user->dt_start)); ?> End: <?php echo date('Y M', strtotime($user->dt_end)); ?></p>

<p class="lead">Degree: <strong><?php echo $degree->abbrv; ?></strong></p>
<p class="lead">Subject: <strong><?php echo $subject->name; ?></strong></p>

<h3>Course Exams</h3>
<table class="table">
	<thead>
	<tr>
		<th>Date</th>
		<th>Name</th>
		<th>Result</th>
	</tr>
	</thead>
	
	<tbody>
	<tr>
		<td>...</td>
		<td>...</td>
		<td>...</td>
	</tr>
	</tbody>
</table>

Signed:
<br /><br /><br /><br /><br /><br /><br /><br /><br /><br />

REGISTRAR