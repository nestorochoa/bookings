<?
if ($warning != '') {
?>
<div class="alert alert-danger">
  <?php echo $warning ?>
</div>
<?
} else {
?>
<form class="form-horizontal" method="post"
  action="<?php echo base_url() ?>welcome/update_users/<?php echo $user_info['id'] ?>" id="user_form">

  <? foreach ($mem_gen as $key => $value) { ?>

  <input type="hidden" id="<?php echo $key ?>" name="<?php echo $key ?>" value="<?php echo $value ?>" />
  <? } ?>

  <fieldset>
    <!-- Address form -->
    <h2>
      <?php echo $user_info['id'] == 'New' ? 'New ' : 'Edit ' ?>
      <?
        if ($mem_gen['mem_type'] == 1) {
          echo 'User';
        }
        if ($mem_gen['mem_type'] == 2) {
          echo 'School Manager';
        }
        if ($mem_gen['mem_type'] == 3) {
          echo 'Instructor';
        }
        if ($mem_gen['mem_type'] == 4) {
          echo 'Student';
        }
        ?>
    </h2>

    <!-- name input-->
    <div class="control-group">
      <label class="control-label">Name</label>
      <div class="controls">
        <input id="edit_name" name="edit_name" type="text" placeholder="Name" value="<?php echo $user_info['name'] ?>"
          validate="required" class="input-xlarge">
        <p class="help-block"></p>
      </div>
    </div>
    <!-- surname input-->
    <div class="control-group">
      <label class="control-label">Surname</label>
      <div class="controls">
        <input id="edit_surname" name="edit_surname" type="text" placeholder="Surname"
          value="<?php echo $user_info['surname'] ?>" validate="required" class="input-xlarge">
        <p class="help-block"></p>
      </div>
    </div>
    <!-- address-line1 input-->
    <div class="control-group">
      <label class="control-label">Email</label>
      <div class="controls">
        <input id="edit_email" name="edit_email" type="email" placeholder="Email"
          value="<?php echo $user_info['email'] ?>" <? if ($validate_email==1) { ?> validate="required"
        <? } ?> class="input-xlarge">
        <!-- <p class="help-block">Street address, P.O. box, company name, c/o</p> -->
        <p class="help-block"></p>
      </div>
    </div>
    <!-- address-line2 input-->
    <div class="control-group">
      <label class="control-label">Phone (Mobile)</label>
      <div class="controls">
        <input id="phone_main" name="phone_main" type="text" placeholder="Mobile"
          value="<?php echo $user_info['f_phone'] ?>" validate="custom_mobile" class="input-xlarge"
          onKeyUp="val_format_ku(this);" onKeyPress="return isNumberKey_phone(this);">
        <p class="help-block"></p>
      </div>
    </div>
    <!-- city input-->
    <!--<div class="control-group">
            <label class="control-label">Sec. Phone</label>
            <div class="controls">
                <input id="sec_phone" name="sec_phone" type="text" placeholder="Sec. Phone" class="input-xlarge"  value="<?php echo $user_info['s_phone'] ?>">
                <p class="help-block"></p>
            </div>
        </div>-->
    <div class="control-group">
      <label class="control-label">Password</label>
      <div class="controls">
        <input id="password" name="password" type="text" placeholder="Password" class="input-xlarge">
        <p class="help-block"></p>
      </div>
    </div>
    <!-- region input-->
    <!-- <div class="control-group">
            <label class="control-label" >Username</label>
            <div class="controls">
                <input id="edit_username" name="edit_username" type="text" placeholder="Username" value="<?php echo $user_info['username'] ?>" validate="required"
                class="input-xlarge">
                <p class="help-block"></p>
            </div>
        </div> -->
    <?php
      if ($mem_gen['mem_type'] == 4) {
      ?>
    <div class="control-group">
      <label class="control-label">Type</label>
      <div class="controls">
        <select class="input-xlarge" id="usr_level" name="usr_level">
          <option value="">-- Choose one --</option>
          <?php foreach ($sport_types->result() as $item) { ?>
          <option value="<?php echo $item->type_id ?>" <?php echo $user_info['usr_sport'] == $item->type_id ?>>
            <?php echo $item->type_description ?>
          </option>
          <? } ?>
        </select>
        <p class="help-block"></p>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label">Level</label>
      <div class="controls">
        <select class="input-xlarge" id="usr_level" name="usr_level">
          <option value="">-- Choose one --</option>
          <? foreach ($level_query->result() as $item) { ?>
          <option value="<?php echo $item->sl_id ?>" <? if ($user_info['level']==$item->sl_id) {
            echo 'selected ';
            } ?>>
            <?php echo $item->sl_description ?>
          </option>
          <? } ?>
        </select>
        <p class="help-block"></p>
      </div>
    </div>

    <div class="control-group">
      <label class="control-label"># Hours</label>
      <div class="controls">
        <? if ($user_info['id'] != 'New') { ?>
        <input type="text" id="usr_hours" name="usr_hours" value="<?php echo $user_info['hours'] ?>" />
        <? } else { ?>
        <select class="input-xlarge" id="usr_hours" name="usr_hours" validate="required">
          <option value="">-- Choose one --</option>
          <?php
                $xx = 0;
                while ($xx <= 10) { ?>
          <option value="<?php echo $xx ?>"><?php echo $xx ?></option>
          <?php $xx += 0.5;
                } ?>

        </select>
        <? } ?>
        <p class="help-block"></p>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label"># Persons</label>
      <div class="controls">
        <select class="input-xlarge" id="usr_people" name="usr_people" validate="required">
          <option value="">-- Choose one --</option>
          <option value="1" <? if ($user_info['persons']==1) { echo 'selected ' ; } ?>>1</option>
          <option value="2" <? if ($user_info['persons']==2) { echo 'selected ' ; } ?>>2</option>
          <option value="3" <? if ($user_info['persons']==3) { echo 'selected ' ; } ?>>3</option>
        </select>
        <p class="help-block"></p>
      </div>
    </div>
    <!-- <div class="control-group">
            <label class="control-label">Price</label>
            <div class="controls">
	    
	    	<input type="text" id="usr_price" name="usr_price"  value="<?php echo $user_info['price'] ?>" readonly />
	    
                <p class="help-block"></p>
            </div>
        </div>
 <?
        $pay_method = $this->db->get('payment_method');
  ?>
	<div class="control-group">
            <label class="control-label">Payment Method</label>
            <div class="controls">
                <select class="input-xlarge" id="pay_method" name="pay_method" <? if ($user_info['pm'] != '') {
                                                                                  echo 'disabled';
                                                                                } ?> >
			<option value="">-- Choose one --</option>
			<? foreach ($pay_method->result() as $pm) { ?>
			<option value="<?php echo $pm->pm_id ?>" <? if ($user_info['pm'] == $pm->pm_id) {
                                                  echo 'selected';
                                                } ?>><?php echo $pm->pm_description ?></option>
			<? } ?>
		</select>
                <p class="help-block"></p>
            </div>
        </div>
	-->
    <div class="control-group">
      <label class="control-label">Promotion Student</label>
      <div class="controls">
        <input type="checkbox" id="usr_special" name="usr_special" onclick="$(this).siblings('.help-block').toggle()"
          value="1" <? if ($user_info['special']==1) { echo 'checked ' ; } ?> />

        <p class="help-block" style="<? if ($user_info['special'] != 1) {
                                            echo 'display:none ';
                                          } ?>">* The special student only can book lessons when the
          special dates are available.</p>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label">Observations</label>
      <div class="controls">
        <textarea class="input-xlarge" id="usr_obs" name="usr_obs"><?php echo $user_info['observation'] ?></textarea>
        <p class="help-block"></p>
      </div>
    </div>
    <?
      } ?>
    <!-- postal-code input-->

    <input type="hidden" id="bk_origin" name="bk_origin"
      value="<?php echo isset($user_info['bk_origin']) ? $user_info['bk_origin'] : "" ?>">
    <input type="hidden" id="usr_id" name="usr_id" value="<?php echo $user_info['id'] ?>">
  </fieldset>

  <? if ($user_info['id'] == 'New') {    ?>
  <div class="btn-group">
    <button id="btnNew" class="btn btn-large btn-primary ladda-button zoom-out">
      <span class="ladda-label">Create New
        <?
            if ($mem_gen['mem_type'] == 1) {
              echo 'User';
            }
            if ($mem_gen['mem_type'] == 2) {
              echo 'School Manager';
            }
            if ($mem_gen['mem_type'] == 3) {
              echo 'Instructor';
            }
            if ($mem_gen['mem_type'] == 4) {
              echo 'Student';
            }
            ?>
      </span>
      <span class="ladda-spinner"></span>
      <div class="ladda-progress" style="width: 0px;"></div>
    </button>
  </div>
  <? } else { ?>
  <div class="btn-group">
    <button id="btnCancel" class="btn btn-large btn-primary  ladda-button zoom-out">
      <span class="ladda-label">Update</span>
      <span class="ladda-spinner"></span>
      <div class="ladda-progress" style="width: 0px;"></div>
    </button>
  </div>

  <? } ?>
  <? if ($validate_email == 1) { ?>
  <div class="btn-group">
    <button id="btn" class="btn btn-large btn-warning  ladda-button zoom-out" type="button" onclick="history.back() ;">
      <span class="ladda-label">Cancel</span>
      <span class="ladda-spinner"></span>
      <div class="ladda-progress" style="width: 0px;"></div>
    </button>
  </div>
  <? } else { ?>
  <div class="btn-group">
    <button id="btn" class="btn btn-large btn-warning  ladda-button zoom-out" type="button" onclick="window.close() ;">
      <span class="ladda-label">Close</span>
      <span class="ladda-spinner"></span>
      <div class="ladda-progress" style="width: 0px;"></div>
    </button>
  </div>


  <? } ?>
  <script>
  $.bt_validate.method(
    'custom_mobile',
    function(value) {

      if (value.length != 12) {
        return false;
      }

      return true;


    },
    "Mobile number must be 10 digit format 04XX XXX XXX"
  );


  $('#user_form').bt_validate();

  function isNumberKey_phone(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
      return false;
    }





    return true;
  }

  function val_format_ku(evt) {
    var actual_value = evt.value;
    var corrected_value = actual_value.replace(/\s+/g, '');
    var or_length = corrected_value.length;

    if (or_length > 4 && or_length <= 7)
      corrected_value = corrected_value.substring(0, 4) + " " + corrected_value.substring(4, corrected_value.length);

    if (or_length > 7)
      corrected_value = corrected_value.substring(0, 4) + " " + corrected_value.substring(4, 7) + " " + corrected_value
      .substring(7, corrected_value.length);

    if (or_length >= 10) {
      corrected_value = corrected_value.substring(0, 12)
    }


    evt.value = corrected_value;

  }
  </script>
</form>



<? } ?>