<?
	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	

	/** Include PHPExcel */
	require_once 'phpexcel/PHPExcel.php';
	
	function load_spreadsheet_db($file,$code,$description,$number_student,$special_group,$number_hours,$exp_date){
		$detectCol = '-1';
		$info = '';
		$error ='';
		
		$CI =& get_instance();
		
		try {
			$Reader = PHPExcel_IOFactory::createReaderForFile($file);
			$Reader->setReadDataOnly(true); 
			$objXLS = $Reader->load($file);
		
		
		
		
	
		} catch (Exception $e) {
			$error = 'Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage();
		}
		
		
		$CI->db->where('code_group',$code);
		$code_ver = $CI->db->get('voucher_head');
		
		if($code_ver->num_rows() > 0){
			$error = 'This Code is already in the system, please write other one';
		}
		
		if($error == ''){
		
				$sheet = $objXLS->getSheet(0);
				$highestRow = $sheet->getHighestRow();
				$highestColumn = $sheet->getHighestColumn();
				
				//  Loop through each row of the worksheet in turn (Search for title Special Code)
				
				
				
				$rowData = $sheet->rangeToArray('A1:' . $highestColumn . '1', NULL, TRUE, FALSE);
				
				
				
				
				foreach($rowData[0] as $k=>$v){
					
					$new_value = str_ireplace("_"," ", $v);
					$new_value = str_ireplace("-"," ", $new_value);
					$new_value = str_ireplace(" ","", $new_value);
					$new_value = strtolower($new_value);
					echo $new_value . '--' . $k;
					if($new_value == 'specialcode'){
						$detectCol = $k;
					}
					echo ' - ' . $detectCol;
					
					
					
				}
				
				$insert_something = 0;
				
				if($detectCol == '-1'){
					$error = 'There is no column named Special Code, Check your file';
				}else{
				
					
					for ($row = 2; $row <= $highestRow; $row++) {
						//  Read a row of data into an array
						$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
						foreach($rowData[0] as $k=>$v)
							if($k == $detectCol && trim($v) != ''){
								
								$code_temp = str_ireplace("x","_",$v);
								
								$CI->db->where("partial_code",$code_temp);
								
								$partial_search = $CI->db->get("voucher_details");
								
								$row_count = $partial_search->num_rows();
								
								$insert = true;
								
								if($row_count > 0 && !stripos($code_temp, '_')){
									$insert = false;
									$info .= '<tr><td class="danger">'.$v.' Code is already used</td></tr>';
								}elseif($row_count > 0){
									$info .= '<tr><td class="warning">'.$v.' Code is already used, but it have wildcards. It is stored on the system</td></tr>';
								}
								
								
								if($insert && trim($v) != ''){
									$array_insert = array(
										'out_id' => $code,
										'partial_code' => $v,
										'redeemed' => '0',
										
									);
									$CI->db->insert('voucher_details',$array_insert);
									
									if($CI->db->affected_rows() > 0){
										$insert_something++;
									}
								}
								
								
							}
						}
				
					}
				
					$info = $info != '' ? '<table class="table">' . $info . '</table>' : '';
					
					
					
					
					if($insert_something > 0){
						$array_insert = array(
							'code_group' =>  $code,
							'description' =>  $description,
							'number_student' => $number_student,
							'special_group' => $special_group,
							'number_hours' => $number_hours,
							'expiry_date' => $exp_date
							
						);
						$CI->db->insert('voucher_head',$array_insert);
						
					}
					
					
			}
		$objXLS->disconnectWorksheets();
		unset($objXLS);
		
		$data_temp['error'] = $error;
		$data_temp['info'] = $info;
		
		return $data_temp;
		
		
	
	}
?>