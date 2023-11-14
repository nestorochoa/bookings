<?php 

	class Saasu 
	{
		public $version = '0.01';
		public static $plugin_prefix;
		public static $plugin_url;
		public static $plugin_path;
		public static $plugin_basefile;
		public static $webaccess;
		public static $fileaccess;
		public $saasu;
		public function __construct(){


			self::$plugin_path = dirname(__FILE__);
			$this->includes();
		}
		

		
		public function ajax_includes() {
			include_once('admin/new_students_saasu.php');
		}
		private function guzzle_includes(){
			include_once('Saasu/saasu_guzzle.php');
		}
		private function includes(){

			$this->ajax_includes();	
			$this->guzzle_includes();
			include_once('Saasu/Util/Throttler.php');	
			include_once('Saasu/SaasuAPI.php');
			include_once('Saasu/Entity/EntityBase.php');
			include_once('Saasu/Entity/Contact.php');
			include_once('Saasu/Entity/PostalAddress.php');
			include_once('Saasu/Entity/ItemInvoiceItem.php');
			include_once('Saasu/Entity/Invoice.php');
			include_once('Saasu/Entity/Tag.php');
			include_once('Saasu/Entity/QuickPayment.php');
			include_once('Saasu/Entity/InventoryItem.php');
			include_once('Saasu/Entity/TradingTerms.php');
			include_once('Saasu/Entity/QuickPayment.php');
			include_once('Saasu/Entity/EmailMessage.php');
			include_once('Saasu/Entity/TransactionCategory.php');
			include_once('Saasu/Entity/BankAccount.php');
			
			include_once('Saasu/Entity/InvoiceInstruction.php');
			include_once('Saasu/Criteria/CriteriaBase.php');
			include_once('Saasu/Criteria/ContactCriteria.php');
			include_once('Saasu/Criteria/InvoiceCriteria.php');
			include_once('Saasu/Criteria/FullInventoryItemCriteria.php');
			include_once('Saasu/Criteria/BankAccountCriteria.php');
			include_once('Saasu/Task/Task.php');
			include_once('Saasu/Task/TaskList.php');
			include_once('Saasu/Task/TaskResult.php');
			include_once('Saasu/Validator/Validator.php');
			
			
			

		}
	}
	
	

?>