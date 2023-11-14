<?php include('include/header.inc.php')?>

<div class="row">
	
	<div class="span12">
    <div class="alert alert-info" role="alert">
    	<ul>
        	<li>The spreadsheet must have xls or xlsx extension</li>
            <li>The Column with the codes have to be called 'Special Code' (It's not case sensitive)</li>
            <li>The X is going to be the wildcard symbol</li>
            
        </ul>
    </div>
    <? if(isset($errors) && $errors != ''){ ?>
    <div class="alert alert-danger" role="alert">
    	<? echo $errors; ?>
    </div>
    <? } ?>
    <form action="" method="post" enctype="multipart/form-data">
    	<table class="table table-bordered" cellpadding="0" cellspacing="0">
        	<tr>
            	<th style="width:8%">Int Code</th>
                <th>Description</th>
                <th># Student</th>
                <th>Special Group</th>
                <th># Hours</th>
                <th>Expiry Date</th>
                <th>Date Creation</th>
                <td></td>
            </tr>
            
            <? foreach($vouchers->result() as $voucher){?>
            <tr>
            	<td><a href="<? echo base_url() . 'voucher_details/' . $voucher->code_group; ?>"><? echo $voucher->code_group; ?></a></td>
                <td><? echo $voucher->description; ?></td>
                <td><? echo $voucher->number_student; ?></td>
                <td><? echo $voucher->special_group == 1 ? 'Yes' : 'No'; ?></td>
                <td><? echo $voucher->number_hours; ?></td>
                <td><? echo $voucher->expiry_date; ?></td>
                <td><? echo $voucher->date_creation; ?></td>
                <td></td>
             </tr>
            <? } ?>
            
           <tr>
            	<td><input type="text" style="width:80%" value="" name="code_internal" id="code_internal" required /></td>
                <td><input type="text" style="width:80%" value="" name="description" id="description" required /></td>
                <td><select name="number_students" required style="width: 50px;" ><option value="">--</option>
                	<option value="1">1</option><option value="2">2</option><option value="3">3</option>
                </select></td>
                <td><input type="checkbox" name="special" id="special" value="1" checked="checked"/></td>
                <td><select name="number_hours" required style="width: 50px;"><option value="">--</option>
                	<option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option>
                </select></td>
                
                <td><input type="text" name="date_exp" id="date_exp" value="" style="width: 75px;" readonly="readonly" /></td>
                <td><input type="file" name="userfile" required   /></td>
                <td><input type="submit" value="Upload" /></td>
             </tr>
           
        </table>  </form>
    </div>
</div>
<script>
	
	$('#date_exp').datepicker({
		format: 'yyyy-mm-dd',
	});

</script>

<?php include('include/footer.inc.php')?>