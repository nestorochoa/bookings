<style>
	.btn_calendar{
		margin-bottom:0px !important
	}
</style>
<div class="row-fluid">
<form id="report_form" name="report_form" method="post" action="<?echo base_url()?>lesson_hours"  />
	<div class="span4 ">
	<div>
		From : <input type="text" name="date_ini" id="date_ini" value="<?echo $this->input->post("date_ini")?>" class="btn_calendar" />
		<a href="#" class="btn small" id="start_date" data-date-format="dd/mm/yyyy" ><i class="icon-calendar"></i></a>
		</div>
	</div>
	<div class="span4">
		<div >
		To :  <input type="text" name="date_end" id="date_end" value="<?echo $this->input->post("date_end")?>"  class="btn_calendar"/>
		<a href="#" class="btn small" id="end_date" data-date-format="dd/mm/yyyy" ><i class="icon-calendar"></i></a>
		</div>
	</div>
	<div class="span4">
		<button class="btn btn-info" type="submit" >Search</button>
	</div>
</div>
<div class="row-fluid">
   <p> </p>
</div>
<?
if(isset($search)){
?>
<div class="row-fluid">
	<table class="table">
		<tr>
		<td>Instructor</td>
		<td>Hours</td>
		</tr><?
	foreach($search->result() as $item){
	?>
	<tr>
		<td><? echo $item->instructor?></td>
		<td><? echo $item->Count?></td>
	</tr>	
	<?
	}
	?>
	</table>
	
</div>
<?
}
?>
<script>
	$(function(){
		$("#start_date").datepicker()
		.on('changeDate', function(ev){
				
			var date_selected= ev.date;
			var dd = date_selected.getDate();
			var mm = date_selected.getMonth()+1;
			var yyyy = date_selected.getFullYear();
			var date_format = dd + '/' + mm + '/' + yyyy;


			$('#start_date').data('date',date_format);
			$('#date_ini').val($('#start_date').data('date'));
				
			$('#start_date').datepicker('hide');
		});
		
		$("#end_date").datepicker()
		.on('changeDate', function(ev){
				
			var date_selected= ev.date;
			var dd = date_selected.getDate();
			var mm = date_selected.getMonth()+1;
			var yyyy = date_selected.getFullYear();
			var date_format = dd + '/' + mm + '/' + yyyy;

			$('#end_date').data('date',date_format);
			$('#date_end').val($('#end_date').data('date'));
				
			$('#end_date').datepicker('hide');
		});
	})
</script>