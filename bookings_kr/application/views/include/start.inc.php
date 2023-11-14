<style>
	.controls > select{
		width: 100% !important;
	}
</style><? 


$class_m = "";
$class_d = "";
$hide = 1;
if(isset($warning) && trim($warning) != ''){ $class_m = $warning; $class_d = "alert-error" ; $hide = 0;}
if(isset($success) && trim($success) != ''){ $class_m = $success; $class_d = "alert-success"; $hide = 0;}

?>

<?php if($hide == 0){?>
<div class="alert <?php echo $class_d?>">
  <? echo $class_m ?>
</div>
<?php  } ?>
<!--<?php  echo $level_student ?> -->
<div class="row-fluid">
	<h1>Hi <? echo $this->session->userdata('user_name')?></h1>
	<h2>You have <? echo $hours_left?> hour(s) to go!</h2>

</div>
<div class="row-fluid">
	<div class="span3"></div>
</div>
<div class="row-fluid">
<? if($history->num_rows() > 0){?>
	<table class="table table-bordered">
		<tr>
			<td>Date</td>
			<td>Hour</td>
			<td colspan="2">Status</td>
			
			
		</tr>
		<?foreach($history->result() as $item) {?>
		<tr>
			<td><? echo $item->bd_date?></td>
			<td><? echo $item->hour_from ?> - <? echo $item->hour_to ?></td>
			<td><? 
			$status_var = $item->bk_status;
			if($special == 1){
			
				$status_var = $item->sg_status;
				
				if($item->bk_status == 3){
					$status_var = 3;
				}
				if($item->bk_status == 1){
					$status_var = 1;
				}
				
				
			}
					
			switch ($status_var) {
				    case 0:
				        echo "Booked";
				        break;
				    case 1:
				        echo "Cancelled by School";
				        break;
				    case 2:
				        echo "Cancelled by User";
				        break;
				    case 3:
				        echo "Done";
				        break;
				} ?></td>
			<? if ($status_var == 0) {?><td style="text-align:center"><button data-id="<? echo $item->bk_id ?>" class="btn btn-mini btn-danger" type="button" onclick="cancel_f(this);">Cancel</button></td><?}?>
		</tr>
		
		<? } ?>
	</table>
        <div id="cancelModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 id="myModalLabel">Booking cancellation</h3>
          </div>
          <div class="modal-body">
            <p>Are you sure ?</p>
          </div>
          <div class="modal-footer">
          <form method="post"  action="<? echo base_url() . 'start'?>">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                <button class="btn btn-danger" type="submit">Cancel</button>
                <input type="hidden" name="id_cancel" value="" id="id_cancel"/>
                <input type="hidden" name="cancel_check" value="1" id="cancel_check"  />
          </form>
        </div>
</div>
<?}else{?>
	<div>You haven't book any lessons yet</div>
<? } ?>
</div>
<div class="row-fluid">
	<div class="span3"></div>
</div>
<? if($special != 1) {?>
<div class="alert alert-info" role="alert"><h3>Hi There, </h3>
	<p>To carry on with your booking process please follow these three steps</p>
	<ol>
    	<li>Choose the number of hours that you want to book.<!-- (If is your first lesson, the best choice is two hours) --></li>
        <li>Then select a day</li>
        <li>The hours available appear in the box next to the label Time. (Sometimes, the day can be fully booked without any availability)</li>
    </ol>
</div>
<? } else { 
	if($basic->st_hours == 5){?>
<div class="alert alert-info" role="alert"><h3>Hi There, </h3>
	<p>To carry on with your booking process please follow these three steps</p>
	<ol>
    	<li>Choose the number of hours that you want to book (Your lessons are split in two different days: Your first lesson is two hours and your second lesson is three hours). Once you complete your first lesson, you will be able to book your second lesson</li>
        <li>Then select a day</li>
        <li>The hours available appear in the box next to the label Time. (Sometimes, the day can be fully booked without any availability)</li>
    </ol>
</div>
    
<? } 
	if($basic->st_hours == 2){
?>
<div class="alert alert-info" role="alert"><h3>Hi There, </h3>
	<p>	The length of your lesson is 2 hours. To carry on with your booking process please select a day<br/>
		Sometimes, the day can be fully booked without any availability<br/>
		For these lessons (2 hr in a group), it should show them only the 2 hr blocks and let them book only a 2 hr lesson (no choice of splitting hours)</p>

</div>
	
<? 	} ?>


<? } ?>
<div class="row-fluid">
	<div class="span3"></div>
</div>
<? if ($hours_left > 0) {?>
<div class="row-fluid">
	<div class="span6 small-grey-well">
	<form class="form-horizontal" id="formE" method="post" action="<? echo base_url() . 'start'?>"  >
	  <div class="control-group">
	    <label class="control-label"> # of hours</label>
	    <div class="controls">
	      <select id="hours_booking" name="hours_booking">
	      	<option value="">-- Select --</option>
	      	<? foreach($hours_book as $hour){?>
				<option value="<?  echo $hour?>"><? echo $hour?></option>
			<? }?>

	      </select>
	    </div>
	  </div>
      <input type="hidden" id="day_booking" name="day_booking" />
	  <? /*<div class="control-group">
	    <label class="control-label">Day</label>
	    <div class="controls">
	      <select id="day_booking" name="day_booking" >
	      	<option value=""> -- </option>
		<?foreach($days_book->result() as $day){?>
			<option value="<? echo $day->bd_date?>"><? echo $day->bd_date?></option>	
		<?}?>
	      </select>
	    </div>
	  </div> */?>
      <div class="control-group">
        <div id="datepicker_container"  >
            <div class="controls" data-date="<? echo date("Y-m-d") ?>" data-date-format="yyyy-mm-dd"></div>
        </div>
      </div>
	  <? /*(<div class="control-group">
	    
	    <div class="controls">
	      <select id="time_booking" name="time_booking" >
	      	<option value="">Please choose # of hours and a day</option>
	      </select>
	    </div>
	  </div> */?>
      <div class="control-group">
      <label class="control-label">Time</label>
      	<div class="controls sel-buttons">
        	
        </div>
      </div>
	  <? /* <div class="control-group">
	    <div class="controls">
	      
	      <button type="submit" class="btn" name="but_book" id="but_book" onclick="return quick_check()" value="but_book">Book now</button>
	    </div>
	  </div> */?>
	  <input type="hidden" id="start" name="start" />
	  <input type="hidden" id="end" name="end" />
	  <input type="hidden" id="group" name="group" />
</form></div>
</div>

<div id="confirmModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="confirmModal" aria-hidden="true">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" >x</button>
    <h3 id="myModalLabel">Booking Confirmation</h3>
    </div>
    <div class="modal-body">
    <p>You are about to book a lesson from <span id="span_ini"></span> to <span id="span_end"></span> on the <span id="span_date"></span></p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true" onclick="clean_form()">Close</button>
        <button class="btn btn-success" type="submit" form="formE" value="but_book"  name="but_book" onclick="send_confirmation()">Ok</button>
	</div>
</div>

<script>
	Date.prototype.yyyymmdd = function() {         
                                
        var yyyy = this.getFullYear().toString();                                    
        var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based         
        var dd  = this.getDate().toString();             
                            
        return yyyy + '-' + (mm[1]?mm:"0"+mm[0]) + '-' + (dd[1]?dd:"0"+dd[0]);
   }; 
   
	var days_available = [<?
		$first = '';
		foreach($days_book->result() as $day){
			echo $first === '' ? "'". $day->bd_date ."'" :  ",'". $day->bd_date ."'";
			
			$first = 'A';
		}
	?>];
	var valid_dates = days_available.map(function(string_date){
		return new Date(string_date).toDateString();
	})

	function quick_check(elem){
		number_hours = $('#hours_booking');
		day  = $('#day_booking');
		book = $('#time_booking');
		
		number_hours.parent().parent().removeClass('error');
		day.parent().parent().removeClass('error');
		book.parent().parent().removeClass('error');
		error = 0;
		
		if(number_hours.val() == ""){
			number_hours.parent().parent().addClass('error');
			error += 1;
		}
		if(day.val() == ""){
			day.parent().parent().addClass('error');
			error += 1;
		}
		if(book.val() == ""){
			book.parent().parent().addClass('error');
			error += 1;
		}
		
		if(error==0){
			var data_gen = $(elem).data();
			$("#start").val(data_gen.ini);
			$("#end").val(data_gen.end);
			$("#group").val(data_gen.idday);
			
			$("#span_ini").html(data_gen.ini);
			$("#span_end").html(data_gen.end);
			$("#span_date").html($("#day_booking").val());
			$('#confirmModal').modal('show');
		}else{
			return false;
		}
		
	}
	
	function check_values(){
		number_hours = $('#hours_booking').val();
		day  = $('#day_booking').val();
		var student_level = <? echo isset($level_student) ? $level_student : 1 ?>;
		
		$(".sel-buttons").html("");
		if(number_hours == '' || day == ''){
			$(".sel-buttons").html('<div class="alert">Please choose # of hours and a day</div>');
				
		}else{
			var params = { 'day': day, 'hour': number_hours, 'level' : student_level};
			var response;
			var re= $.ajax({
				url : base_url + 'welcome/find_next_booking_date',
				type : 'post',
				dataType : 'json',
				data: params,
				async : false,
				success : function (data){
					response = data;
				}
			})
			numb = 0
			
			
			var midday = new Date();
			parts = ("12:00:00").split(":");
			midday.setHours(parts[0],parts[1],parts[2],0);
			
			
			$.each(response, function( index, value ){
				numb += 1;
				
				option_b = '<button class="btn btn-primary btn-block" type="button" onclick="quick_check(this)" data-idday="'+value.bd_id+'" data-end="'+value.hour_end+'"  data-ini="'+value.hour_ini+'">'+ value.hour_ini +' - '+ value.hour_end +'</button>';


				var current_ini = new Date();
				var parts = value.hour_ini.split(":");
				current_ini.setHours(parts[0],parts[1],parts[2],0);
				
				if(student_level > 1){
					if(midday.getTime() <= current_ini.getTime()){
						$(".sel-buttons").append(option_b);
					}
				}else{
					$(".sel-buttons").append(option_b);
				}
				
			})
			
			if(numb==0){
				$(".sel-buttons").html('<div  class="alert">Sorry! There is no availability this day</div>');
			}
		
		
		}
	
	
	}
	
	$(function(){
		$('#day_booking').change(function(){check_values()})
		$('#hours_booking').change(function(){check_values()})
		
		var nowTemp = new Date();
		var nowFuture = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate()+10, 0, 0, 0, 0);
		var now  = new Date(nowTemp.getFullYear(), nowTemp.getMonth() , nowTemp.getDate(), 0, 0, 0, 0);
		$("#datepicker_container > div").datepicker({
			onRender : function(date){
				return $.inArray(date.toDateString(),valid_dates) > -1 ? 'bookable' : 'disabled';
			},
			
		}).on('changeDate',function(date){
			if(nowFuture.getDate() < date.date.getDate()){
				alert("You are booking more than 10 days away. Can you book sooner? Don't run out of time to book your lessons");
			}
			$("#day_booking").val(date.date.yyyymmdd());
			check_values();
		})
		
		$("td.day").removeClass("active");	
		
		
		
	})
	
	
</script>
<? } ?>
<script>
function cancel_f(obj){
	$("#id_cancel").val($(obj).data('id'));
	$('#cancelModal').modal('show');
}
function clean_form(){
	$("#start").val('');
	$("#end").val('');
	$("#group").val('');
}
</script>
