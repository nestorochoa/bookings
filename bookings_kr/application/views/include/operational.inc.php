<style>
	.btn_calendar{
		margin-bottom:0px !important
	}
	.hd_table th{
		text-align:center !important;
	}
	.select_hour{
		width:93px;
	}
	table tr td{
		text-align:center !important;
	}
	
</style>
<h1>Operational hours</h1>
<form id="op_form" name="op_form" action="<?echo base_url()?>operational_hours" method="post" class="form-inline"   />
<div class="row-fluid">
<?
	$month_m = date('n');
	$year_m = date('Y');
	if($this->input->post("month")){$month_m =$this->input->post("month");}
	if($this->input->post("year")){$year_m = $this->input->post("year");}
?>
	<div class="span4"><label class="control-label">Month</label><select id="month" name="month"><?foreach($months->result() as $month){?>
	<option value="<?echo $month->value?>" <? echo $month_m == $month->value ? 'selected' : '' ;?>><?echo $month->desc ?></option>
	<?}?></select></div>
	<div class="span4"><label class="control-label">Year</label><select  id="year" name="year"><?foreach($years->result() as $year){?>
	<option value="<?echo $year->year?>" <? echo $year_m == $year->year ? 'selected' : '' ;?>><?echo $year->year ?></option>
	<?}?></select></div>
	<div class="span4"><button class="btn" type="submit" >Search</button></div>
</div>
</form>
<?if ($main_table!=null){?>
<div class="row-fluid">
<table class="table table-bordered">
<tr class="hd_table">
	<th>Date</th>
	<th>From</th>
	<th>To</th>
	<th># Instructors</th>

	
</tr>
<?
	$count = 1;	

foreach($main_table->result() as $date){?>
<tr class="type_<?echo $count?>">
<?
	$hour_ini = '10:00:00';
	$hour_end = '21:00:00';
	if($date->dl_hour_ini!=''){
		$hour_ini = $date->dl_hour_ini;
	}
	if($date->dl_hour_end!=''){
		$hour_end = $date->dl_hour_end;
	}
?>

	<td><?echo $date->day; ?><input type="hidden" name="date_<?echo $count?>" id="date_<?echo $count?>" value="<? echo $date->day ?>" /></td>
	<td><select id="ini_<?echo $count?>" name="ini_<?echo $count?>"  class="select_hour" onchange="check_hour(<?echo $count?>);" ><?foreach($hours->result() as $hour	){?>
		<option value="<?echo $hour->hour_24 ?>" <?echo $hour_ini == $hour->hour_24 ? 'selected' : ''?>><?echo $hour->hour_24 ?></option>
	<?}?></select></td>
	<td><select id="end_<?echo $count?>" name="end_<?echo $count?>"  class="select_hour"  onchange="check_hour(<?echo $count?>);"><?foreach($hours->result() as $hour	){?>
		<option value="<?echo $hour->hour_24 ?>" <?echo $hour_end == $hour->hour_24 ? 'selected' : ''?>><?echo $hour->hour_24 ?></option>
	<?}?></select></td>
	<td><?echo $date->instructors; ?></td>
	<td style="text-align:center;"><i class="icon-warning-sign" id="war_<?echo $count?>" style="display:none;" ></i></td>

</tr>
<?
	$count += 1;
}?>
</table>
<? } ?>
</div>
<script>
function check_hour(ind){
	var date = $("#date_"+ind);
	var hour_ini = $("#ini_"+ind);
	var hour_end = $("#end_"+ind);
	
	var params = {'date' : date.val() , 'hour_ini' : hour_ini.val(), 'hour_end' : hour_end.val()};
	var response;
	
	var re= $.ajax({
		url : base_url + 'welcome/change_operational',
		type : 'post',
		dataType : 'json',
		data: params,
		async : false,
		success : function (data){
			response = data;
		}
	})
	
	hour_ini.val(response.hour_ini);
	hour_end.val(response.hour_end);
	
	if(response.warning != ''){
		$("#war_"+ind).fadeIn("fast");
		$("#war_"+ind).fadeOut(1000);	
	}

}
</script>