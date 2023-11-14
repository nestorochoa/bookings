<?php
use \Dlin\Saasu;
use \Dlin\Saasu\Entity;
use \Dlin\Saasu\Criteria;
use \Dlin\Saasu\Task\TaskList;




function saasu_new_student($webaccess,$fileaccess,$id_student) {

	$CI =& get_instance();
	$CI->db->where('usr_id',$id_student);
	$CI->db->join('student_details','st_id = usr_id');
	$usr_info = $CI->db->get('bk_users');
	$usr_row = $usr_info->row();
	$email_check = $usr_row->usr_email;
	
	$persons = $usr_row->st_n_students;
	$hours = $usr_row->st_hours;
	$payment_m_code = $usr_row->st_payment_m;

	if($payment_m_code == '' || $persons == ''){
		die('Not enough information to create an invoice, please check the persons and the hours that they are going to pay');
	}

	$CI->db->where('pm_id',$payment_m_code);
	$pay_check = $CI->db->get('payment_method');
	$pay_row = $pay_check->row();
	$pay_code = $pay_row->pm_code;
	
	$CI->db->where('shv_hours',$hours);
	$CI->db->where('shv_people',$persons);
	$res_q = $CI->db->get('student_hours_value');
	$res_row = $res_q->row();
	if(isset($res_row->shv_saasu_code)){
		$innerCode = $res_row->shv_saasu_code;
		$product_description = $res_row->shv_description;
	}else{
		die('Not enough information to create an invoice, please check the persons and the hours that they are going to pay (Saasu code error)');	
	}

	$saasu = new Saasu\SaasuAPI($webaccess,$fileaccess);
	
	$contact_search = new Criteria\ContactCriteria();
	$contact_search->searchFieldName = 'Name';
	$contact_search->searchFieldNameBeginsWith = $email_check;
	
	try{
		$results = $saasu->searchEntities($contact_search);
	}catch(Exception $e){
		$err = $e->getMessage();
		//report the exception
		echo $err . '<br/>';
		die('The system was unable to create the contact in Saasu, please try manually');
	}
	

	
	if(empty($results)){
	
		
		$new_contact = new  Entity\Contact();
		
		$new_contact->givenName = $res_row->usr_name;
		$new_contact->middleInitials;
		$new_contact->familyName = $res_row->usr_surname;
		
		$new_postal = new Entity\PostalAddress;

		$new_contact->email = $email_check;
		$new_contact->mainPhone = $res_row->usr_phone_main;

		$new_contact->mobilePhone = $res_row->usr_phone_main;

		try{
			$saasu->saveEntity($new_contact);
		}catch(Exception $e){
			$err = $e->getMessage();
			//report the exception
			echo $err . '<br/>';
			die('There is a problem with the creation of the contact in Saasu. please try manually.');
		}
		$contact_search_new = new Criteria\ContactCriteria();
		$contact_search_new->SearchFieldName = 'Name';
		$contact_search_new->SearchFieldNameBeginsWith = $email_check;
		//echo var_dump($contact_search);
		$results = $saasu->searchEntities($contact_search_new);
		if(empty($results)){
			die( __( 'There is a problem with the creation of the contact in Saasu. please try manually.', 'woocommerce' ) );
		}else{
			$saasu_contact_id = $results[0]->uid;
		}
		
	}else{
		$saasu_contact_id = $results[0]->uid;	
	}
	
		$items_invoice = array($innerCode);
		$group_items = array();
		$total_sale = 0;

		foreach($items_invoice as $item){
					
				
				
				$new_invoice_item = new Entity\ItemInvoiceItem();
				$new_invoice_item->quantity = 1;
				$new_invoice_item->inventoryItemUid =$item;
				
				$new_invoice_item->description = $product_description;
				
				$new_invoice_item->taxCode = 'G1';

			
				$errors = $new_invoice_item->validate()->getErrors();
				
				$group_items[] = $new_invoice_item;

			
		}
		
		
		
		$quickPay = new Entity\QuickPayment();
		$quickPay->datePaid = date('Y-m-d');
		$quickPay->bankedToAccountUid = $pay_code; // Hardcoded 617237
		$quickPay->amount = $total_sale;
		$invoice_new = new Entity\Invoice();
		$invoice_tag = new Entity\Tag();
		$invoice_tag->name = 'Online';
		$tags = array();
		$tags[] = $invoice_tag;	
		$invoice_new->transactionType = 'S';
		$invoice_new->invoiceType = 'Tax Invoice'; // 2
		$invoice_new->contactUid = $saasu_contact_id;
		$invoice_new->shipToContactUid = 0; //3
		//$invoice_new->externalNotes = ;
		//$invoice_new->dueOrExpiryDate; //4
		$invoice_new->layout = 'I'; //5
		//$invoice_new->status = 'I';
		$invoice_new->invoiceNumber = '<Auto Number>';
		//$invoice_new->purchaseOrderNumber = $order_obj->get_order_number(); order_number?!?!?
		$invoice_new->invoiceItems = $group_items;
		$invoice_new->quickPayment = $quickPay;
		//$invoice_new->tradingTerms;
		$invoice_new->isSent = 'false';
		//$invoice_new->totalAmountInclTax;
		//$invoice_new->totalAmountExclTax;
		//$invoice_new->totalTaxAmount;
		$invoice_new->autoPopulateFxRate = 'true';
		$invoice_new->date = date('Y-m-d');
		$invoice_new->tags = 'Online';
		//$invoice_new->summary;
		//$invoice_new->notes = $order_obj->customer_note;

		//$invoice_new->requiresFollowUp;
		//$invoice_new->ccy;
		//$invoice_new->fcToBcFxRate;
		
		
		
		
		try{
			$email = new Entity\EmailMessage();
			$email->to = $email_check; //'nestor_ochoa99@hotmail.com';//
			$email->from = "shop@kiterepublic.com.au";
			$email->subject = "Invoice for order " . $order_obj->get_order_number() ;
			$email->body = "Kite Republic Invoice.";
			
			$instruction = new Entity\InvoiceInstruction();
			$instruction->emailMessage = $email;
			$instruction->emailToContact = 'true';


			$saasu->saveEntity($invoice_new, $instruction);

			
			
		}catch(Exception $e){
			$err = $e->getMessage();
			//report the exception
			die($err);
		}


}