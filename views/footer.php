<footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
  <div class="col-md-4 d-flex align-items-center">
    <a href="/" class="mb-3 me-2 mb-md-0 text-muted text-decoration-none lh-1">
      <svg class="me-2 text-muted" width="1em" height="1em" role="img" aria-label="OCSD"><use xlink:href="images/icons.svg#ocsd-logo"/></svg>
    </a>
    <span class="text-muted">© 2015 - <?php echo date('Y');?> <a href="https://github.com/dox" class="link-secondary">github/dox</a>.  All rights reserved.</span>
  </div>
</footer>

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="https://help.seh.ox.ac.uk/assets/chat/chat.min.js"></script>
<script>
$(function() {
  new ZammadChat({
  title: 'Need IT Support?',
  background: '#6b7889',
  fontSize: '12px',
  chatId: 1
  });
});
</script>