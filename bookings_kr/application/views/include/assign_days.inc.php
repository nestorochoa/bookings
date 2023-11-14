
            
		<div class="alert alert-error" id="alert_date" style="display: none;">
			<strong>The end date can not be less then the start date</strong>
		  </div>
		<table class="table table-bordered" style="max-width:400px;">
			
				<tr>
					<td style="text-align:center"><a href="#" class="btn small" id="dp4" data-date-format="dd/mm/yyyy" data-date="<?echo $day_sel?>">Start date</a></td>
					<td style="text-align:center"><a href="#" class="btn small" id="dp5" data-date-format="dd/mm/yyyy" data-date="<?echo $day_sel?>">End date</a></td>
				</tr>
			
				<tr>
					<td style="text-align:center" ><input type="text" readonly value="<?echo $day_sel?>" id="startDate_td" name="startDate_td"  class="input-small"/></td>
					<td style="text-align:center"><input type="text" readonly value="<?echo $day_sel?>" id="endDate_td" name="endDate_td"  class="input-small"/></td>
				</tr>
				<tr>
					<td style="text-align:center">Groups</td>	
					<td style="text-align:center"><input id="n_instructor" name="n_instructor" type="text" placeholder="#" style="max-width:40px; text-align:left;" /></td>
				</tr>


		</table>
					<script>

					    
					function validate_lessons(){
						//var end_time = $("#timepicker_end").val();
						//var start_time = $("#timepicker_ini").val();
						var n_instructors = $("#n_instructor").val();
						var warning = "";
						//var diff = ( new Date("1970-1-1 " + end_time) - new Date("1970-1-1 " + start_time) ) / 1000 / 60 / 60;
						//if(diff<=0){
						//	warning += ' * The start time can not be greater that the end time. \n';
						//}
						
						if(startDate_tp.valueOf() > endDate_tp.valueOf()){
							warning += '* The start date can not be greater then the end date \n';
						}
						
						if(isNaN(n_instructors) || n_instructors==''){
							warning += '* The instructors field must be a number \n';
						}
						if(warning!=''){
							$('#alert').show().find('strong').text(warning);
							return false;
						}
						
						return true;
					
					}
			var startDate_i = '<?echo $day_sel?>';
			var start_i = startDate_i.split('/');
			var startDate_tp = new Date(start_i[2],start_i[1]-1,start_i[0]);
			var endDate_tp = new Date(start_i[2],start_i[1]-1,start_i[0]);
			
			$('#dp4').datepicker()
			.on('changeDate', function(ev){
				if (ev.date.valueOf() > endDate_tp.valueOf()){
					$('#alert_date').show().find('strong').text('The start date can not be greater then the end date');
				} else {
					var date_selected= ev.date;
					var dd = date_selected.getDate();
					var mm = date_selected.getMonth()+1;
					var yyyy = date_selected.getFullYear();
					var date_format = dd + '/' + mm + '/' + yyyy;
						
					$('#alert_date').hide();
					startDate_tp = new Date(ev.date);
					$('#dp4').data('date',date_format);
					$('#startDate_td').val($('#dp4').data('date'));
				}
				$('#dp4').datepicker('hide');
			});
			$('#dp5').datepicker()
			.on('changeDate', function(ev){
				if (ev.date.valueOf() < startDate_tp.valueOf()){
					$('#alert_date').show().find('strong').text('The end date can not be less then the start date');
				} else {
					var date_selected= ev.date;
					var dd = date_selected.getDate();
					var mm = date_selected.getMonth()+1;
					var yyyy = date_selected.getFullYear();
					var date_format = dd + '/' + mm + '/' + yyyy;
					
					
					$('#alert_date').hide();
					endDate_tp = new Date(ev.date);
					$('#dp5').data('date',date_format);
					
					$('#endDate_td').val($('#dp5').data('date'));
				}
				$('#dp5').datepicker('hide');
			});
			
			
</script>