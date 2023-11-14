<style>
	.ui-resizable-s {
		bottom: 0;
		height: 30px;
		//background-color:#359AF7;
	}
</style>

<script>
	var formatDate = (dateTransform) => {
		return `${dateTransform.getFullYear()}-${dateTransform.getMonth()}-${dateTransform.getDate()}`
	}
	var general_date = '<?echo $day_sel?>';
	var freeDates = JSON.parse('<?echo $freeDates?>').map((elm) => {
		elm.realDate = new Date(elm.bd_date)
		return elm
	});



	$(function() {

		$("#datepicker_container div").data({
			date: '<?echo $day_sel?>'
		})
		$("#datepicker_container div").datepicker({
			format: "dd/mm/yyyy",
			beforeShowDay: (date) => {
				var cond = freeDates.some((elm) => formatDate(elm.realDate) === formatDate(date) && elm.available === "1");
				console.log(cond)
				return {
					enabled: true,
					classes: cond ? 'highlight' : ''
				}
			},
			todayHighlight: true
		}).on('changeDate', function(ev) {
			date_selected = ev.date;
			var dd = date_selected.getDate();
			var mm = date_selected.getMonth() + 1;
			var yyyy = date_selected.getFullYear();
			var date_format = dd + '/' + mm + '/' + yyyy;
			$("#date_sel").val(date_format);

			$("#form_post").submit();
		}).on('changeMonth', function(ev) {
			date_selected = ev.date;
			var mm = date_selected.getMonth() + 1;
			var yyyy = date_selected.getFullYear();
			console.log(mm, yyyy)
		})





	});
</script>

<style>


</style>

<form id="form_post" method="post" action="<?echo base_url() ?>bookings">
	<input type="hidden" id="date_sel" name="date_sel" value="" />
</form>

<?
	if($day_schedule->num_rows()>0){
?>
<? include('calendar.inc.php');?>

<?}else{?>
<div class="alert alert-error" id="alert">
	<strong>There is no instructors assigned to this date. Please assign one.</strong>
</div>
<div class="btn-group">
	<form id="add_column" name="add_column" method="post" action="<?echo base_url() ?>bookings">
		<input type="hidden" id="add_ins" name="add_ins" value="1" />
		<input type="hidden" id="date_sel" name="date_sel" value="<? echo $this->input->post('date_sel')?>" />
		<input type="hidden" id="del_ins" name="del_ins" value="" />
		<input type="hidden" id="col_id" name="col_id" value="" />
		<button class="btn  btn-success" type="submit">Add Instructor</button>
	</form>
</div>
<div class="wishlist_container">
	<div id="datepicker_container">
		<div></div>
	</div>
</div>
</div>
<?}?>



<div class="modal fade" id="new_lessons_modal" style="display:none;" tabindex="-1" role="dialog" aria-labelledby="New Lessons" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Add available lessons</h4>
			</div>
			<form id="add_lessons" method="post" action="<?echo base_url()?>welcome/add_lessons" />
			<div class="modal-body" style="text-align:center">
				<? include ('assign_days.inc.php')?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary" onclick="return validate_lessons();">Save changes</button>
			</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->