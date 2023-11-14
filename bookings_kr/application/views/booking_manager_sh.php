<style>
table tr td{
 border:1px solid black
}
</style>
<?
$times = array(
			'09:00:00' => '9am',
			'09:30:00' => '9am',
			'10:00:00' => '10am',
			'10:30:00' => '10am',
			'11:00:00' => '11am',
			'11:30:00' => '11am',
			'12:00:00' => '12pm',
			'12:30:00' => '12pm',
			'13:00:00' => '1pm',
			'13:30:00' => '1pm',
			'14:00:00' => '2pm',
			'14:30:00' => '2pm',
			'15:00:00' => '3pm',
			'15:30:00' => '3pm',
			'16:00:00' => '4pm',
			'16:30:00' => '4pm',
			'17:00:00' => '5pm',
			'17:30:00' => '5pm',
			'18:00:00' => '6pm',
			'18:30:00' => '6pm',
			'19:00:00' => '7pm',
			'19:30:00' => '7pm',
			'20:00:00' => '8pm',
			'20:30:00' => '8pm',
			'21:00:00' => '9pm',
			'21:30:00' => '9pm',
			'22:00:00' => '10pm',
			'22:30:00' => '10pm' );
$record_n = 0;
$query_row = $book->num_rows() 
	
  
?>

<table style="border:1px solid black">
<tr>
	<td></td><?
	
	$num = 1;
	$mem_span = array();
	$mem_span_left = array();
	foreach($book_hd->result() as $head){?>
	<td><? echo $head->instructor == "" ? "Instructor No #" .$num : $head->instructor   ?></td>
	<? 
	$mem_span[$num] = $head->bd_id;
	$mem_span_left[$num] = 1;
	$num += 1;
	} ?>
</tr>
<?foreach ($times as $time => $desc){?>
<tr>
	<td><?echo $time?></td>
	<? for($ind=1;$ind<$num;$ind++){ ?>
	<?
		$bd_code = $mem_span[$ind];
		$info = '';
		$diff = 1;
		if($query_row > $record_n){
			$row_s = $book->row($record_n);
			
			if($row_s->hour_from == $time && $row_s->bd_id == $bd_code){
				$info =  $row_s->hour_from . ' - ' .$row_s->hour_to . '<br/>' . $row_s->student;
				$diff = (strtotime($row_s->hour_to) - strtotime($row_s->hour_from))/(60*30);
				$record_n += 1;
				
			}	
		}
	
	if($mem_span_left[$ind] == 1){
	?>
	<td rowspan="<?echo $diff!=1 ? $diff : 1; ?>"><? echo $info?></td>
	<?
		$mem_span_left[$ind] = $diff;
	
	}else{
		$mem_span_left[$ind] -= 1;
	}
	}?>
</tr>
<?}

//echo (strtotime('22:00:00') - strtotime('20:00:00'))/(60*30)

?>
