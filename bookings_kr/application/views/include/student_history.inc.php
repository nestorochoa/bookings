
<div class="well">
<? if(isset($information)){ 
	foreach($basic_info->result() as $deg){
	
	$hours = $deg->st_hours ;
	$h_confirm = round(cal_hours_left_confirmed($deg->usr_id), 2);
	$h_booked = round(cal_hours_left_booked($deg->usr_id), 2);
	$h_penalty = round(cal_hours_left_penalty($deg->usr_id), 2);
		
	$h_left = $hours - ($h_confirm + $h_booked + $h_penalty);
	
	?><h1><? echo $deg->usr_name ?> <? echo $deg->usr_surname ?></h1>
	<h2>Hours : <? echo $hours?>,  H. Done : <? echo $h_confirm?>, H. Booked : <? echo $h_booked?>, H. Penalty : <? echo $h_penalty?>, H. Left : <? echo $h_left?></h2>
	<?
	if($deg->st_observations != ''){
		?>
	<p>Observations:<br/><? echo $deg->st_observations ?></p>
		<?
	}
	
	}
?>

<? } ?>
<table  class="table table-bordered">
<tr>
	<th>Day</th>				
	<th>Instructor</th>
	<th>From</th>
	<th>To</th>
	<th>Status</th>
    <th>Lesson Level</th>
    <th>Special</th>
</tr>
<?
	foreach($information->result() as $item){
?>
<tr>
	<td><? echo $item->bd_date ?></td>
    <td><? echo $item->usr_name . ' ' . $item->usr_surname ?></td>
	<td><? echo $item->hour_from ?></td>
    <td><? echo $item->hour_to ?></td><?
    	$status = '';
		if($item->bk_level != 3){
			$status = $item->bk_status == 0 ? 'Booked' : ($item->bk_status == 1 ? 'Cancelled' : ($item->bk_status == 2 ? 'User Cancelled' : ($item->bk_status == 3 ? 'Complete' : 'Undefined')));
		}else{
			//$status = $item->sg_status == 0 ? 'Booked' : ($item->sg_status == 1 ? 'Cancelled' : ($item->sg_status == 2 ? 'User Cancelled' : ($item->bk_status == 3 ? 'Complete' : 'Undefined')));
			//$status = $item->bk_status == 3 ? 'Complete' : ($item->sg_status == 1 ? 'Cancelled' : ($item->sg_status == 2 ? 'User Cancelled' : ($item->bk_status == 0 ? 'Booked' : 'Undefined')));
			$status = $item->sg_status == 2 ? 'User Cancelled' : ($item->bk_status == 1 ? 'Cancelled' : ($item->bk_status == 0 ? 'Booked' : ($item->bk_status == 3 ? 'Complete' : 'Undefined')));
		}
	?>
    <td><? echo $status ?></td>
    <td><? echo $item->sl_description ?></td>
	<td><? echo $item->bk_level == 3 ? 'Yes' : 'No' ; ?></td>

</tr>
<?
	}
?>




</table>
</div>
<? if(isset($voucher)){ ?>
<div class="well">
	<table  class="table table-bordered">
	<tr>
		<th>Group Code</th>				
		<th>Date Redeem</th>
		<th># Students </th>
		<th>Special</th>
		<th># Hours</th>
		<th>Voucher Code</th>
		<th>Reedem Date</th>
		<th>Expiry Date</th>
		
	</tr>
	<? foreach($voucher->result() as $vv){ ?>
	<tr>
		<td><? echo $vv->code_group    ?></td>
		<td><? echo $vv->description    ?></td>
		<td><? echo $vv->number_student    ?></td>
		<td><? echo $vv->special_group    ?></td>
		<td><? echo $vv->number_hours    ?></td>
		<td><? echo $vv->partial_code    ?></td>
		<td><? echo $vv->redeem_date    ?></td>
		<td><? echo $vv->expiry_date    ?></td>
	</tr>
	<? } ?>
</div>
<? } ?>
