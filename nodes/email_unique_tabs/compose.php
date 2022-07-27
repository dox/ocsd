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
            <input type="text" class="form-control" name="emailSubject" value="St Edmund Hall IT Credentials">
          </div>
        </div>
      </div>
      <div class="mb-2">
        <div class="row align-items-center">
          <label class="col-sm-2">From:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="emailSender" value="help@seh.ox.ac.uk">
          </div>
        </div>
      </div>
      <div class="mb-2">
        <div class="row align-items-center">
          <label class="col-sm-2">From Name:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="emailSenderName" value="SEH IT Office">
          </div>
        </div>
      </div>
      <textarea rows="10" class="form-control summernote" name="emailMessage">
        <p>Your College computer account for St Edmund Hall has now been activated, and your details are listed below:</p>
        
        <p>Username: </p>
        <p>Password: <code>{{password}}</code> (case sensitive)</p>
        
        <h3>WiFI/Internet</h3>
        <p>Your College username/password will grant you access to the SEH WiFi (please 'forget' the SEH Guest WiFi network if you have been using it).</p>
        
        <p>You can also use this username/password to log on to the protected sections of the www.seh.ox.ac.uk website.</p>
        
        <p>Please note that your Internet access is monitored. Downloading of illegal material (such as films or music) is strictly prohibited and, if caught, will be fined £100/offence by the University Information Security team.</p>
        
        <h3>Printing</h3>
        <p>You can either print from any of the onsite computers at St Edmund Hall, or you can log on to http://printing.seh.ox.ac.uk from your own computer. From here you can check your printing charges and submit to the JCR, MCR, Library or NSE printers.</p>
        
        <p>Paper is available from the Lodge</p>
        
        <h3>Computers</h3>
        <p>The above username/password will log you on to any computer at St Edmund Hall.</p>
        
        <p>If you wish to change your College password, please <a href="https://www.seh.ox.ac.uk/it/password/index.php?action=change">click here</a></p>
        
        <p>Please note: it is important that you do not share these details with anyone. It is used to track who had access and made changes to specific information. You are responsible for everything done on the system using your username and password.</p>
        
        <p>For any College-IT issues, please email help@seh.ox.ac.uk</p>
        
        <p>Regards,<br><br>
        
        IT Office<br>
        St Edmund Hall<br></p>

      </textarea>
      <div class="btn-list mt-4 text-right">
        <button type="button" class="btn btn-white btn-space">Cancel</button>
        <button type="submit" class="btn btn-primary btn-space">Send message</button>
      </div>
    </form>
  </div>
</div>
