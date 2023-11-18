<div class="row">
  <div class="col">
    <h2>SMS Templates</h2>
  </div>
</div>
<div class="row">
  <div class="col">
    <div class="alert alert-info" role="alert">
      <div>
        <b>[hour]</b> -> Booking hour
      </div>
      <div>
        <b>[date]</b> -> Booking date

      </div>
      <div>
        <b>[name]</b> -> user name

      </div>
    </div>
  </div>
</div>
<form id="smsForm" hx-post="/sms/update" hx-confirm="Are you sure you want to update the SMS messages?">
  <?php foreach ($data as $template) { ?>
  <div class="mt-4 row special-ta">
    <div class="col">
      <div class="input-group">
        <span class="input-group-text"><?php echo $template->st_description ?></span>
        <textarea name="text-<?php echo $template->st_id ?>" class="form-control" require
          aria-label="<?php echo $template->st_description ?>"><?php echo $template->st_template ?></textarea>
      </div>
    </div>
  </div>
  <?php } ?>
  <div class="row mt-4">
    <div class="col">
      <button class="btn btn-primary">Save changes</button>
    </div>
  </div>
</form>
<script>


</script>