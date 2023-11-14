<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Voucher extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('session');
		ini_set('display_errors', '1');
		$gen_query = $this->db->get('Company');
		$gen_row = $gen_query->row();
		$this->basic_var['company_logo'] = base_url() . "front/img/" . $gen_row->company_logo;
		$this->basic_var['company_name'] = $gen_row->company_name;
		$this->basic_var['recordset_menu'] = NULL;
		$this->config_pag['per_page'] = 10;
		$this->config_pag['num_links'] = 10;
		$this->config_pag['uri_segment']= 2;

		
		
		if($this->session->userdata('user_id')){
			$this->load->helper('mysqli');
			$user_type = $this->session->userdata('user_type');
			$this->basic_var['recordset_menu']= $this->db->query("CALL menu_user_type('{$user_type}');");
			clean_mysqli_connection($this->db->conn_id); 
		}
		
		
		
	}
	
	
	public function coupon_list(){
		if($this->session->userdata('user_id') && $this->session->userdata('user_type') <= 2)
		{
			$data['basic_var'] = $this->basic_var;
			
			$this->db->order_by("date_creation");
			$data["vouchers"] = $this->db->get("voucher_head");
			$errors = '';
			
			$config['upload_path'] = './temp_files/';
			$config['allowed_types'] = 'xls|xlsx|cvs';
			
			$this->load->library('upload', $config);
			
			if ($this->upload->do_upload()) {
				$this->load->helper('excel');
				$data_upload = $this->upload->data();
				
				$date_exp = $this->input->post("date_exp");
				if(!$date_exp != ''){
					$date_exp = NULL;
				}
				
				
				$info = load_spreadsheet_db($data_upload['full_path'],$this->input->post('code_internal'), $this->input->post('description'), $this->input->post('number_students'), $this->input->post('special'), $this->input->post('number_hours'),$date_exp);
				
				
				//$file,$code,$description,$number_student,$special_group,$number_hours

				if($info['error'] != ''){
					$errors = $info['error'];        
				}else{
					$code = $this->input->post('code_internal');
				
				
				
					$this->db->where("out_id",$code);
					$this->db->order_by('partial_code');
					$this->db->join('bk_users','user_id = usr_id','left');
					$data["voucher_detail"] = $this->db->get("voucher_details");
					$data['info'] = $info['info'];
				
				
					$this->load->view('voucher_details',$data);
					return;
				}
				
			
			}else{
				$errors =  $this->upload->display_errors();
			}
			
			if(!isset($_POST['code_internal'])){
				$errors = '';
			}
			
			$data['errors'] = $errors;
			
			$this->load->view('list_voucher',$data);
			
			
			
			
		}else{
			header("Location: ".base_url());
		}
	}
	
	public function voucher_details(){
		if($this->session->userdata('user_id') && $this->session->userdata('user_type') <= 2)
		{
			$ts=$this->uri->total_segments();
			$offset= $this->uri->segment($ts); 
		
			$data['basic_var'] = $this->basic_var;
			
			$this->db->where("out_id",$offset);
			$this->db->order_by('partial_code');
			$this->db->join('bk_users','user_id = usr_id','left');
			$data["voucher_detail"] = $this->db->get("voucher_details");
			
			$this->load->view('voucher_details',$data);
			
		}else{
			header("Location: ".base_url());
		}
	}
	
	public function test_hour(){
		$ThatTime ="10:08:10";
		if (time() >= strtotime($ThatTime)) {
		  echo "ok";
		}
	
	}
	
	public function test_count(){
			$group = 1296;
			$student = 555;
			
			$this->db->where('st_id',$student);
			
			$student_info = $this->db->get('student_details');
			$s_info = $student_info->row();
			
			
			$this->db->where('sg_day',$group);
			$this->db->where('sg_status',0);
			$this->db->join('student_details','sg_student = st_id');
			$this->db->select('sum(st_n_students) as total');
			$query_count = $this->db->get("special_group");
			
			echo $this->db->last_query();
			
			$q_count = $query_count->row();
			$this->db->where('bk_id',$group);
			$query_day = $this->db->get('bk_days');
			$row_day = $query_day->row();
			
			echo $this->db->last_query();
			
			$data['warning'] = '';
			if($row_day->bk_special_num < ($q_count->total + $s_info->st_n_students)){
				$data['warning'] = 'There is no more students allowed to this class.';
			}else{
				$this->db->where('sg_day',$group);
				$this->db->where('sg_student',$student);
				$this->db->where('sg_status',0);
				$query_count_student = $this->db->get("special_group");
				if($query_count_student->num_rows() > 0){
					$data['warning'] = 'The student is already booked in this class' ;
				}
			
			
			}
			var_dump($data);
	
	
	
	}
	


}