<script type="text/javascript" src="<?php echo base_url() ?>front/js/calendar.js?1234657878"></script>

<?
function getTimes()
{
	$times = array(
		'08:00:00' => '8am',
		'09:00:00' => '9am',
		'10:00:00' => '10am',
		'11:00:00' => '11am',
		'12:00:00' => '12pm',
		'13:00:00' => '1pm',
		'14:00:00' => '2pm',
		'15:00:00' => '3pm',
		'16:00:00' => '4pm',
		'17:00:00' => '5pm',
		'18:00:00' => '6pm',
		'19:00:00' => '7pm',
		'20:00:00' => '8pm',
		'21:00:00' => '9pm',
		'22:00:00' => '10pm'
	);
	return $times;
}
$times = getTimes();
?>

<div class="row-fluid">
	<div class="alert alert-error" id="message_error" style="display:none" onclick="$('#message_error').fadeOut(1000);"></div>
	<div class="alert alert-block" id="message_q" style="display:none" onclick="$('#message_q').fadeOut(1000);"></div>

</div>
<div class="row-fluid">
	<div class="container_cal">
		<div class="calendar">
			<div class="background_block"></div>
			<div class="cal_header">

				<?php
				$var_ids = '';
				$ix = 1;
				foreach ($day_schedule->result() as $item) {
					if ($var_ids == '') {
						$var_ids .= $item->bd_id;
					} else {
						$var_ids .= ',' . $item->bd_id;
					}
				?>
					<div class="day_header" data-id="<? echo $item->bd_id ?>" data-index="<? echo $ix ?>">
						<?
						$instructor_name = 'Instructor No ' . $ix;
						if ($item->bd_instructor != null) {
							$instructor_name = $item->usr_name . ' ' . $item->usr_surname;
						} ?>
						<div class="title_row" data-id="<? echo $item->bd_instructor ?>"><? echo $instructor_name ?></div><span class="icon-remove remove_row" data-code="<? echo $item->bd_id ?>"></span><input class="sms_instructor" type="checkbox" value="<? echo $item->bd_instructor ?>" />
					</div>
				<?
					$ix += 1;
				} ?>
			</div>
			<div class="cal_body">
				<div class="cal_times">
					<?php

					foreach ($times as $time => $description) {

					?>

						<div id="t_<?php echo $time ?>" class="cal_time"><?php echo htmlentities($description) ?></div>
					<?php } ?>
				</div>
				<div class="cal_days">
					<? foreach ($day_schedule->result() as $item) { ?>
						<div id="day_<?php echo $item->bd_id ?>" class="cal_day" data-id="<?php echo $item->bd_id ?>">
							<?php for ($i = 0; $i < 60; $i++) { ?>
								<div id="<?php echo $i . "_" . $item->bd_id ?>" class="cal_half_hour"></div>
							<?php } ?>
						</div>
					<? } ?>
				</div>
				<div class="wishlist_container">
					<div id="datepicker_container">
						<div></div>
					</div>
				</div>


				<div class="wishlist" id="wishlist_day">
					<div class="title_row">Wishlist</div>



				</div>
			</div>

		</div>


	</div>

	<div id="hidden_box" style="display:none">
		<select id="ddl_instructors" name="ddl_instructors" />
		<option value="">None</option>
		<? foreach ($instructors->result() as $one) { ?>
			<option value="<? echo $one->usr_id ?>"><? echo $one->usr_name . ' ' . $one->usr_surname ?></option>
		<? } ?>
		</select>
	</div>

	<script>
		$(function() {
			Calendar.init('<? echo $var_ids ?>');
		})

		function add_sms(ind) {
			var org = $("#text_sms_custom").val()
			if (ind == 1) {
				$("#text_sms_custom").val(org + '[name]');
			}
			if (ind == 2) {
				$("#text_sms_custom").val(org + '[date]');
			}

		}
	</script>

</div>