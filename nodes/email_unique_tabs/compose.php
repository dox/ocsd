<div class="card">
  <div class="card-header">
    <h3 class="card-title">Compose new message</h3>
  </div>
  <div class="card-body">
    <form id="sendGroupEmail" target="_self" method="post">
      <div class="mb-2">
        <div class="row align-items-center">
          <label class="col-sm-2">To:</label>
          <div class="col-sm-10">
            <textarea rows="5" class="form-control" name="emailRecipients"></textarea>
          </div>
        </div>
      </div>
      <div class="mb-2">
        <div class="row align-items-center">
          <label class="col-sm-2">Subject:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="emailSubject">
          </div>
        </div>
      </div>
      <div class="mb-2">
        <div class="row align-items-center">
          <label class="col-sm-2">From:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="emailSender" value="covid19@seh.ox.ac.uk">
          </div>
        </div>
      </div>
      <div class="mb-2">
        <div class="row align-items-center">
          <label class="col-sm-2">From Name:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="emailSenderName" value="SEH COVID-19 UPDATE">
          </div>
        </div>
      </div>
      <textarea rows="10" class="form-control summernote" name="emailMessage"></textarea>
      <div class="btn-list mt-4 text-right">
        <button type="button" class="btn btn-white btn-space">Cancel</button>
        <button type="submit" class="btn btn-primary btn-space">Send message</button>
      </div>
    </form>
  </div>
</div>
