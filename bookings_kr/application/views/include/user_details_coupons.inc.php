<?
if($warning!=''){
?>
 <div class="alert alert-danger"><?php echo $warning?></div>
<?
} else {
?>
 <form class="form-horizontal" method="post" action="<? echo base_url()?>welcome/update_users_coupon/" id="user_form">


    <fieldset>
        <!-- Address form -->
 
<h2>New Student</h2>
 
        <!-- name input-->
        <div class="control-group">
            <label class="control-label">Name</label>
            <div class="controls">
                <input id="edit_name" name="edit_name" type="text" placeholder="Name" value="" validate="required"
                class="input-xlarge">
                <p class="help-block"></p>
            </div>
        </div>
	<!-- surname input-->
        <div class="control-group">
            <label class="control-label">Surname</label>
            <div class="controls">
                <input id="edit_surname" name="edit_surname" type="text" placeholder="Surname" value=""  validate="required"
                class="input-xlarge">
                <p class="help-block"></p>
            </div>
        </div>
	
        <!-- address-line1 input-->
        <div class="control-group">
            <label class="control-label">Email</label>
            <div class="controls">
                <input id="edit_email" name="edit_email" type="text" placeholder="Email" value=""  validate="required|email"
                class="input-xlarge">
                <!-- <p class="help-block">Street address, P.O. box, company name, c/o</p> -->
		<p class="help-block"></p>
            </div>
        </div>
        <!-- address-line2 input-->
        <div class="control-group">
            <label class="control-label">Phone (Mobile)</label>
            <div class="controls">
                <input id="phone_main" name="phone_main" type="text" placeholder="Mobile" value="" validate="custom_mobile" class="input-xlarge" onKeyUp="val_format_ku(this);"  onKeyPress="return isNumberKey_phone(this);">
		<p class="help-block"></p>
            </div>
        </div>
        <!-- city input-->
        <div class="control-group">
            <label class="control-label">Sec. Phone</label>
            <div class="controls">
                <input id="sec_phone" name="sec_phone" type="text" placeholder="Sec. Phone" class="input-xlarge"  value="">
                <p class="help-block"></p>
            </div>
        </div>
		<div class="control-group">
            <label class="control-label">Password</label>
            <div class="controls">
                <input id="password" name="password" type="text" placeholder="Password"
                class="input-xlarge">
                <p class="help-block"></p>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">Im agree with the <a href="#ModalTerms" role="button" data-toggle="modal">terms and conditions</a></label>
            <div class="controls">
                <input id="termsnconditions" name="termsnconditions" type="checkbox"  class="input-xlarge" />
                <p class="help-block"></p>
            </div>
        </div>
        <!-- region input-->

		<input type="hidden" id="usr_hours" name="usr_hours"  value="<? echo $hours ?>"  />
		<input  id="usr_people" name="usr_people"  value="<? echo $people ?>"/>
		<input type="hidden"  id="usr_special" name="usr_special" value="<? echo $special ?>" />
		<input  type="hidden" id="usr_obs" name="usr_obs" value="online registration, Promotional Code <? echo $code ?>"   />
		<input  type="hidden" id="usr_code" name="usr_code" value="<? echo $code ?>"   />
		<input  type="hidden" id="usr_level" name="usr_level"  value="<? echo $level ?>" />
        <!-- postal-code input-->
        
        
        <input type="hidden" id="usr_id" name="usr_id" value="">
    </fieldset>
    
    <div id="ModalTerms" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalTermLabel" aria-hidden="true">
        <div class="modal-header">
        	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        	<h3 id="myModalTermLabel">Terms and Conditions</h3>
        </div>
        <div class="modal-body">
        	<p><? echo $terms ?></p>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        </div>
    </div>
    
    <? if($user_info['id']=='New'){    ?>
    <div class="btn-group">
    <button id="btnNew" class="btn btn-large btn-primary ladda-button zoom-out">
        <span class="ladda-label">Register</span>
        <span class="ladda-spinner"></span>
        <div class="ladda-progress" style="width: 0px;"></div>
    </button>
    </div>
    <?}else{?>
    <div class="btn-group">
    <button id="btnCancel" class="btn btn-large btn-primary  ladda-button zoom-out"  >
        <span class="ladda-label">Update</span>
        <span class="ladda-spinner"></span>
        <div class="ladda-progress" style="width: 0px;"></div>
    </button>
    </div>

    <?}?>
    <div class="btn-group">
    <button id="btn" class="btn btn-large btn-warning  ladda-button zoom-out" type="button" onclick="history.back() ;" >
        <span class="ladda-label">Cancel</span>
        <span class="ladda-spinner"></span>
        <div class="ladda-progress" style="width: 0px;"></div>
    </button>
    </div>
    <script>
		$.bt_validate.method(
	  'custom_mobile', 
	  function(value) {
		  
		if(value.length != 12){
			return false;	
		}
		
		return true;
		
		
	  },
	  "Mobile number must be 10 digit format 04XX XXX XXX"
	);
	
	
	$('#user_form').bt_validate();
	
	function isNumberKey_phone(evt)
      {
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57)){
            return false;
		 }
		 
		 
		 
		 
		 
         return true;
      }
	 function val_format_ku(evt){
		 var actual_value = evt.value;
		 var corrected_value = actual_value.replace(/\s+/g, '');
		 var or_length = corrected_value.length;
		 
		if( or_length > 4 && or_length <= 7 )
			corrected_value = corrected_value.substring(0, 4) + " " + corrected_value.substring(4, corrected_value.length);
			
		if(or_length > 7 )
			corrected_value = corrected_value.substring(0, 4) + " " + corrected_value.substring(4,7) + " " + corrected_value.substring(7, corrected_value.length);
		
		if(or_length >= 10){
			corrected_value = corrected_value.substring(0, 12)
		}
		
		
		evt.value = corrected_value;
		 
	}
    </script>
</form>



<? } ?>