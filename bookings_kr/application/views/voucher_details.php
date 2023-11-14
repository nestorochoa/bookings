<?php include('include/header.inc.php')?>

<div class="row">
	<div class="span12">
    <? if(isset($info) && $info != ''){ ?>
    <? echo $info; ?>
    <? } ?>
    	<table class="table table-bordered" cellpadding="0" cellspacing="0">
        	<tr>
            	<th>Partial Code</th>
                <th>Redeemed</th>
                <th>Date</th>
                <th>User</th>
            </tr>
            
            <? foreach($voucher_detail->result() as $voucher){?>
            <tr>
            	<td><? echo $voucher->partial_code; ?></td>
                <td><? echo $voucher->redeemed == 1 ? 'Yes' : 'No'; ?></td>
                <td><? echo $voucher->redeem_date; ?></td>
                <td><? echo $voucher->user_id !== '0' ? $voucher->user_id . ' - ' . $voucher->usr_name . ' ' . $voucher->usr_surname : ''; ?></td>
             </tr>
            <? } ?>
        </table>  
    </div>
</div>


<?php include('include/footer.inc.php')?>