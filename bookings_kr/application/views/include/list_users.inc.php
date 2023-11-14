<?
if($warning!=''){
?>
 <div class="alert alert-danger"><?echo $warning?></div>
<?
} else {
?>
<form action="<?echo base_url()?>user_edit" id="form_edit" name="form_edit" method="post" >
    	<input type="hidden" id="user_type_form" name="user_type_form" value="<?echo $user_level?>" />
	<input type="hidden" id="user_id_form" name="user_id_form" value="" />
    </form>
<div class="btn-toolbar">
    <button class="btn btn-primary" onclick="submit_form('New');">New <? 
    	$value_search='';
	$extra = '';
    	if($user_level==1){
		echo 'User';
		$value_search = 'users';
		
	}
	if($user_level==2){
		echo 'School Manager';
		$value_search = 'user_school';		
	}
	if($user_level==3){
		echo 'Instructor';
		$value_search = 'instructors';
	}
	if($user_level==4){
		echo 'Student';
		$value_search = 'students';
		$extra = '<th>Hours</th><th>H. Done</th><th>H. Booked</th><th>H. Penalty</th><th>H. Left</th>';
		$this->load->helper('misc');
	}
    ?></button>
</div>
<div class="well">
<table  class="table">
<tr>
	<th>Name</th>				
	<th>Surname</th>
	<th>Mobile</th>
	<th>Email</th><?php
	if($user_level==3){ ?>
	<th>Hours</th>
	<?php }
	echo $extra
	?>
	<th>Active</th>
	<th>Action</th>
</tr>
<?
	foreach($user_list->result() as $item){
?>
<tr><?
$special = '';
$usr_name = $item->usr_name;
$usr_surname = $item->usr_surname;
if($user_level==4){ 

	$usr_name = '<a href="'.base_url().'student_details/'.$item->usr_id.'">' . $usr_name . '</a>';
	$usr_surname = '<a href="'.base_url().'student_details/'.$item->usr_id.'">' . $usr_surname . '</a>';

	if($item->st_special == 1){
		$special = '<i class="icon-gift" style="margin-right: 9px;"></i>';
	}
}?>
	<td><?echo $special?><? echo $usr_name  ?></td>
	<td><? echo $usr_surname ?></td>
	<td><? echo $item->usr_phone_main ?></td>
	<td><? echo $item->usr_email ?></td><?
	if($user_level==4){
		$hours = $item->st_hours ;
		$h_confirm = round(cal_hours_left_confirmed($item->usr_id), 2);
		$h_booked = round(cal_hours_left_booked($item->usr_id), 2);
		$h_penalty = round(cal_hours_left_penalty($item->usr_id), 2);
		
		$h_left = $hours - ($h_confirm + $h_booked + $h_penalty);
		 
	?>
	<td><? echo $hours ?></td>
	<td><? echo $h_confirm; ?></td>
    <td><? echo $h_booked; ?></td>
    <td><? echo $h_penalty; ?></td>
	<td><? echo $h_left; ?></td>
    <? 
	}
	if($user_level==3){
	?><td><a href="<?php echo base_url() ?>extract/<?php echo $item->usr_id ?>" target="_blank"><i class="icon-book"></i></a></td><?php 
	}
	?>
	<td>
		<label class="switch">
		  <input type="checkbox" class="active_checkbox" <?php echo ($item->usr_deactive == 0 ? 'checked' : ''); ?> data-id="<?php echo $item->usr_id; ?>">
		  <div class="slider"></div>
		</label>
	</td>
			<td>
              <a class="data_edit" href="javascript:submit_form('<? echo $item->usr_id ?>')"><i class="icon-pencil"></i></a>
              <a href="#myModal" role="button" data-toggle="modal" data-id="<? echo $item->usr_id ?>" class="data_del"><i class="icon-remove"></i></a>
          </td>
</tr>
<?
	}
?>
</div>
<div class="modal small hide fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">�</button>
        <h3 id="myModalLabel">Delete Confirmation</h3>
    </div>
    <div class="modal-body">
        <p class="error-text">Are you sure you want to delete the user?</p>
    </div>
    <div class="modal-footer">
    <form method="post" action="<?echo base_url()?>welcome/delete_user">
		<input type="hidden" id="user_type_form" name="user_type_form" value="<?echo $user_level?>" />
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button class="btn btn-danger"  type="submit">Delete</button>
		<input name="id_delete" id="id_delete" value="" type="hidden" />
	</form>
    </div>
    
</div>
<div class="pagination">
<?echo $links_pag?>
<div class="search_users">
  <div class="input-append">
  <form id="search_form" name="search_form" action="<?echo base_url() . $value_search?>" method="post	" />
    <input type="text" class="span2 search-query" name="search_user" id="search_user" value="<?echo $this->input->post('search_user')?>" >
    <button type="submit" class="search-btn btn">Search</button>
  </div>
</div>
</div>
<script>
$(document).ready(function () {
    $(".data_del").click(function () {
        $('#id_delete').val($(this).data('id'));
    });
	$(".active_checkbox").click(active_switch);
});
function submit_form(id){
	$('#user_id_form').val(id);
	$('#form_edit').submit();
}

	function active_switch(event){
		event.preventDefault();
		var element = $(event.target);
		$.ajax({
			url : '<?php echo base_url()?>welcome/activate_user',
			data : { id : element.data('id') },
			type : "POST",
			dataType : "json"
		}).done(function(response){
			var deactive = response.deactive == 0;
			element.prop('checked',deactive);
		})
	}
	
	
</script>

<?
}
?>
