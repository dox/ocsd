<?php
$template = $templatesClass->one($_GET['uid']);

?>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Template: <?php echo $template['name']; ?></h3>
  </div>
  <div class="card-body">
    <form>
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" class="form-control" name="name" placeholder="Name" value="<?php echo $template['name'];?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Type</label>
        <select class="form-select" name="type">
          <?php
          $typesArray = array("email", "unused");

          foreach ($typesArray AS $type) {
            if ($template['type'] == $type) {
              $active = " selected";
            } else {
              $active = "";
            }
            echo "<option value=\"email\" " . $active . ">" . $type . "</option>";
          }
          ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Subject</label>
        <input type="text" class="form-control" name="subject" placeholder="Subject" value="<?php echo $template['subject'];?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea class="form-control" name="description" placeholder="Description"><?php echo $template['description'];?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Body</label>
        <textarea class="form-control summernote" name="body" placeholder="Body"><?php echo $template['body'];?></textarea>
      </div>
    </form>
  </div>
</div>
