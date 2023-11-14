<?
if($warning!=''){
?>
 <div class="alert alert-danger"><?echo $warning?></div>
<?
} else {
?>
 <form class="form-horizontal" method="post" action="<? echo base_url()?>welcome/validate_coupon/" id="user_form" onsubmit="return validate_form()">


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
                <input id="phone_main" name="phone_main" type="text" placeholder="Mobile" value="" validate="required" class="input-xlarge"  onKeyPress="return isNumberKey_phone(this);" onKeyUp="val_format_ku(this);">
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
            <label class="control-label checkbox_alert" style="border-radius: 9px;padding: 3px;">Im agree with the <a href="#ModalTerms" role="button" data-toggle="modal">terms and conditions</a></label>
            <div class="controls">
                <input id="termsnconditions" name="termsnconditions" type="checkbox"  class="input-xlarge" validate="required" data-alert=".checkbox_alert" />
                <p class="help-block"></p>
            </div>
        </div>
        <!-- region input-->

		<input type="hidden" id="usr_hours" name="usr_hours"  value="<? echo $hours ?>"  />
		<input  type="hidden" id="usr_people" name="usr_people"  value="<? echo $people ?>"/>
		<input type="hidden"  id="usr_special" name="usr_special" value="<? echo $special ?>" />
		<input  type="hidden" id="usr_obs" name="usr_obs" value="online registration, Promotional Code <? echo $code ?>"   />
		<input  type="hidden" id="coupon" name="coupon" value="<? echo $code ?>"   />
		<input  type="hidden" id="usr_level" name="usr_level"  value="<? echo $level ?>" />
		<input  type="hidden" id="retrieve" name="retrieve"  value="1" />
        <!-- postal-code input-->
        
        
        <input type="hidden" id="usr_id" name="usr_id" value="">
    </fieldset>
    

    <div class="btn-group">
    <button id="btnNew" class="btn btn-large btn-primary ladda-button zoom-out" type="submit" onclick="">
        <span class="ladda-label">Register</span>
        <span class="ladda-spinner"></span>
        <div class="ladda-progress" style="width: 0px;"></div>
    </button>
    </div>

    <div class="btn-group">
    <button id="btn" class="btn btn-large btn-warning  ladda-button zoom-out" type="button" onclick="history.back() ;" >
        <span class="ladda-label">Cancel</span>
        <span class="ladda-spinner"></span>
        <div class="ladda-progress" style="width: 0px;"></div>
    </button>
    </div>
    <script>
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
	
		function validate_form(){
			$("#btnNew").attr("disabled","disabled");
			$("#btnNew").children(".ladda-label").html('Please wait...');
			
		
			var validate_elements = $('input[validate]');
			errors = 0;
			errorCss = { border : '1px solid red' }
			clearCss = { border : '1px solid #cccccc'}
			validate_elements.css(clearCss);
			
			validate_elements.each(function(){
				if($(this).attr('type') == 'checkbox'){
					if(!$(this).prop('checked')){
						$($(this).data('alert')).css(errorCss);
						errors += 1;
					}else{
						$($(this).data('alert')).css(clearCss);
					}
				
				
				}else{
				
					if($(this).val() == ''){
						$(this).css(errorCss);
						errors +=1;
					}
					
				}
				
				
				
			})
			
			if(errors!=0){
				$("#btnNew").removeAttr("disabled");
				$("#btnNew").children(".ladda-label").html('Register');
			
			}
			
			return errors == 0;
			
				
		
		
		
		}
    </script>
    
     <div id="ModalTerms" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalTermLabel" aria-hidden="true">
        <div class="modal-header">
        	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        	<h3 id="myModalTermLabel">Terms and Conditions</h3>
        </div>
        <div class="modal-body">
        	<p><?php echo @$terms ?></p>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        </div>
    </div>
</form>



<? } ?>
