<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends CI_Controller
{

	public function __construct()
	{
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
		$this->config_pag['uri_segment'] = 2;

		if ($this->session->userdata('user_id')) {
			$user_type = $this->session->userdata('user_type');
			$this->basic_var['recordset_menu'] = $this->model_procedure->menu_user_type($user_type);
		}
	}

	public function student_info()
	{

		if ($this->session->userdata('user_id') && $this->session->userdata('user_type') <= 2) {

			$ts = $this->uri->total_segments();
			$offset = $this->uri->segment($ts);

			$this->load->helper('misc');

			if (is_numeric($offset)) {
				$data['basic_var'] = $this->basic_var;


				$select_basic = 'Select * From bk_users Inner Join student_details On usr_id = st_id where usr_id = ' . $offset;

				$select_temp = 'SELECT * FROM (bk_days) LEFT JOIN special_group ON sg_day = bk_id JOIN bk_day_groups ON bd_id = bk_group LEFT JOIN bk_users instructor ON bd_instructor = instructor.usr_id  LEFT JOIN student_level On bk_lesson_level = sl_id WHERE IFNULL(bk_student,sg_student) = ' . $offset;

				$temp = $this->db->query($select_temp, FALSE);

				$select_vouchers = 'SELECT * FROM voucher_details INNER JOIN voucher_head On out_id = code_group INNER JOIN bk_users On usr_id = user_id
				WHERE usr_id = ' . $offset;

				$data['information'] = $temp;
				$data['basic_info'] = $this->db->query($select_basic, FALSE);
				$data['voucher'] = $this->db->query($select_vouchers, FALSE);

				/*$this->db->where('student.usr_id',$offset);
				$this->db->join('special_group','sg_day = bk_id','left');
				$this->db->join('bk_day_groups','bd_id = bk_day');
				$this->db->join('bk_users instructor', 'bd_instructor = instructor.usr_id','left');
				$this->db->join('bk_users student', 'IFNULL(bk_student,sg_student) = student.usr_id','left');
				$temp = $this->db->get('bk_days');*/


				$this->load->view('student_details', $data);
			}
		} else {
			header("Location: " . base_url());
		}
	}
}
