 <div class="modal fade" id="new_booking_modal" style="display:none;" tabindex="-1" role="dialog" aria-labelledby="New Booking" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">New booking</h4>
      </div>
      
      <form id="add_booking" method="post" action="<?echo base_url()?>welcome/add_booking" />
      <div class="modal-body" style="text-align:center">
 <div class="well">
 <div class="alert alert-error" id="alert_booking" style="display:none">
			<strong></strong>
		  </div>
 	<div class="group_form">
                
		<input type="hidden" id="id_student" name="id_student" value="" />
		<input type="hidden" id="id_lesson_booking" name="id_lesson_booking" value="" />
		<div style="float:right;color:red;" class="error" ></div>
	 </div>
<div class="group_form">
<label>Start hour</label>
  	<select id="booking_start_hour" name="booking_start_hour" >
		<option >  </option>
	</select> <div style="float:right;color:red" class="error" ></div></div>
	
<div class="group_form">
<label>Hours</label>
  	<select id="booking_hour_quatity" name="booking_hour_quatity" >
		<option >  </option>
	</select><div style="float:right;color:red" class="error" ></div>
 </div>	
	
 </div>
       </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="return val_booking();">Save changes</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
 <script>
 	
	
	function val_booking(){
		$("#alert_booking").css('display','none');
		var  hour_q = $('#booking_hour_quatity');
		var student = $('#id_student');
		var hour_t = $('#booking_start_hour');
		
		var id_lesson = $("#id_lesson_booking").val()
		
		$("#new_booking_modal").children().find('.error').html('');
		
		error = 0 ;
		if(student.val() == ''){
			error += 1;
			student.siblings(".error").html("*");	
		}
		
		if(hour_q.val() == ''){
			error += 1;
			hour_q.siblings(".error").html("*");	
		}
		
		if(hour_t.val() == ''){
			error += 1;
			hour_t.siblings(".error").html("*");	
		}
		
		if(error == 0){
			$.ajax({
		              url: '<?echo base_url() ?>welcome/ajax_booking',
		              type: 'post',
		              dataType: 'json',
		
		              data: { 'id_student' : student.val() , 'id_day' : id_lesson, 'start_hour' : hour_t.val(), 'end_hour' : hour_q.val()},
		              success: function(data) {
					if(data.response.warning!=''){
						$("#alert_booking").css('display','block');
						$("#alert_booking").children('strong').html(data.response.warning)
					}else{
						Calendar.create_booking_html( data.response.student,data.response.booking);
						$("#new_booking_modal").modal("hide");
					}
					
					
					
		              },
			      complete : function(data){
			      	var data
			      }
		        });
		}else{
				
		}
		
		return false;
	}
 </script>