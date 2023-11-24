<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */

	public $basic_var = [];

	public function __construct()
	{
		parent::__construct();
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

	public function start()
	{
		if ($this->session->userdata('user_id')) {

			$this->db->where('usr_id', $this->session->userdata('user_id'));
			$this->db->join('student_details', 'st_id = usr_id');
			$user_qb = $this->db->get('bk_users');
			$user_info_b = $user_qb->row();
			$data['success'] = '';

			if ($this->input->post('but_book')) {
				//$data['warning'] = 'Its On';
				$start_time = $this->input->post('start');
				$end_time = $this->input->post('end');
				$group_day = $this->input->post('group');


				$query_booking = "SELECT bk_student,sg_student,sg_status,bk_status,bk_level
										FROM `bk_days` 
										Inner join bk_day_groups On bd_id = bk_group
										left join special_group On sg_day = bk_id and sg_status = 0
										Where (bd_inactive is Null or bd_inactive != 1) And bk_status = 0 And bk_level != 2 And IfNull(bk_student,sg_student) = " . $this->session->userdata('user_id') . "
										Order by bk_student,sg_student";
				$query_result_b = $this->db->query($query_booking);

				if ($query_result_b->num_rows() == 0) {

					if ($user_info_b->st_special != 1) {

						$type = 1;
						$student = $this->session->userdata('user_id');

						$response = $this->save_lesson_ci($start_time, $end_time, $group_day, $type, $student);

						if ($response['error'] != '') {
							$data['warning'] = $response['error'];
						} else {
							$data['success'] = 'You booking has been successful';
						}
					} else {
						$id_day = $this->input->post('group');
						$query = 'Select (Select Count(*) From special_group Where sg_day = ' . $id_day . ' And sg_status in (0,3)) < (Select bk_special_num From bk_days Where bk_id = ' . $id_day  . ') as question, 
											(Select Count(*) From special_group Where sg_day = ' . $id_day . ' And sg_status in (0,3) AND sg_student = ' . $this->session->userdata('user_id') . ') as yet';
						$response = $this->db->query($query);
						$response_row = $response->row();
						if ($response_row->question == 1 && $response_row->yet == 0) {
							$new_special = array(
								'sg_day' => $group_day,
								'sg_student' => $this->session->userdata('user_id'),
								'sg_status' => 0
							);
							$this->db->insert('special_group', $new_special);

							$data['success'] = 'You booking has been successful! Once your first lesson is completed, you can book your next lesson!<br/>Please be advised that if the conditions are not suitable for lessons, we will text you at least 1 hr before your lesson.<br/>In that case, you can log again to make another booking!';
						} else {
							if ($response_row->yet > 0) {
								$data['warning'] = 'You already are booked for this session';
							} else {
								$data['warning'] = 'All the spots are filled, try other date, sorry ';
							}
						}
					}
				} else {

					$data['warning'] = "You are unable to book the next lesson! Wait till your lesson is done, Then you will be able to book the next one!";
				}


				if ($data['success'] != "") {

					$this->db->where('st_id', '5');
					$mes_q = $this->db->get('sms_templates');
					$mes_r = $mes_q->row();

					$this->db->where('usr_id', $this->session->userdata('user_id'));
					$this->db->join('student_details', 'st_id = usr_id');
					$user_q = $this->db->get('bk_users');
					$user_info = $user_q->row();

					$id_bk = '';
					if ($user_info_b->st_special != 1) {
						$id_bk = $response['id'];
					} else {
						$id_bk = $group_day;
					}
					$this->db->where('bk_id', $id_bk);
					$this->db->join('bk_day_groups', 'bd_id = bk_group');
					$query_m = $this->db->get('bk_days');
					$query_m_row = $query_m->row();

					$message = $mes_r->st_template;

					$this->load->helper('misc');

					$message = str_ireplace('[name]', $user_info->usr_name . ' ' . $user_info->usr_surname, $message);
					$message = str_ireplace('[date]', $query_m_row->bd_date . ' at ' . $query_m_row->hour_from, $message);

					$this->db->where('usr_type', 2);
					$this->db->where('usr_email', 'school@kiterepublic.com.au'); // Be Careful!!!!
					$managers = $this->db->get('bk_users');

					foreach ($managers->result() as $manager) {
						$phone = $manager->usr_phone_main;
						$response = send_email('school@kiterepublic.com.au', 'Notification', $message, $manager->usr_email);
					}
				}
			}

			if ($this->input->post('cancel_check') == 1) {
				//$data['warning'] = 'Its On';
				$id_cancel = $this->input->post('id_cancel');
				$user_cancel = $this->session->userdata('user_id');

				$this->db->where('st_id', '4');
				$mes_q = $this->db->get('sms_templates');
				$mes_r = $mes_q->row();

				$this->db->where('usr_id', $user_cancel);
				$this->db->join('student_details', 'st_id = usr_id');
				$user_q = $this->db->get('bk_users');
				$user_info = $user_q->row();

				$message = $mes_r->st_template;

				$warning_hours = '';
				$hour_start 	= '09:00:00';
				$hour_end	= '17:00:00';

				if (time() > strtotime($hour_end) || time() < strtotime($hour_start)) {
					$warning_hours = 'The cancelation must be beetween 9:00 am to 5:00pm!';
				} else {

					$this->db->where('bk_id', $id_cancel);
					$this->db->select('hour_from,bd_date, TIME_TO_SEC( TIMEDIFF( hour_to, hour_from ) ) / ( 60 *60 ) *0.5 as penalty');
					$this->db->join('bk_day_groups', 'bd_id = bk_group');
					$pre_select = $this->db->get('bk_days');
					$row_pre = $pre_select->row();


					$date_class = $row_pre->bd_date . 'T' . $row_pre->hour_from;
					$date_class_obj = new DateTime($date_class);
					$date_today = new DateTime('NOW');
					$diff = $date_today->diff($date_class_obj);

					// Call the format method on the DateInterval-object
					$hours = $diff->h;
					$hours = $hours + ($diff->days * 24);

					$extra_w = '';

					if ($hours < 24) {
						$array_penalty = array('st_penalty' => round($row_pre->penalty, 2));
						$this->db->where('st_id', $user_cancel);
						$this->db->update('student_details', $array_penalty);
						$extra_w = '<h2>You cancelled your lesson in a short notice less than 24 hours, there is a penalty of half of the lesson.</h2> <br/>';
					}




					if ($user_info->st_special == 1) {
						$cancel_array = array(
							'sg_date_cancel' => date("Y-m-d H:i:s"),
							'sg_status' => '2'
						);
						$this->db->where('sg_day', $id_cancel);
						$this->db->where('sg_student', $user_cancel);
						$this->db->update('special_group', $cancel_array);
					} else {
						$cancel_array = array(
							'bk_canceldate' => date("Y-m-d H:i:s"),
							'bk_status' => '2'
						);
						$this->db->where('bk_id', $id_cancel);
						$this->db->update('bk_days', $cancel_array);
					}
				}



				if ($this->db->affected_rows()  > 0 && $warning_hours == '') {

					$this->load->helper('misc');
					$message = str_ireplace('[name]', $user_info->usr_name . ' ' . $user_info->usr_surname, $message);
					$message = str_ireplace('[date]', date("Y-m-d H:i:s"), $message);

					$this->db->where('usr_type', 2);
					$this->db->where('usr_email', 'school@kiterepublic.com.au');

					$managers = $this->db->get('bk_users');
					foreach ($managers->result() as $manager) {
						$phone = $manager->usr_phone_main;
						$response = send_email('school@kiterepublic.com.au', 'Notification', $message, $manager->usr_email);
					}

					$data['success'] = $extra_w . 'The cancellation is done, hope next time you can enjoy the wind :)';
				} else {
					$data['warning'] = "The cancellation wasn't succesful. Please contact kite republic. " . ($warning_hours != '' ? '<br/> ' . $warning_hours : '');
				}
			}

			if ($user_info_b->st_special == 1) {
				$this->db->select('sg_status, bk_status, bd_date,hour_from,hour_to, bk_id');
				$this->db->where('sg_student', $this->session->userdata('user_id'));
				$this->db->join('bk_days', 'sg_day = bk_id');
				$this->db->join('bk_day_groups', 'bk_group = bd_id');
				$this->db->order_by('bd_date', 'desc');
				$this->db->order_by('hour_from', 'asc');
				$res_q = $this->db->get('special_group');
			} else {
				$this->db->where('bk_student', $this->session->userdata('user_id'));
				$this->db->order_by('bd_date', 'desc');
				$this->db->order_by('hour_from', 'asc');
				$this->db->join('bk_day_groups', 'bd_id = bk_group');
				$res_q = $this->db->get('bk_days');
			}



			$this->db->where('st_id', $this->session->userdata('user_id'));
			$q_basic = $this->db->get('student_details');
			$basic = $q_basic->row();
			$data['basic'] = $basic;
			$actual_level = $basic->st_level;
			$hours_left = $this->cal_hours_left_total_student($this->session->userdata('user_id'));
			$data['history'] = $res_q;
			//$data['hours_do'] = $this->cal_hours_left($this->session->userdata('user_id'));
			$data['hours_left'] = $hours_left;
			$hours_opt = array();
			$hour_ini = 1;
			$hour_end = 3;
			$data['level_student'] = $actual_level;

			if (is_numeric($hours_left) && floor($hours_left) != $hours_left) {

				$hour_ini = 1.5;
			}
			if ($hours_left < 3) {
				$hour_end = $hours_left;
			}

			//$hours_opt[] = 0.5;

			while ($hour_ini <= $hour_end) {

				$hours_opt[] = $hour_ini;
				$hour_ini = $hour_ini + 1;
			}

			if ($user_info_b->st_special == 1) {
				if ($hours_left == 5) {
					$hours_opt = array(3);
				}
				if ($hours_left == 3) {
					$hours_opt = array(3);
				}
				if ($hours_left == 2) {
					$hours_opt = array(2);
				}
				if ($hours_left == 1) {
					$hours_opt = array(1);
				}
				if ($hours_left == 4) {
					$hours_opt = array(2);
				}
			}

			if ($hours_left == 5) {
				$hours_opt = array(3);
			}
			if ($hours_left == 2) {
				$hours_opt = array(2);
			}

			$data['hours_book'] = $hours_opt;
			$this->db->select('bd_date');
			$this->db->where('bd_date >', date("Y-m-d"));
			$this->db->limit(30);

			$this->db->group_by('bd_date');

			$data['days_book'] =  $this->db->get('bk_day_groups');



			if ($user_info_b->st_special == 1) {

				$this->db->select('bd_date');
				$this->db->where('bd_date >', date("Y-m-d"));
				$this->db->limit(30);
				$this->db->group_by('bd_date');
				$this->db->join('bk_days', 'bk_group = bd_id');
				$this->db->where('bk_level', 3);
				$this->db->where('(bd_inactive is null or bd_inactive <> 1)');
				$data['days_book'] =  $this->db->get('bk_day_groups');

				//echo $this->db->last_query();

			}

			$data['special'] = $user_info_b->st_special;
			$data['basic_var'] = $this->basic_var;
			$this->load->view('start', $data);
		} else {
			$data['basic_var'] = $this->basic_var;
			$this->load->view('index', $data);
		}
	}

	public function index()
	{
		if ($this->session->userdata('user_type')) {
			header("Location: " . base_url() . "main");
		} else {

			$data['basic_var'] = $this->basic_var;
			$this->load->view('index', $data);
		}
	}

	public function validate_user()
	{
		$this->load->library('PasswordHash');
		$type = $this->input->post('submit');
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$coupon = $this->input->post('coupon');
		$error = 1;

		$this->session->set_flashdata('warning', 'Your email address or password is incorrect. Please check the details entered and try again.');
		$this->db->where('usr_email', $username);
		//$this->db->where_in('usr_type',array('1','2','4'));
		$res = $this->db->get('bk_users');
		$res_q = $res->result();
		$row = $res->row();

		if (isset($row->usr_pass)) {
			if (password_verify($password, $row->usr_pass)) {
				$error = 0;
				$new_session = array(
					'user_type' => $row->usr_type,
					'username' => $username,
					'user_id' => $row->usr_id,
					'user_name' => $row->usr_name,
					'user_surname' => $row->usr_surname,
					'user_phone' => $row->usr_phone_main,
					'user_phone_sec' => $row->usr_phone_sec
				);
				$this->session->set_userdata($new_session);

				header("Location: " . base_url() . "main");
			}
		}
		if ($error == 1) {
			header("Location: " . base_url());
		}
	}

	public function main()
	{
		if ($this->session->userdata('user_id')) {
			if ($this->session->userdata('user_type') == 4) {
				header("Location: " . base_url() . "start");
			} elseif ($this->session->userdata('user_type') == 2) {
				header("Location: " . base_url() . "bookings");
			} else {

				$data['basic_var'] = $this->basic_var;
				$this->load->view('main', $data);
			}
		} else {
			header("Location: " . base_url());
		}
	}

	public function users()
	{
		if ($this->session->userdata('user_id')) {
			$user_access = 1;
			$data['user_level'] = $user_access;
			$data['warning'] = '';
			if ($this->session->userdata('user_type') != $user_access) {
				$data['warning'] = 'Access forbidden';
			}
			$this->load->library('pagination');
			$this->load->helper('misc');
			$this->config_pag['base_url'] = base_url() . 'users';

			$temp_res = users_selection(1, $this->config_pag['per_page'], $this->uri->segment(2), $this->input->get("search_user"), $phone = NULL, $level = NULL);
			$this->config_pag['total_rows'] = $temp_res['without']->num_rows();
			$this->pagination->initialize($this->config_pag);
			$data['user_list'] = $temp_res['with'];
			$data['basic_var'] = $this->basic_var;
			$data['links_pag'] = $this->pagination->create_links();
			$this->load->view('users', $data);
		} else {
			header("Location: " . base_url());
		}
	}

	public function students()
	{

		if ($this->session->userdata('user_id')) {
			$user_access = 4;
			$data['user_level'] = $user_access;
			$data['warning'] = '';

			if ($this->session->userdata('user_type') >= $user_access) {
				$data['warning'] = 'Access forbidden';
			}

			$this->load->library('pagination');
			$this->load->library('pagination');
			$this->load->helper('misc');
			$this->config_pag['base_url'] = base_url() . 'students';
			$this->config_pag['suffix'] = '?' . http_build_query($_GET, '', "&");
			$temp_res = users_selection(4, $this->config_pag['per_page'], $this->uri->segment(2), $this->input->get("search_user"), $phone = NULL, $level = NULL);
			$this->config_pag['total_rows'] = $temp_res['without']->num_rows();
			$this->pagination->initialize($this->config_pag);
			$data['links_pag'] = $this->pagination->create_links();

			$data['user_list'] = $temp_res['with'];
			$data['basic_var'] = $this->basic_var;



			$this->load->view('users', $data);
		} else {
			header("Location: " . base_url());
		}
	}

	public function user_school()
	{
		if ($this->session->userdata('user_id')) {
			$user_access = 2;
			$data['user_level'] = $user_access;
			$data['warning'] = '';
			if ($this->session->userdata('user_type') >= $user_access) {
				$data['warning'] = 'Access forbidden';
			}

			$this->load->library('pagination');
			$this->load->helper('misc');
			$this->config_pag['base_url'] = base_url() . 'user_school';

			$temp_res = users_selection(2, $this->config_pag['per_page'], $this->uri->segment(2), $this->input->get("search_user"), $phone = NULL, $level = NULL);
			$this->config_pag['total_rows'] = $temp_res['without']->num_rows();
			$this->pagination->initialize($this->config_pag);
			$data['links_pag'] = $this->pagination->create_links();
			$data['user_list'] = $temp_res['with'];
			$data['basic_var'] = $this->basic_var;
			$this->load->view('users', $data);
		} else {
			header("Location: " . base_url());
		}
	}

	public function user_edit()
	{
		if ($this->session->userdata('user_id')) {
			$id = $this->input->post("user_id_form");
			$type = $this->input->post("user_type_form");

			$mem_name = '';


			$data_mem = array(
				'mem_type' => $type,
				'mem_name' => $mem_name,
			);

			$data['mem_gen'] = $data_mem;
			$data['basic_var'] = $this->basic_var;
			$data['warning'] = '';
			$this->db->where('usr_id', $id);
			$temp_t = $this->db->get('bk_users');
			$res = $temp_t->result();

			if ($res) {
				$row = $res[0];
				$user_info = array(
					'id' => $row->usr_id,
					'name' => $row->usr_name,
					'surname' => $row->usr_surname,
					'email' => $row->usr_email,
					'f_phone' => $row->usr_phone_main,
					's_phone' => $row->usr_phone_sec,
					'username' => $row->usr_username
				);

				if ($type == 4) {
					$this->db->where('st_id', $id);
					$query = $this->db->get('student_details');
					$query_r = $query->row();
					if (isset($query_r)) {
						$user_info['observation'] = isset($query_r->st_observations) ? $query_r->st_observations : '';
						$user_info['level'] = isset($query_r->st_level) ? $query_r->st_level : '';
						$user_info['hours'] = isset($query_r->st_hours) ? $query_r->st_hours : '';
						$user_info['persons'] = isset($query_r->st_n_students) ? $query_r->st_n_students : '';
						$user_info['special'] = isset($query_r->st_special) ? $query_r->st_special : '';
						$user_info['pm'] = isset($query_r->st_payment_m) ? $query_r->st_payment_m : '';
						$user_info['price'] = isset($query_r->usr_price) ? $query_r->price : '';
						$user_info['usr_sport'] = isset($query_r->usr_sport) ? $query_r->usr_sport : '';
					}
				}
				$data['user_info'] = $user_info;
			} else {
				if ($id == 'New') {
					$user_info = array(
						'id' => 'New',
						'name' => '',
						'surname' => '',
						'email' => '',
						'f_phone' => '',
						's_phone' => '',
						'username' => '',
						'observation' => '',
						'level' => '',
						'hours' => '',
						'persons' => '',
						'special' => '',
						'pm' => '',
						'price' => '',
						'usr_sport' => ''
					);

					if ($this->input->post("from_bookings") == 1) {
						$user_info['bk_origin'] = 4;
					}

					$data['user_info'] = $user_info;
				} else {
					$data['warning'] = 'The user is not in the system.';
				}
			}

			$data['level_query'] = $this->db->get('student_level');
			$data['sport_types'] = $this->db->get('TypeLessons');


			$this->load->view('users_detail', $data);
		} else {
			header("Location: " . base_url());
		}
	}

	public function update_users($id)
	{

		if ($this->session->userdata('user_id')) {

			$data['basic_var'] = $this->basic_var;
			$type_user = $this->input->post('mem_type');
			$data['warning'] = '';

			$new_data = array(
				'usr_name' => $this->input->post('edit_name'),
				'usr_surname' => $this->input->post('edit_surname'),
				'usr_type' => $this->input->post('mem_type'),
				'usr_email' => $this->input->post('edit_email'),
				'usr_phone_main' => $this->input->post('phone_main'),
				'usr_phone_sec' => $this->input->post('sec_phone'),
				'usr_deactive' => 0


			);
			if ($type_user == '4') {
				$check_email = 1;
				$data_student['st_level'] = $this->input->post('usr_level');
				if ($id == 'New') {
					$data_student['st_hours'] = $this->input->post('usr_hours');
					$new_data['bk_origin'] = $this->input->post('bk_origin');
					if ($this->input->post('bk_origin') == 4) {
						$check_email = 0;
					}
				} else {
					if ($this->input->post('usr_hours') != "") {
						$data_student['st_hours'] = $this->input->post('usr_hours');
					}
				}
				$data_student['st_observations'] = $this->input->post('usr_obs');
				$data_student['st_special'] = $this->input->post('usr_special');
				$data_student['st_n_students'] =  $this->input->post('usr_people');
				$data_student['st_payment_m'] = $this->input->post('pay_method');

				//$data_student['st_price'] = $this->input->post('usr_price');

			}

			if ($this->input->post('password') != '') {
				$this->load->library('PasswordHash');
				$new_data['usr_pass'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
			} elseif ($id == 'New') {
				$this->load->library('PasswordHash');
				$a = '';
				for ($i = 0; $i < 6; $i++) {
					$a .= mt_rand(0, 9);
				}
				$this->load->helper('misc');
				$from = 'bookings@kiterepublic.com.au';
				$subject = 'New Password';
				$to = $this->input->post('edit_email');
				$message = '<h2>New Password is </h2><br/>' . $a;
				send_email($from, $subject, $message, $to);
				$new_data['usr_pass'] = password_hash($a, PASSWORD_BCRYPT);
			}




			if ($id == 'New') {

				$this->db->where('usr_email', $this->input->post('edit_email'));
				$query_v = $this->db->get('bk_users');
				if ($query_v->num_rows() > 0 && $check_email == 1) {
					$data['warning'] = 'The email is used by other user.';
				}
				$this->db->where('usr_name', $this->input->post('edit_name'));
				$this->db->where('usr_surname', $this->input->post('edit_surname'));
				$query_val = $this->db->get('bk_users');
				if ($query_val->num_rows() > 0 && $check_email == 0) {
					$data['warning'] = 'The name and surname is used by other user.';
					$data['bk_origin'] = 4;
				}

				if ($data['warning'] == '') {


					$this->db->insert('bk_users', $new_data);
					$new_id = $this->db->insert_id();
					$data_student['st_id'] = $new_id;
					$this->db->insert('student_details', $data_student);

					$array_log = array(
						'l_user' => $this->session->userdata('user_id'),
						'l_action' => 1,
						'l_reference' => '',
						'l_observations' => 'User Create User Code  :' . $new_id . ' Name :' . $this->input->post('edit_name') . ' ' . $this->input->post('edit_surname'),
						'l_student' => $new_id,
						'l_date' => date("Y-m-d H:i:s")
					);
					$this->db->insert("logs", $array_log);
				}
			} else {
				$this->db->where('usr_email', $this->input->post('edit_email'));
				$this->db->where('usr_id !=', $id);
				$this->db->where('usr_deactive = ', 0);
				$this->db->where('usr_type =', $type_user);
				$query_v = $this->db->get('bk_users');

				if ($query_v->num_rows() > 0) {
					echo $this->db->last_query();
					$data['warning'] = 'The email is used by other user.';
				} else {

					$this->db->where('usr_id', $id);
					$this->db->update('bk_users', $new_data);

					if ($type_user == '4') {
						$this->db->where('st_id', $id);
						$this->db->update('student_details', $data_student);
					}
				}
			}


			if ($data['warning'] != '') {
				$user_info = array(
					'id' => $id,
					'name' => $this->input->post('edit_name'),
					'surname' => $this->input->post('edit_surname'),
					'email' =>  $this->input->post('edit_email'),
					'f_phone' => $this->input->post('phone_main'),
					's_phone' => $this->input->post('sec_phone'),
					'username' => $this->input->post('edit_username'),



				);
				if ($type_user == '4') {
					$user_info['st_level'] = $this->input->post('usr_level');
					$user_info['st_hours'] = $this->input->post('usr_hours');
					$user_info['st_observations'] = $this->input->post('usr_obs');
					$user_info['st_special'] = $this->input->post('usr_special');
					$user_info['st_n_students'] =  $this->input->post('usr_people');
					$user_info['st_payment_m'] = $this->input->post('pay_method');
					$user_info['st_price'] = $this->input->post('usr_price');
				}



				$this->load->view('users_detail', $data);
			} else {
				if ($type_user == 1) {
					header("Location: " . base_url() . "users");
				}
				if ($type_user == 2) {
					header("Location: " . base_url() . "user_school");
				}
				if ($type_user == 3) {
					header("Location: " . base_url() . "instructors");
				}
				if ($type_user == 4) {
					header("Location: " . base_url() . "students");
				}
			}
		} else {
			header("Location: " . base_url());
		}
	}
	public function delete_user()
	{
		$privacy = 0;
		if ($this->session->userdata('user_id')) {
			if ($this->session->userdata('user_type') == 1 || $this->session->userdata('user_type') == 2) {
				$id_delete = $this->input->post('id_delete');
				$this->db->where('usr_id', $id_delete);
				$this->db->delete('bk_users');

				$type_user = $this->input->post('user_type_form');

				if ($type_user == 1) {
					header("Location: " . base_url() . "users");
				}
				if ($type_user == 2) {
					header("Location: " . base_url() . "user_school");
				}
				if ($type_user == 3) {
					header("Location: " . base_url() . "instructors");
				}
				if ($type_user == 4) {
					header("Location: " . base_url() . "students");
				}
				$privacy = 1;
			}
		}
		if ($privacy == 0) {
			header("Location: " . base_url());
		}
	}

	public function bookings_api()
	{

		$data['basic_var'] = $this->basic_var;
		$date = new DateTime();
		$date_show = $date->format('d/m/Y');
		if ($this->input->post('date')) {
			$date_show = $this->input->post('date');
		}
		$data["temp"] = $this->input->post('date');
		$data['day_sel'] = $date_show;

		$currentdate = DateTime::createFromFormat('d/m/Y', $date_show);
		$current_date = $currentdate->format('Y-m-d');

		$this->db->where('usr_type', '3');
		$this->db->order_by('usr_name', 'asc');
		$this->db->order_by('usr_surname', 'asc');

		$data['instructors'] = $this->db->get('bk_users')->result();

		$this->db->where('bd_date', $current_date);
		$this->db->where('(bd_inactive != 1 OR bd_inactive is NULL)');
		$this->db->join('bk_users', 'usr_id = bd_instructor', 'left');

		$data['day_schedule'] = $this->db->get('bk_day_groups')->result();
		$data['select_booking_types'] = $this->db->get('booking_type')->result();
		$data['select_student_level'] = $this->db->get('student_level')->result();

		header("Access-Control-Allow-Origin:http://localhost:3000");
		header("content-type: application/json");

		echo json_encode($data);
	}

	public function bookings()
	{
		if ($this->session->userdata('user_id') && $this->session->userdata('user_type') <= 2) {

			$data['basic_var'] = $this->basic_var;
			$date = new DateTime();
			$date_show = $date->format('d/m/Y');
			if ($this->input->post('date_sel')) {
				$date_show = $this->input->post('date_sel');
			}
			$data['day_sel'] = $date_show;



			$currentdate = DateTime::createFromFormat('d/m/Y', $date_show);
			$current_date = $currentdate->format('Y-m-d');
			$data['freeDates'] = $this->freeDate($currentdate->format('Y'),  $currentdate->format('m'));

			if ($this->input->post('add_ins') == 1) {
				$data_new = array(
					'bd_date' => $current_date,
				);
				$this->db->insert('bk_day_groups', $data_new);

				$array_log = array(
					'l_user' => $this->session->userdata('user_id'),
					'l_action' => 1,
					'l_reference' => '',
					'l_observations' => 'User Create Day :' . $current_date,
					'l_student' => NULL,
					'l_date' => date("Y-m-d H:i:s")
				);
				$this->db->insert("logs", $array_log);
			}

			if ($this->input->post('del_ins') == 1) {

				$row_d = $this->input->post('col_id');
				$this->db->where('bk_group', $row_d);
				$this->db->where('bk_status', '3');
				$query_rec = $this->db->get('bk_days');

				if ($query_rec->num_rows() == 0) {

					$new_status = array(
						'bk_status' => '1'
					);
					$this->db->where('bk_group', $row_d);
					$this->db->where('bk_status', '0');
					$this->db->update('bk_days', $new_status);

					$new_inactive['bd_inactive'] = 1;
					$this->db->where('bd_id', $row_d);
					$this->db->update('bk_day_groups', $new_inactive);
				}
			}

			$this->db->where('usr_type', '3');
			$this->db->order_by('usr_name', 'asc');
			$this->db->order_by('usr_surname', 'asc');

			$data['instructors'] = $this->db->get('bk_users');

			$this->db->where('bd_date', $current_date);
			$this->db->where('(bd_inactive != 1 OR bd_inactive is NULL)');
			$this->db->join('bk_users', 'usr_id = bd_instructor', 'left');

			$data['day_schedule'] = $this->db->get('bk_day_groups');

			$data['select_booking_types'] = $this->db->get('booking_type');
			$data['select_student_level'] = $this->db->get('student_level');

			$data['userdata'] = $this->session->all_userdata();
			$data['typeUser'] = $this->session->userdata('user_type');
			$this->load->view('booking_manager', $data);
		} else {
			header("Location: " . base_url());
		}
	}
	public function add_lessons()
	{
		if ($this->session->userdata('user_id')) {
			$date_ini = $this->input->post("startDate_td");
			$date_end = $this->input->post("endDate_td");
			$number_instructors =  $this->input->post("n_instructor");
			$startDate = DateTime::createFromFormat('d/m/Y', $date_ini);
			$endDate = DateTime::createFromFormat('d/m/Y', $date_end);
			$intervalDate = DateTime::createFromFormat('d/m/Y', $date_ini);
			$interval = $startDate->diff($endDate);
			$number = $interval->format('%a');

			for ($i = 0; $i <= $number; $i++) {
				$current_date = $intervalDate->format('Y-m-d');

				$this->db->where('bd_date', $current_date);
				$temp_result = $this->db->get('bk_day_groups');
				$number_reg = $temp_result->num_rows();

				if ($number_reg < $number_instructors) {
					for ($x = 0; $x < ($number_instructors - $number_reg); $x++) {
						$data_new = array(
							'bd_date' => $current_date,
						);
						$this->db->insert('bk_day_groups', $data_new);
					}
				}

				$intervalDate->add(new DateInterval('P1D'));
			}


			header("Location: " . base_url() . "bookings");
		} else {
			header("Location: " . base_url());
		}
	}


	public function json_event_wishlist_bk()
	{

		if ($this->session->userdata('user_id')) {

			$id_chain = $this->input->post('id_chain');

			$date_t = $this->input->post("date_a");

			$date_current = DateTime::createFromFormat('d/m/Y', $date_t);

			$date_correct_sql = $date_current->format('Y-m-d');

			$this->load->helper('misc');
			$main_events_id = explode(',', $id_chain);
			$events = array();


			$query_wishlist = '
				Select Max(bk_id) bk_id ,Max(bk_group) bk_group,bk_level,bk_special_num,bk_status,
				bk_student,Max(bk_cancel_manager) bk_cancel_manager, cancel_count bk_cancel_count, bd_date,bd_id,bd_inactive,
				bd_instructor,If("' . $date_correct_sql . '" = bd_date,1,0) as current,hour_from,hour_to
				From bk_days
				Inner Join
				(Select bk_student student ,sum(bk_cancel_count) cancel_count From bk_days where bk_status = 1 and bk_student != 0 group by bk_student) sel_table On student = bk_student
				Inner Join bk_day_groups On bd_id = bk_group

				Where bk_status = 1
				Group by bk_level,bk_special_num,bk_status,bk_student
				Union 
				Select bk_id,bk_group,bk_level,bk_special_num,bk_status,bk_student,bk_cancel_manager,
				bk_cancel_count,bd_date,bd_id,bd_inactive,bd_instructor,If("' . $date_correct_sql . '" = bd_date,1,0) as current,hour_from,hour_to
				From bk_days
				Inner Join bk_day_groups On bd_id = bk_group
				Where bk_status =1 and bk_level=3
				Order by bk_cancel_manager desc';


			$query = $this->db->query($query_wishlist);


			if ($query->num_rows() > 0) {

				foreach ($query->result() as $q_row) {
					$jsonEvent = array();
					$jsonEvent["id"] = $q_row->bk_id;
					$jsonEvent["start_time"] = $q_row->hour_from;
					$jsonEvent["end_time"] = $q_row->hour_to;
					$jsonEvent["time_description"] = formatDescriptiveTimes($q_row->hour_from, $q_row->hour_to);
					$jsonEvent["minutes"] = getTimeDuration("08:00:00", $q_row->hour_from);
					$jsonEvent["duration"] = getTimeDuration($q_row->hour_from, $q_row->hour_to);
					$jsonEvent["bk_level"] = $q_row->bk_level;
					$jsonEvent['student'] = '';
					$jsonEvent['obs'] = '';
					$jsonEvent['hl'] = '';
					$jsonEvent['special'] = array();
					$jsonEvent['count_cancel'] = $q_row->bk_cancel_count;
					$jsonEvent['current'] = $q_row->current;


					$this->db->where('bt_id', $q_row->bk_level);
					$q_l = $this->db->get('booking_type');

					$query_l = $q_l->row();
					$jsonEvent["level"] = $query_l->bt_description;

					if ($q_row->bk_level == 1) {
						$this->db->where('usr_id', $q_row->bk_student);
						$this->db->join('student_details', 'st_id = usr_id');
						$this->db->join('student_level', 'sl_id = st_level', 'left');
						$q_r = $this->db->get('bk_users');
						$query_r = $q_r->row();

						$jsonEvent['student'] = @$q_row->bk_student;
						$jsonEvent['student_name'] = @$query_r->usr_name . ' ' . @$query_r->usr_surname;
						if (isset($query_r->st_n_students) && $query_r->st_n_students > 1) {
							$jsonEvent['student_name'] .= ' (+' . (@$query_r->st_n_students - 1) . ' )';
						}
						$jsonEvent['student_mobile'] =  @$query_r->usr_phone_main;
						$jsonEvent['obs'] = @$query_r->st_observations;
						$jsonEvent['hl'] = @$this->cal_hours_left_total($q_row->bk_student);
						$jsonEvent["level"] = @$query_r->sl_description;
					} else {
						if (isset($q_row->bk_obs)) {
							$jsonEvent['obs'] = $q_row->bk_obs;
						} else {
							$jsonEvent['obs'] = '';
						}
					}

					if ($q_row->bk_level == 3) {
						$where_s = 'sg_day = ' . $q_row->bk_id . ' AND sg_status in(0,3)';
						$this->db->where($where_s, null, false);
						$this->db->join('bk_users', 'usr_id = sg_student');
						$this->db->join('student_details', 'usr_id = st_id');
						$this->db->join('student_level', 'st_level = sl_id');
						$group_q = $this->db->get('special_group');

						foreach ($group_q->result() as $res) {

							$jsonEvent["level"] = $res->sl_description . ' (Sp)';

							$jsonEvent['special'][] = array(
								'name' => $res->usr_name . ' ' . $res->usr_surname,
								'phone' => $res->usr_phone_main,
								'usr_id' => $res->usr_id,
								'number' => $res->st_n_students,
							);
						}
					}

					if ($q_row->bk_level == 2) {
						$this->db->where('es_id', $q_row->bk_lesson_level);
						$query_desc = $this->db->get('extra_status');
						if ($query_desc->num_rows() > 0) {
							$row = $query_desc->row();
							$jsonEvent["level"] = $row->es_description;
						}
					}


					$jsonEvent["group"] = $q_row->bk_group;

					$events[] = $jsonEvent;
				}
			}
			$json["events"] = $events;
			$json["rc"] = 0;
			header("content-type: application/json");
			print json_encode($json);
		} else {
			header("content-type: application/json");
			$json["rc"] = 99;
			print json_encode($json);
		}
	}


	public function json_event_wishlist()
	{
		if ($this->session->userdata('user_id')) {
			$id_chain = $this->input->post('id_chain');
			$date_t = $this->input->post("date_a");

			$date_current = DateTime::createFromFormat('d/m/Y', $date_t);

			$date_correct_sql = $date_current->format('Y-m-d');

			$this->load->helper('misc');
			$main_events_id = explode(',', $id_chain);
			$events = array();


			$query_wishlist = 'Select Max(bk_id) bk_id ,Max(bk_group) bk_group,bk_level,bk_special_num,bk_status,
				bk_student,Max(bk_cancel_manager) bk_cancel_manager, cancel_count bk_cancel_count, 
				bd_date,bd_id,bd_inactive, bd_instructor,If("' . $date_correct_sql . '" = bd_date,1,0) as current,
				hour_from,hour_to, usr_name,usr_surname,st_n_students,usr_phone_main,sl_description,bk_obs,bt_description,st_observations
				From bk_days
				Inner Join
				(Select bk_student student ,sum(bk_cancel_count) cancel_count From bk_days where bk_status = 1 and bk_student != 0 group by bk_student) sel_table On student = bk_student
				
				Inner Join bk_day_groups On bd_id = bk_group
				Inner Join bk_users On usr_id = student
				Inner Join student_details On st_id = student
				Left Join student_level On sl_id = st_level
				Left Join booking_type On bt_id = bk_level
				Where bk_status = 1
				Group by bk_level,bk_special_num,bk_status,bk_student
				Union 
				Select bk_id,bk_group,bk_level,bk_special_num,bk_status,bk_student,bk_cancel_manager, 
				bk_cancel_count,bd_date,bd_id,bd_inactive,bd_instructor,If("' . $date_correct_sql . '" = bd_date,1,0) as current,
				hour_from,hour_to,NULL as usr_name,NULL as usr_surname,NULL as st_n_students,NULL as usr_phone_main,NULL as sl_description, bk_obs,NULL,NULL
				
				From bk_days
				Inner Join bk_day_groups On bd_id = bk_group
				Where bk_status =1 and bk_level = 3 and exists (Select * From special_group where sg_day = bk_id)
				Order by bk_cancel_manager desc';

			$query = $this->db->query($query_wishlist);


			if ($query->num_rows() > 0) {

				foreach ($query->result() as $q_row) {
					$jsonEvent = array();
					$jsonEvent["id"] = $q_row->bk_id;
					$jsonEvent["start_time"] = $q_row->hour_from;
					$jsonEvent["end_time"] = $q_row->hour_to;
					$jsonEvent["time_description"] = formatDescriptiveTimes($q_row->hour_from, $q_row->hour_to);
					$jsonEvent["minutes"] = getTimeDuration("08:00:00", $q_row->hour_from);
					$jsonEvent["duration"] = getTimeDuration($q_row->hour_from, $q_row->hour_to);
					$jsonEvent["bk_level"] = $q_row->bk_level;
					$jsonEvent['student'] = '';
					$jsonEvent['obs'] = '';
					$jsonEvent['hl'] = '';
					$jsonEvent['special'] = array();
					$jsonEvent['count_cancel'] = $q_row->bk_cancel_count;
					$jsonEvent['current'] = $q_row->current;
					/*$this->db->where('bt_id',$q_row->bk_level);
						$q_l = $this->db->get('booking_type');
		
						$query_l = $q_l->row();*/
					$jsonEvent["level"] = $q_row->bt_description;

					if ($q_row->bk_level == 1) {

						/*
							
							$this->db->where('usr_id',$q_row->bk_student);
							$this->db->join('student_details','st_id = usr_id');
							$this->db->join('student_level','sl_id = st_level','left');
							$q_r = $this->db->get('bk_users');
							$query_r = $q_r->row();*/

						$jsonEvent['student'] = @$q_row->bk_student;
						$jsonEvent['student_name'] = @$q_row->usr_name . ' ' . @$q_row->usr_surname;
						if (isset($q_row->st_n_students) && $q_row->st_n_students > 1) {
							$jsonEvent['student_name'] .= ' (+' . (@$q_row->st_n_students - 1) . ' )';
						}
						$jsonEvent['student_mobile'] =  @$q_row->usr_phone_main;
						$jsonEvent['obs'] = @$q_row->st_observations;
						$jsonEvent['hl'] = @$this->cal_hours_left_total($q_row->bk_student);
						$jsonEvent["level"] = @$q_row->sl_description;
					} else {
						if (isset($q_row->bk_obs)) {
							$jsonEvent['obs'] = $q_row->bk_obs;
						} else {
							$jsonEvent['obs'] = '';
						}
					}

					if ($q_row->bk_level == 3) {
						$where_s = 'sg_day = ' . $q_row->bk_id . ' AND sg_status in(0,3)';
						$this->db->where($where_s, null, false);
						$this->db->join('bk_users', 'usr_id = sg_student');
						$this->db->join('student_details', 'usr_id = st_id');
						$this->db->join('student_level', 'st_level = sl_id');
						$this->db->select('usr_name,usr_surname,usr_phone_main,usr_id,st_n_students,sl_description');
						$group_q = $this->db->get('special_group');

						foreach ($group_q->result() as $res) {

							$jsonEvent["level"] = $res->sl_description . ' (Sp)';

							$jsonEvent['special'][] = array(
								'name' => $res->usr_name . ' ' . $res->usr_surname,
								'phone' => $res->usr_phone_main,
								'usr_id' => $res->usr_id,
								'number' => $res->st_n_students,
							);
						}
					}

					/*
					if($q_row->bk_level == 2){
						$this->db->where('es_id', $q_row->bk_lesson_level);
						$query_desc = $this->db->get('extra_status');
						if($query_desc->num_rows() > 0){
							$row = $query_desc->row(); 
							$jsonEvent["level"] = $row->es_description;
						}
						
					}
					*/

					$jsonEvent["group"] = $q_row->bk_group;

					$events[] = $jsonEvent;
				}
			}


			$json["events"] = $events;
			$json["rc"] = 0;
			header("content-type: application/json");
			print json_encode($json);
		} else {
			header("content-type: application/json");
			$json["rc"] = 99;
			print json_encode($json);
		}
	}

	public function json_event_delay()
	{
		if ($this->session->userdata('user_id')) {
			$id_chain = $this->input->post('id_chain');

			if ($id_chain == '') {
				$id_chain = $this->input->get('id_chain');
			}

			$this->load->helper('misc');
			//$main_events_id = explode(',',$id_chain);
			$events = array();
			//var_dump($main_events_id);



			$where_c = ' bk_group IN (' . $id_chain . ') AND bk_status IN (0,3) '; //Only the Ones without Cancel or Deleted
			$this->db->where($where_c, null, false);
			$this->db->join('booking_type', 'bt_id = bk_level', 'left');
			$this->db->join('bk_users', 'usr_id = bk_student', 'left');
			$this->db->join('student_details', 'st_id = usr_id', 'left');
			$this->db->join('student_level', 'sl_id = st_level', 'left');
			$query = $this->db->get('bk_days');


			if ($query->num_rows() > 0) {

				foreach ($query->result() as $q_row) {
					$jsonEvent = array();
					$jsonEvent["id"] = $q_row->bk_id;
					$jsonEvent["start_time"] = $q_row->hour_from;
					$jsonEvent["end_time"] = $q_row->hour_to;
					$jsonEvent["time_description"] = formatDescriptiveTimes($q_row->hour_from, $q_row->hour_to);
					$jsonEvent["minutes"] = getTimeDuration("08:00:00", $q_row->hour_from);
					$jsonEvent["duration"] = getTimeDuration($q_row->hour_from, $q_row->hour_to);
					$jsonEvent["bk_level"] = $q_row->bk_level;
					$jsonEvent["status"] = $q_row->bk_status;
					$jsonEvent['student'] = '';
					$jsonEvent['obs'] = '';
					$jsonEvent['hl'] = '';
					$jsonEvent['count_cancel'] = $q_row->bk_cancel_count;
					$jsonEvent['special'] = array();
					$jsonEvent["level"] = $q_row->bt_description == '' ? '1' : $q_row->bt_description;

					if ($q_row->bk_level == 1) {


						$jsonEvent['student'] = @$q_row->bk_student;
						$jsonEvent['student_name'] = @$q_row->usr_name . ' ' . @$q_row->usr_surname;
						if (@$q_row->st_n_students > 1) {
							$jsonEvent['student_name'] .= ' (+' . (@$q_row->st_n_students - 1) . ' )';
						}


						$jsonEvent['student_mobile'] =  @$q_row->usr_phone_main;
						$jsonEvent['obs'] = @$q_row->st_observations;
						$jsonEvent['hl'] = @$this->cal_hours_left_total(@$q_row->bk_student);
						$jsonEvent["level"] = @$q_row->sl_description;

						if ($q_row->bk_status == 3 && $q_row->bk_lesson_level != '') {

							$jsonEvent["level"] = $q_row->sl_description;
						}
					}


					if ($q_row->bk_level == 2) {
						$this->db->where('es_id', $q_row->bk_lesson_level);
						$query_desc = $this->db->get('extra_status');
						if ($query_desc->num_rows() > 0) {
							$row = $query_desc->row();
							$jsonEvent["level"] = $row->es_description;
						}
						$jsonEvent['obs'] = $q_row->bk_obs;
					}

					if ($q_row->bk_level == 3) {

						$jsonEvent['obs'] = $q_row->bk_obs;

						$where_s = 'sg_day = ' . $q_row->bk_id . ' AND sg_status in(0,3)';
						$this->db->where($where_s, null, false);
						$this->db->join('bk_users', 'usr_id = sg_student');
						$this->db->join('student_details', 'usr_id = st_id');
						$this->db->join('student_level', 'st_level = sl_id', 'left'); // Some Student with level issues
						$group_q = $this->db->get('special_group');
						$level_desc = '';


						if ($q_row->bk_lesson_level != '') {
							$this->db->where('sl_id', $q_row->bk_lesson_level);
							$level_d = $this->db->get('student_level');
							$level_r = $level_d->row();
							$level_desc =  $level_r->sl_description;
						}

						foreach ($group_q->result() as $res) {
							$jsonEvent["level"] = $res->sl_description . ' (Sp)';

							$jsonEvent['special'][] = array(
								'name' => $res->usr_name . ' ' . $res->usr_surname,
								'phone' => $res->usr_phone_main,
								'usr_id' => $res->usr_id,
								'number' => $res->st_n_students,

							);
						}

						if ($q_row->bk_lesson_level != '') {
							$jsonEvent["level"] = $level_desc  . ' (Sp)';
						}
					}

					$jsonEvent["group"] = $q_row->bk_group;
					$events[] = $jsonEvent;
				}
			}


			$json["events"] = $events;
			$json["rc"] = 0;
			header("content-type: application/json");
			print json_encode($json);
		} else {
			header("content-type: application/json");
			$json["rc"] = 99;
			print json_encode($json);
		}
	}


	public function json_event()
	{
		if ($this->session->userdata('user_id')) {
			$id_chain = $this->input->post('id_chain');

			if ($id_chain == '') {
				$id_chain = $this->input->get('id_chain');
			}

			$this->load->helper('misc');
			//$main_events_id = explode(',',$id_chain);
			$events = array();
			//var_dump($main_events_id);



			$where_c = ' bk_group IN (' . $id_chain . ') AND bk_status IN (0,3) '; //Only the Ones without Cancel or Deleted
			$this->db->where($where_c, null, false);
			$this->db->join('booking_type', 'bt_id = bk_level', 'left');
			$this->db->join('bk_users', 'usr_id = bk_student', 'left');
			$this->db->join('student_details', 'st_id = usr_id', 'left');
			$this->db->join('student_level', 'sl_id = IFNULL(bk_lesson_level,st_level)', 'left');
			$query = $this->db->get('bk_days');

			// $json['temp'] = $this->db->last_query();
			if ($query->num_rows() > 0) {

				foreach ($query->result() as $q_row) {
					$jsonEvent = array();
					$jsonEvent["id"] = $q_row->bk_id;
					$jsonEvent["start_time"] = $q_row->hour_from;
					$jsonEvent["end_time"] = $q_row->hour_to;
					$jsonEvent["time_description"] = formatDescriptiveTimes($q_row->hour_from, $q_row->hour_to);
					$jsonEvent["minutes"] = getTimeDuration("08:00:00", $q_row->hour_from);
					$jsonEvent["duration"] = getTimeDuration($q_row->hour_from, $q_row->hour_to);
					$jsonEvent["bk_level"] = $q_row->bk_level;
					$jsonEvent["status"] = $q_row->bk_status;
					$jsonEvent['student'] = '';
					$jsonEvent['obs'] = '';
					$jsonEvent['hl'] = '';
					$jsonEvent['count_cancel'] = $q_row->bk_cancel_count;
					$jsonEvent['special'] = array();
					$jsonEvent["level"] = $q_row->bt_description == '' ? '1' : $q_row->bt_description;

					if ($q_row->bk_level == 1) {


						$jsonEvent['student'] = @$q_row->bk_student;
						$jsonEvent['student_name'] = @$q_row->usr_name . ' ' . @$q_row->usr_surname;
						if (@$q_row->st_n_students > 1) {
							$jsonEvent['student_name'] .= ' (+' . (@$q_row->st_n_students - 1) . ' )';
						}


						$jsonEvent['student_mobile'] =  @$q_row->usr_phone_main;
						$jsonEvent['obs'] = @$q_row->st_observations;
						$jsonEvent['hl'] = $this->cal_hours_left_total(@$q_row->bk_student);
						$jsonEvent["level"] = @$q_row->sl_description;

						if ($q_row->bk_status == 3 && $q_row->bk_lesson_level != '') {

							$jsonEvent["level"] = $q_row->sl_description;
						}
					}


					if ($q_row->bk_level == 2) {
						$this->db->where('es_id', $q_row->bk_lesson_level);
						$query_desc = $this->db->get('extra_status');
						if ($query_desc->num_rows() > 0) {
							$row = $query_desc->row();
							$jsonEvent["level"] = $row->es_description;
						}
						$jsonEvent['obs'] = $q_row->bk_obs;
					}

					if ($q_row->bk_level == 3) {

						$jsonEvent['obs'] = $q_row->bk_obs;

						$where_s = 'sg_day = ' . $q_row->bk_id . ' AND sg_status in(0,3)';
						$this->db->where($where_s, null, false);
						$this->db->join('bk_users', 'usr_id = sg_student');
						$this->db->join('student_details', 'usr_id = st_id');
						$this->db->join('student_level', 'st_level = sl_id', 'left'); // Some Student with level issues
						$group_q = $this->db->get('special_group');
						$level_desc = '';


						if ($q_row->bk_lesson_level != '') {
							$this->db->where('sl_id', $q_row->bk_lesson_level);
							$level_d = $this->db->get('student_level');
							$level_r = $level_d->row();
							$level_desc =  $level_r->sl_description;
						}

						foreach ($group_q->result() as $res) {
							$jsonEvent["level"] = $res->sl_description . ' (Sp)';

							$jsonEvent['special'][] = array(
								'name' => $res->usr_name . ' ' . $res->usr_surname,
								'phone' => $res->usr_phone_main,
								'usr_id' => $res->usr_id,
								'number' => $res->st_n_students,

							);
						}

						if ($q_row->bk_lesson_level != '') {
							$jsonEvent["level"] = $level_desc  . ' (Sp)';
						}
					}

					$jsonEvent["group"] = $q_row->bk_group;
					$events[] = $jsonEvent;
				}
			}


			$json["events"] = $events;
			$json["rc"] = 0;
			header("content-type: application/json");
			print json_encode($json);
		} else {
			header("content-type: application/json");
			$json["rc"] = 99;
			print json_encode($json);
		}
	}

	public function json_event_temp_two()
	{
		if ($this->session->userdata('user_id')) {



			$id_chain = $this->input->post('id_chain');
			if ($id_chain == '') {
				$id_chain = $this->input->get('id_chain');
			}

			$this->load->helper('misc');
			$main_events_id = explode(',', $id_chain);
			$events = array();
			//var_dump($main_events_id);

			foreach ($main_events_id as $item) {
				$where_c = ' bk_group = ' . $item . ' AND bk_status IN (0,3) ';

				$this->db->where($where_c, null, false);
				$query = $this->db->get('bk_days');


				if ($query->num_rows() > 0) {

					foreach ($query->result() as $q_row) {
						$jsonEvent = array();
						$jsonEvent["id"] = $q_row->bk_id;
						$jsonEvent["start_time"] = $q_row->hour_from;
						$jsonEvent["end_time"] = $q_row->hour_to;
						$jsonEvent["time_description"] = formatDescriptiveTimes($q_row->hour_from, $q_row->hour_to);
						$jsonEvent["minutes"] = getTimeDuration("08:00:00", $q_row->hour_from);
						$jsonEvent["duration"] = getTimeDuration($q_row->hour_from, $q_row->hour_to);
						$jsonEvent["bk_level"] = $q_row->bk_level;
						$jsonEvent["status"] = $q_row->bk_status;
						$jsonEvent['student'] = '';
						$jsonEvent['obs'] = '';
						$jsonEvent['hl'] = '';
						$jsonEvent['count_cancel'] = $q_row->bk_cancel_count;
						$jsonEvent['special'] = array();

						$this->db->where('bt_id', $q_row->bk_level);
						$q_l = $this->db->get('booking_type');
						$query_l = $q_l->row();

						if (isset($query_l->bt_description)) {
							$jsonEvent["level"] = $query_l->bt_description;
						} else {
							$jsonEvent["level"] = '1';
						}
						if ($q_row->bk_level == 1) {


							$this->db->where('usr_id', $q_row->bk_student);
							$this->db->join('student_details', 'st_id = usr_id');
							$this->db->join('student_level', 'sl_id = st_level', 'left');

							$q_r = $this->db->get('bk_users');
							$query_r = $q_r->row();
							$jsonEvent['student'] = @$q_row->bk_student;
							$jsonEvent['student_name'] = @$query_r->usr_name . ' ' . @$query_r->usr_surname;
							if (@$query_r->st_n_students > 1) {
								$jsonEvent['student_name'] .= ' (+' . (@$query_r->st_n_students - 1) . ' )';
							}


							$jsonEvent['student_mobile'] =  @$query_r->usr_phone_main;
							$jsonEvent['obs'] = @$query_r->st_observations;
							$jsonEvent['hl'] = @$this->cal_hours_left_total(@$q_row->bk_student);
							$jsonEvent["level"] = @$query_r->sl_description;

							if ($q_row->bk_status == 3 && $q_row->bk_lesson_level != '') {
								$this->db->where('sl_id', $q_row->bk_lesson_level);
								$res = $this->db->get('student_level');
								$res_row = $res->row();
								$jsonEvent["level"] = $res_row->sl_description;
							}
						}
						if ($q_row->bk_level == 2) {
							$this->db->where('es_id', $q_row->bk_lesson_level);
							$query_desc = $this->db->get('extra_status');
							if ($query_desc->num_rows() > 0) {
								$row = $query_desc->row();
								$jsonEvent["level"] = $row->es_description;
							}
							$jsonEvent['obs'] = $q_row->bk_obs;
						}





						if ($q_row->bk_level == 3) {

							$jsonEvent['obs'] = $q_row->bk_obs;

							$where_s = 'sg_day = ' . $q_row->bk_id . ' AND sg_status in(0,3)';
							$this->db->where($where_s, null, false);
							$this->db->join('bk_users', 'usr_id = sg_student');
							$this->db->join('student_details', 'usr_id = st_id');
							$this->db->join('student_level', 'st_level = sl_id', 'left'); // Some Student with level issues
							$group_q = $this->db->get('special_group');
							$level_desc = '';


							if ($q_row->bk_lesson_level != '') {
								$this->db->where('sl_id', $q_row->bk_lesson_level);
								$level_d = $this->db->get('student_level');
								$level_r = $level_d->row();
								$level_desc =  $level_r->sl_description;
							}

							foreach ($group_q->result() as $res) {
								$jsonEvent["level"] = $res->sl_description . ' (Sp)';

								$jsonEvent['special'][] = array(
									'name' => $res->usr_name . ' ' . $res->usr_surname,
									'phone' => $res->usr_phone_main,
									'usr_id' => $res->usr_id,
									'number' => $res->st_n_students,

								);
							}

							if ($q_row->bk_lesson_level != '') {
								$jsonEvent["level"] = $level_desc  . ' (Sp)';
							}
						}



						$jsonEvent["group"] = $q_row->bk_group;

						$events[] = $jsonEvent;
					}
				}
			}
			$json["events"] = $events;
			$json["rc"] = 0;
			header("content-type: application/json");
			print json_encode($json);
		} else {
			header("content-type: application/json");
			$json["rc"] = 99;
			print json_encode($json);
		}
	}

	private function save_lesson_ci($start_time, $end_time, $group_day, $type, $student)
	{



		$new_data = array(
			'hour_from' => $start_time,
			'hour_to' => $end_time,
			'bk_group' => $group_day,
			'bk_level' => $type,
			'bk_student' => $student

		);



		// Validate Only Hours for Booking
		$where_val = " NOT ((hour_from >= Cast('" . $end_time . "' as time) AND hour_to > Cast('" . $end_time . "' as time)) 
				OR 
		(hour_from < Cast('" . $start_time . "' as time) AND hour_to <= Cast('" . $start_time . "' as time))) AND bk_group = " . $group_day;
		$where_val .= ' AND bk_status In (0,3)   and (bd_inactive != 1 or bd_inactive is null)';

		$this->db->join('bk_day_groups', 'bd_id = bk_group');
		$this->db->where($where_val, NULL, false);
		$query_val = $this->db->get('bk_days');



		if ($query_val->num_rows() > 0) {

			$json["error"] = 'Not Available, try again';
		} else {


			$new_data['bk_status'] = 0;
			$this->db->insert('bk_days', $new_data);
			$json["id"] = $this->db->insert_id();

			if ($this->db->affected_rows()  > 0) {
				$json["error"] = '';
				$this->db->where('bt_id', $type);
				$q_l = $this->db->get('booking_type');
				$query_l = $q_l->row();
				$json["level_type"] = $query_l->bt_description;
			} else {
				$json["error"] = 'There where no changes';
			}
		}
		return $json;
	}

	private function check_special_level($id_lesson)
	{
		$query = 'SELECT sl_id,sl_description FROM `bk_days` Left Join special_group On sg_day = bk_id Left Join student_details On sg_student = st_id Left Join student_level On ifNull(bk_lesson_level,st_level) = sl_id where bk_id = ' . $id_lesson . ' Group By sl_id,sl_description Order By bk_id Desc';
		$resp = $this->db->query($query);

		$out = array(
			'code' => '',
			'desc' => '(Sp)'
		);



		foreach ($resp->result() as $val) {
			$out['code'] = $val->sl_id;
			$out['desc'] = $val->sl_description;
		}

		$out['desc'] .= '(Sp)';

		if ($resp->num_rows() > 1) {
			$out['desc'] .= '*';
		}


		return $out;
	}

	public function json_save_lesson()
	{
		if ($this->session->userdata('user_id')) {
			$type = $this->input->post('type');
			$id = $this->input->post('id');
			$minutes = $this->input->post('minutes');
			$duration =  $this->input->post('duration');
			$group = $this->input->post('group');

			$student = $this->input->post('student') == 0 ? null : $this->input->post('student');

			$this->load->helper('misc');


			$count_errors = 0;

			if ($id != '') {
				$this->db->where('bk_id', $id);
				$this->db->where('bk_status', '3');
				$query_ck = $this->db->get('bk_days');
				$count_errors = $query_ck->num_rows();
			}



			$startTime = getFutureTime("08:00:00", $minutes);
			$endTime = getFutureTime("08:00:00", ($minutes + $duration));

			$json["level"] = $type;
			$json["type"] = $type;


			$new_data = array(
				'hour_from' => $startTime,
				'hour_to' => $endTime,
				'bk_group' => $group,
				'bk_level' => $type,
				'bk_student' => $student

			);
			// Validate Only Hours for Booking
			$where_val = " NOT ((hour_from >= Cast('" . $endTime . "' as time) AND hour_to > Cast('" . $endTime . "' as time)) 
					OR 
					(hour_from < Cast('" . $startTime . "' as time) AND hour_to <= Cast('" . $startTime . "' as time))) AND bk_group = " . $group;
			$where_val .= ' AND bk_status In (0,3)  and (bd_inactive != 1 or bd_inactive is null) ';
			if ($id != '') {
				$where_val .= " AND bk_id != " . $id;
			}


			$this->db->where($where_val, NULL, false);
			$this->db->join('bk_day_groups', 'bd_id = bk_group');
			$query_val = $this->db->get('bk_days');


			$number_same = 0;

			if ($student != '' && $type == 1) {

				$this->db->where('bd_id', $group);
				$date_q = $this->db->get('bk_day_groups');
				$date_r = $date_q->row();

				$temp_date = $date_r->bd_date;


				$where_val = " NOT ((hour_from >= Cast('" . $endTime . "' as time) AND hour_to > Cast('" . $endTime . "' as time)) 
						OR 
						(hour_from < Cast('" . $startTime . "' as time) AND hour_to <= Cast('" . $startTime . "' as time))) AND bd_date = '" . $temp_date . "'";
				$where_val .= ' AND bk_status In (0,3)   and (bd_inactive != 1 or bd_inactive is null) and bk_student = ' . $student;
				if ($id != '') {
					$where_val .= " AND bk_id != " . $id;
				}

				$this->db->where($where_val, NULL, false);
				$this->db->join('bk_day_groups', 'bd_id = bk_group');
				$query_val_same = $this->db->get('bk_days');
				$number_same = $query_val_same->num_rows();
			}






			if ($query_val->num_rows() + $count_errors + $number_same  > 0) {
				$json["rc"] = 'Hour overlaping';
				$json["temp_test"] = $this->db->last_query();
			} else {



				if ($id == '') {
					$new_data['bk_status'] = 0;
					if ($type == 3) {
						$new_data['bk_special_num'] = 3;
						$new_data['bk_lesson_level'] =  $student;
					}
					if ($type == 2) {
						$new_data['bk_lesson_level'] = $student;
						$new_data['bk_student']  = NULL;
					}


					$this->db->insert('bk_days', $new_data);
					$json["id"] = $this->db->insert_id();
				} else {

					$new_data['bk_status'] = 0;
					$this->db->where('bk_id', $id);
					$this->db->update('bk_days', $new_data);
				}


				if ($this->db->affected_rows()  > 0) {
					$json["rc"] = 0;
					$json["time_description"] = formatDescriptiveTimes($startTime, $endTime);

					$this->db->where('bt_id', $type);
					$q_l = $this->db->get('booking_type');
					$query_l = $q_l->row();
					if (isset($query_l->bt_description)) {
						$json["level_type"] = $query_l->bt_description;
					} else {
						$json["level_type"] = 'L1';
					}

					if ($type == 1) {



						$this->db->where('usr_id', $student);
						$this->db->join('student_details', 'st_id = usr_id');
						$this->db->join('student_level', 'st_level = sl_id', 'left');
						$q_r = $this->db->get('bk_users');




						$query_r = $q_r->row();
						$json['student_name'] = $query_r->usr_name . ' ' . $query_r->usr_surname;
						$json['student_mobile'] = $query_r->usr_phone_main;
						$json['hl'] = $this->cal_hours_left_total($student);


						$json["level_type"] = $query_r->sl_description;

						$this->db->where('bk_student', $student);
						$this->db->where('bk_status', '1');
						$this->db->delete('bk_days');
					}

					if ($type == 2) {
						$this->db->where('es_id',  $student);
						$query_desc = $this->db->get('extra_status');
						if ($query_desc->num_rows() > 0) {
							$row = $query_desc->row();
							$json["level_type"] = $row->es_description;
						}
					}
					if ($type == 3) {
						$id_new = $id != '' ? $id : $json["id"];
						$new_level_sp = $this->check_special_level($id_new);
						$json["level_type"] = $new_level_sp['desc'];
					}
				} else {
					$json["rc"] = 'There where no changes';
				}
			}

			$array_log = array(
				'l_user' => $this->session->userdata('user_id'),
				'l_action' => 4,
				'l_reference' => '',
				'l_observations' => 'User Create booking for  :' . $student,
				'l_student' => $student,
				'l_date' => date("Y-m-d H:i:s")
			);
			$this->db->insert("logs", $array_log);


			header("content-type: application/json");
			print json_encode($json);
		} else {
			header("content-type: application/json");
			$json["rc"] = 99;
			print json_encode($json);
		}
	}

	public function log_out()
	{
		if ($this->session->userdata('user_id')) {
			$this->session->unset_userdata('user_type');
			$this->session->unset_userdata('username');
			$this->session->unset_userdata('user_id');
			$this->session->unset_userdata('user_name');
			$this->session->unset_userdata('user_surname');
			$this->session->unset_userdata('user_phone');
			$this->session->unset_userdata('user_phone_sec');
			$this->session->sess_destroy();
		}
		header("Location: " . base_url());
	}

	public function ajax_booking()
	{
		if ($this->session->userdata('user_id')) {

			$this->load->helper('misc');
			$id_student = $this->input->post('id_student');
			$id_day = $this->input->post('id_day');
			$start_hour = $this->input->post('start_hour');
			$end_hour = getFutureTime($start_hour, $this->input->post('end_hour') * 60);

			$data['test'] = array();
			$data['test'][] = $id_student;
			$data['test'][] = $id_day;
			$data['test'][] = $start_hour;
			$data['test'][] = $end_hour;
			$data['response'] = $this->create_booking($id_student, $id_day, $start_hour, $end_hour);


			header("content-type: application/json");
			print json_encode($data);
		}
	}

	public function search_student()
	{
		if ($this->session->userdata('user_id')) {
			$string_search = $this->input->post('search');
			$where_string = " (usr_name Like \"%" . $string_search . "%\" OR usr_surname like \"%" . $string_search . "%\") and usr_type = 4 and (st_special is not null and st_special != 1 ) and usr_deactive = 0";
			$this->db->where($where_string, NULL, false);
			$this->db->select("usr_id,usr_name, CONCAT(usr_surname,' ', if(bk_origin = 4,'(s)','')) as usr_surname", false);
			$this->db->order_by('usr_name,usr_surname');
			$this->db->join('student_details', 'st_id = usr_id');
			$query = $this->db->get('bk_users');
			$data['options'] = array();
			foreach ($query->result() as $item) {
				$data['options'][] = array('id' => $item->usr_id, 'name' => $item->usr_name . ' ' . $item->usr_surname);
			}

			header("content-type: application/json");
			print json_encode($data);
		} else {
		}
	}

	public function search_student_special()
	{
		if ($this->session->userdata('user_id')) {
			$string_search = $this->input->post('search');
			$where_string = " (usr_name Like '%" . $string_search . "%' OR usr_surname like '%" . $string_search . "%') and usr_type = 4 and st_special = 1  and usr_deactive = 0";
			$this->db->where($where_string, NULL, false);
			$this->db->select("usr_id,usr_name, usr_surname, st_n_students");
			$this->db->order_by('usr_id DESC,usr_name,usr_surname');
			$this->db->join('student_details', 'st_id = usr_id');
			$query = $this->db->get('bk_users');
			$data['options'] = array();
			foreach ($query->result() as $item) {
				$students = $item->st_n_students - 1;
				$data['options'][] = array('id' => $item->usr_id, 'name' => $item->usr_name . ' ' . $item->usr_surname . ($students > 0 ? '(+' . $students . ')' : ''));
			}

			header("content-type: application/json");
			print json_encode($data);
		} else {
		}
	}

	public function search_basic_info()
	{
		if ($this->session->userdata('user_id')) {
			$id_lessons = $this->input->post('id_lesson');

			$minutes = 30 * 60;

			if ($this->session->userdata('user_type') == 4) {
				$minutes = 60 * 60;
			}

			$this->db->where('bk_id', $id_lessons);
			$query_day = $this->db->get('bk_days');

			$data['hours_available'] = array();
			$data['hours_to'] = array();


			if ($query_day->num_rows() > 0) {
				$row = $query_day->row();
				$start_time = $row->hour_from;
				$end_time = $row->hour_to;
				$time1 = strtotime($start_time);
				$time2 = strtotime($end_time);
				$loop =  $time2 - $time1;


				for ($ii = $minutes; $ii <= 60 * 60 * 3; $ii += $minutes) {
					$data['hours_to'][] = $ii / (60 * 60);
				}

				while ($loop > 0) {
					$data['hours_available'][] = $start_time;
					$time1  = $time1 + $minutes;
					$start_time = date("H:i:s", $time1);
					$loop =  $time2 - $time1;
				}
			}




			header("content-type: application/json");
			print json_encode($data);
		}
	}

	public function change_status()
	{
		if ($this->session->userdata('user_id')) {
			$id = $this->input->post('id_booking');
			$new_status = $this->input->post('status');

			$this->db->where('bk_id', $id);
			$this->db->join('bk_day_groups', 'bd_id = bk_group');
			$q_t = $this->db->get('bk_days');
			$query_t = $q_t->row();

			$lesson_day = $query_t->bd_date;

			$current_status = $query_t->bk_status;
			$current_count = $query_t->bk_cancel_count;
			$type_day = $query_t->bk_level;
			$current_level = $query_t->bk_lesson_level;
			$student = $query_t->bk_student;

			$new_array = array(
				'bk_status' => $new_status
			);

			// New Level Confirm

			if ($new_status == 3) {

				if ($type_day == 1) {
					$this->db->where('st_id', $student);
					$query_s_q = $this->db->get('student_details');
					$query_s_r = $query_s_q->row();
					$old_level = $query_s_r->st_level;
					$new_array['bk_lesson_level'] = $old_level;
				}

				if ($type_day == 3) {
					if ($current_level == '') {
						$out_l = $this->check_special_level($id);
						$new_array['bk_lesson_level'] = $out_l['code'];
					}
				}
			}


			if ($new_status == 1) {
				$current_count += 1;
				$new_array['bk_cancel_count'] = $current_count;
				$new_array['bk_cancel_manager'] = date("Y-m-d H:i:s");
			}

			if ($new_status == 0) {

				// Update old level to student ?? 

				if ($type_day == 3) {
				}
				if ($type_day == 1) {
				}
			}

			$this->db->where('bk_id', $id);
			$this->db->update('bk_days', $new_array);

			if ($this->db->affected_rows() > 0) {
				if ($new_status == 1 && $type_day == 1) {
					$this->db->where('bk_student', $query_t->bk_student);
					$this->db->where('bk_status', 1);
					$this->db->select('Sum(bk_cancel_count) as count');
					$query_number = $this->db->get('bk_days');
					$query_number_row = $query_number->row();
					$json['cancel_count'] = $query_number_row->count;
					$json['student'] = $query_t->bk_student;
					$json['hl'] = $this->cal_hours_left_total($query_t->bk_student);
				}



				if ($new_status == 1 && $type_day == 3) {
				}


				$array_log = array(
					'l_user' => $this->session->userdata('user_id'),
					'l_action' => 7,
					'l_reference' => '',
					'l_observations' => 'Change Status from ' . $current_status . ' to ' . $new_status . '  student code :' . $student . ', lesson_day :' . $lesson_day,
					'l_student' => $student,
					'l_date' => date("Y-m-d H:i:s")
				);
				$this->db->insert("logs", $array_log);


				$json['status'] = $new_status;

				if ($this->input->post('new_level') && $current_status != 3) {
					$student = $query_t->bk_student;
					$type = $query_t->bk_level;

					$new_update = array(
						'st_level' => $this->input->post('new_level')
					);


					if ($type == 3) {
						$where_level = ' st_id IN (Select sg_student from special_group where sg_day = ' . $id . ')';
					} else {
						$where_level = ' st_id = ' . $student;
					}
					$this->db->where($where_level, null, false);
					$this->db->update('student_details', $new_update);
				}
			} else {
				$json['status'] = $current_status;
			}

			header("content-type: application/json");
			print json_encode($json);
		}
	}

	public function change_instructor()
	{
		if ($this->session->userdata('user_id')) {
			$id_day = $this->input->post('bk_day');
			$id_instructor = $this->input->post('instructor');
			/*
			$this->db->where('bk_group',$id_day);
			$this->db->where('bk_status','3');
			$q_t = $this->db->get('bk_days');
			*/
			$cancel = 0;
			$data['message_error'] = '';
			$instructor_return = '';

			// Cancel if a lesson is confirmed
			/*
			if($q_t->num_rows() > 0){
				$cancel = 1;
				
			}
			*/
			if ($id_instructor != '') {
				$where_others = ' bd_date = (Select bd_date From bk_day_groups where bd_id = ' . $id_day . ') And bd_id != ' . $id_day . ' And bd_instructor = ' . $id_instructor . ' and (bd_inactive != 1 or bd_inactive is null)';
				$this->db->where($where_others, null, false);
				$q_other = $this->db->get('bk_day_groups');
				if ($q_other->num_rows() > 0) {
					$cancel = 1;
					$data['message_error'] = 'The instructor is assigned in other column.';
				}
			}

			if ($cancel == 0) {
				if ($id_instructor == '') {
					$id_instructor = null;
				}
				$new_data = array(
					'bd_instructor' => $id_instructor
				);
				$this->db->where('bd_id', $id_day);
				$this->db->update('bk_day_groups', $new_data);
				if ($this->db->affected_rows() > 0) {
					$instructor_return = $id_instructor;
				}
			} else {
				$this->db->where('bd_id', $id_day);
				$q_info = $this->db->get('bk_day_groups');
				$q_row = $q_info->row();
				$instructor_return = $q_row->bd_instructor;
			}

			$data['instructor_name'] = 'Instructor No';
			$data['instructor_id'] = $instructor_return;
			if ($instructor_return != '' && $instructor_return != null) {
				$this->db->where('usr_id', $instructor_return);
				$q_s = $this->db->get('bk_users', $instructor_return);
				$q_s_row = $q_s->row();
				$data['instructor_name'] = $q_s_row->usr_name . ' ' . $q_s_row->usr_surname;
			}

			header("content-type: application/json");
			print json_encode($data);
		}
	}

	public function send_confirm_sms()
	{
		if ($this->session->userdata('user_id')) {
			$this->load->helper('misc');


			$vector_days = $this->input->post('v_days');
			$v_days = explode(',', $vector_days);

			$this->db->where('st_id', '2');
			$mes_q = $this->db->get('sms_templates');
			$mes_r = $mes_q->row();

			$original = $mes_r->st_template;

			if ($this->input->post("message") != '') {
				$original = $this->input->post("message");
			}

			$this->db->from('bk_days');
			$this->db->join('bk_day_groups', 'bk_group = bd_id');
			$this->db->join('special_group', 'sg_day = bk_id AND sg_status = 0', 'left');
			$this->db->join('bk_users', 'usr_id = IFNULL(bk_days.bk_student,special_group.sg_student)');
			$this->db->where_in('bk_id', $v_days);
			$result = $this->db->get();
			$group = array();

			foreach ($result->result() as $item) {
				$personal_message = str_ireplace('[hour]', $item->hour_from, $original);
				$personal_message = str_ireplace('[date]', $item->bd_date, $personal_message);
				$personal_message = str_ireplace('[name]', $item->usr_name, $personal_message);
				$group[] = send_sms($this->session->userdata('user_id'), $item->usr_id, $personal_message);
			}

			header("content-type: application/json");
			print json_encode($group);
		}
	}

	public function send_cancel_sms()
	{
		if ($this->session->userdata('user_id')) {
			$this->load->helper('misc');


			$vector_days = $this->input->post('v_days');
			$v_days = explode(',', $vector_days);

			$this->db->where('st_id', '3');
			$mes_q = $this->db->get('sms_templates');
			$mes_r = $mes_q->row();

			$original = $mes_r->st_template;

			$this->db->from('bk_days');
			$this->db->join('bk_day_groups', 'bk_group = bd_id');
			$this->db->join('special_group', 'sg_day = bk_id  AND sg_status = 0', 'left');
			$this->db->join('bk_users', 'usr_id = IFNULL(bk_days.bk_student,special_group.sg_student)');
			$this->db->where_in('bk_id', $v_days);
			$result = $this->db->get();
			$group = array();
			foreach ($result->result() as $item) {
				$personal_message = str_ireplace('[hour]', $item->hour_from, $original);
				$personal_message = str_ireplace('[date]', $item->bd_date, $personal_message);
				$group[] = send_sms($this->session->userdata('user_id'), $item->usr_id, $personal_message);
			}

			header("content-type: application/json");
			print json_encode($group);
		}
	}

	public function send_confirmation_all()
	{
		if ($this->session->userdata('user_id')) {
			$this->load->helper('misc');


			$vector_days = $this->input->post('date');
			$intervalDate = DateTime::createFromFormat('d/m/Y', $vector_days);

			$date_correct = $intervalDate->format('Y-m-d');
			$this->db->where('st_id', '1');
			$mes_q = $this->db->get('sms_templates');
			$mes_r = $mes_q->row();

			$original = $mes_r->st_template;

			$this->db->from('bk_days');
			$this->db->join('bk_day_groups', 'bk_group = bd_id');
			$this->db->join('special_group', 'sg_day = bk_id AND sg_status = 0', 'left');
			$this->db->join('bk_users', 'usr_id = IFNULL(bk_days.bk_student,special_group.sg_student)');
			$where = ' bd_date = "' . $date_correct . '" and (bd_inactive != 1 or bd_inactive is null) AND bk_status = 0';


			$this->db->where($where, null, false);
			$result = $this->db->get();
			$group = array();

			foreach ($result->result() as $item) {
				$personal_message = str_ireplace('[hour]', $item->hour_from, $original);
				$personal_message = str_ireplace('[date]', $item->bd_date, $personal_message);
				$group[] = send_sms($this->session->userdata('user_id'), $item->usr_id, $personal_message);
			}


			header("content-type: application/json");
			print json_encode($group);
		}
	}

	public function send_confirmation_instructors()
	{
		if ($this->session->userdata('user_id')) {
			$this->load->helper('misc');

			$vector_instructors = explode(',', $this->input->post('instructors'));

			$vector_days = $this->input->post('date');
			$intervalDate = DateTime::createFromFormat('d/m/Y', $vector_days);

			$date_correct = $intervalDate->format('Y-m-d');



			$this->db->join('bk_users', 'bd_instructor = usr_id');
			$where = ' bd_date = "' . $date_correct . '"and (bd_inactive != 1 or bd_inactive is null)';
			$this->db->where($where, null, false);
			$this->db->where_in('usr_id', $vector_instructors);
			$result = $this->db->get('bk_day_groups');
			$group = array();

			foreach ($result->result() as $item) {
				$message = 'Hi ' . $item->usr_name . ', you have been booked the ' . $date_correct . ' at ';
				$this->db->select('hour_from,hour_to,sl_description');
				$this->db->join("student_details as one", "bk_student = one.st_id", "left");
				$this->db->join("special_group", "sg_day = bk_id", "left");
				$this->db->join("student_details as two", "sg_student = two.st_id", "left");
				$this->db->join("student_level", "sl_id = IFNULL(one.st_level,bk_lesson_level)", "left");
				$this->db->where('(bk_level = 1 OR bk_level = 3)', NULL, false);
				$this->db->where('bk_group', $item->bd_id);
				$this->db->where('bk_status', '0');
				$this->db->order_by('hour_from');
				$this->db->group_by('hour_from,hour_to,sl_description');
				$get_shift = $this->db->get('bk_days');
				$message_c = '';
				foreach ($get_shift->result() as $shift) {
					$message_c .= $shift->hour_from . ' - ' . $shift->hour_to . ' - ' . $shift->sl_description . ', ';
				}
				if ($message_c != '') {
					$message .= $message_c . " Cheers!";
					$group[] = send_sms($this->session->userdata('user_id'), $item->usr_id, $message);
				}
			}

			header("content-type: application/json");
			print json_encode($group);
		}
	}

	public function send_confirmation_instructors_test()
	{

		$this->load->helper('misc');
		$vector_instructors = explode(',', '993,1175,496,1545');

		$vector_days = '06/04/2015';
		$intervalDate = DateTime::createFromFormat('d/m/Y', $vector_days);

		$date_correct = $intervalDate->format('Y-m-d');



		$this->db->join('bk_users', 'bd_instructor = usr_id');
		$where = ' bd_date = "' . $date_correct . '"and (bd_inactive != 1 or bd_inactive is null)';
		$this->db->where($where, null, false);
		$this->db->where_in('usr_id', $vector_instructors);
		$result = $this->db->get('bk_day_groups');
		$group = array();

		foreach ($result->result() as $item) {

			$message = 'Hi ' . $item->usr_name . ', you have been booked the ' . $date_correct . ' at ';
			$this->db->select('hour_from,hour_to,sl_description');
			$this->db->join("student_details as one", "bk_student = one.st_id", "left");
			$this->db->join("special_group", "sg_day = bk_id", "left");
			$this->db->join("student_details as two", "sg_student = two.st_id", "left");
			$this->db->join("student_level", "sl_id = IFNULL(one.st_level,two.st_level)", "left");
			$this->db->where('(bk_level = 1 OR bk_level = 3)', NULL, false);
			$this->db->where('bk_group', $item->bd_id);
			$this->db->where('bk_status', '0');
			$this->db->order_by('hour_from');
			$this->db->group_by('hour_from,hour_to,sl_description');
			$get_shift = $this->db->get('bk_days');
			echo '<br/>' . $this->db->last_query() . '<br/>';
			$message_c = '';
			foreach ($get_shift->result() as $shift) {
				$message_c .= $shift->hour_from . ' - ' . $shift->hour_to . ' - ' . $shift->sl_description . ', ';
			}
			if ($message_c != '') {
				$message .= $message_c . '<br/>';
				//					$group[] = send_sms($this->session->userdata('user_id'),$item->usr_id,$message);	
			}
			echo $message;
		}
		//echo $message_c;




	}

	public function cal_hours_left_total_student($student)
	{
		$this->load->helper('misc');
		$total = cal_hours_left($student);
		return $total;
	}

	public function cal_hours_left_total($student)
	{
		$query_special = "SELECT st_hours - (SUM(IF(bk_status = 3, TIME_TO_SEC( TIMEDIFF( hour_to, hour_from ) ) / ( 60 *60 )     ,0)) + SUM(IF( bk_status = 0,TIME_TO_SEC( TIMEDIFF( hour_to, hour_from ) )  / ( 60 *60 ),0))) - st_penalty as count
			FROM bk_day_groups
			INNER JOIN bk_days ON bk_group = bd_id
			LEFT JOIN special_group ON sg_day = bk_id And sg_status = 0
			LEFT JOIN student_details On st_id = IFNULL(sg_student,bk_student)
			WHERE bd_inactive IS NULL 
			AND ( sg_student = " . $student . " OR bk_student = " . $student . ")";
		$res = $this->db->query($query_special);
		//echo $this->db->last_query();	
		$res_f = $res->row();

		return isset($res_f->count) ? $res_f->count  : 0;
	}

	public function cal_hours_left_total_t($student)
	{
		$query_special = "SELECT st_hours - (SUM(IF(bk_status = 3, TIME_TO_SEC( TIMEDIFF( hour_to, hour_from ) ) / ( 60 *60 )     ,0)) + SUM(IF( bk_status = 0,TIME_TO_SEC( TIMEDIFF( hour_to, hour_from ) )  / ( 60 *60 ),0))) - st_penalty as count
			FROM bk_day_groups
			INNER JOIN bk_days ON bk_group = bd_id
			LEFT JOIN special_group ON sg_day = bk_id And sg_status = 0
			LEFT JOIN student_details On st_id = IFNULL(sg_student,bk_student)
			WHERE bd_inactive IS NULL 
			AND ( sg_student = " . $student . " OR bk_student = " . $student . ")";
		$res = $this->db->query($query_special);
		echo $this->db->last_query();
		$res_f = $res->row();

		echo isset($res_f->count) ? $res_f->count  : 0;
	}

	public function cal_hours_left_total_bk($student)
	{
		$query_special = "SELECT st_hours - (SUM(IF(bk_status = 3,  TIME_TO_SEC( TIMEDIFF( hour_to, hour_from ) ) / ( 60 *60 ) ,0)) + SUM(IF( bk_status = 0,TIME_TO_SEC(TIMEDIFF(hour_to,hour_from))/(60*60),0))  + SUM(IF((bk_status = 2 OR sg_status = 2) 
			AND timediff(STR_TO_DATE(CONCAT(bd_date, ' ', '10:00:00'), '%Y-%m-%d %H:%i:%s'),IFNULL(sg_date_cancel,bk_canceldate))  < TIME('24:00:00'),TIME_TO_SEC( TIMEDIFF( hour_to, hour_from ) ) / ( 60 *60 ),0))*0.5)
				as count
							FROM bk_day_groups
							INNER JOIN bk_days ON bk_group = bd_id
							LEFT JOIN special_group ON sg_day = bk_id And sg_status = 0
							LEFT JOIN student_details On st_id = IFNULL(bk_student,bk_student)
							WHERE bd_inactive IS NULL 
							AND ( sg_student = " . $student . " OR bk_student = " . $student . ")";
		$res = $this->db->query($query_special);
		//echo $this->db->last_query();	
		$res_f = $res->row();

		return isset($res_f->count) ? $res_f->count  : 0;
	}

	public function cal_hours_left_real($student)
	{
		$this->db->where('st_id', $student);
		$query_q = $this->db->get('student_details');
		$row_q = $query_q->row();

		$special = $row_q->st_special == null ? 0 : $row_q->st_special;

		if ($special != 1) {
			$query = 'SELECT SUM(CASE bk_status 
			WHEN 0 Then TIME_TO_SEC(TIMEDIFF(hour_to,hour_from))/(60*60) 
			WHEN 3 Then TIME_TO_SEC(TIMEDIFF(hour_to,hour_from))/(60*60) 
			WHEN 2 Then (TIME_TO_SEC(TIMEDIFF(hour_to,hour_from))/(60*60))*if(TIME_TO_SEC(TIMEDIFF(bk_canceldate,STR_TO_DATE(CONCAT(bd_date, " ", hour_to), "%Y-%m-%d %H:%m:%s")))/(60*60) < 2,0.5,0)
			Else 0 End )as count , bk_student
			FROM bk_days INNER JOIN bk_day_groups ON bk_group = bd_id Where bk_Student = ' . $student;
		} else {
			$query = 'SELECT SUM(CASE sg_status 
			WHEN 0 Then TIME_TO_SEC(TIMEDIFF(hour_to,hour_from))/(60*60) 
			WHEN 3 Then TIME_TO_SEC(TIMEDIFF(hour_to,hour_from))/(60*60) 
			WHEN 2 Then (TIME_TO_SEC(TIMEDIFF(hour_to,hour_from))/(60*60))*if(TIME_TO_SEC(TIMEDIFF(sg_date_cancel,STR_TO_DATE(CONCAT(bd_date, " ", hour_to), "%Y-%m-%d %H:%m:%s")))/(60*60) < 2,0.5,0)
			Else 0 End )as count , bk_student
			FROM bk_days INNER JOIN bk_day_groups ON bk_group = bd_id Inner Join special_group On sg_day = bk_id Where sg_student = ' . $student;
		}
		$res = $this->db->query($query);

		//echo $this->db->last_query();	
		$res_f = $res->row();


		return isset($res_f->count) ? $row_q->st_hours - $res_f->count  : 0;
	}

	public function cal_hours_left($student)
	{
		$this->db->where('st_id', $student);
		$query_q = $this->db->get('student_details');
		$row_q = $query_q->row();

		$special = $row_q->st_special == null ? 0 : $row_q->st_special;

		if ($special != 1) {
			$query = 'SELECT SUM(CASE bk_status 
			WHEN 0 Then TIME_TO_SEC(TIMEDIFF(hour_to,hour_from))/(60*60) 
			WHEN 3 Then TIME_TO_SEC(TIMEDIFF(hour_to,hour_from))/(60*60) 
			WHEN 2 Then (TIME_TO_SEC(TIMEDIFF(hour_to,hour_from))/(60*60))*if(TIME_TO_SEC(TIMEDIFF(bk_canceldate,STR_TO_DATE(CONCAT(bd_date, " ", hour_to), "%Y-%m-%d %H:%m:%s")))/(60*60) < 2,0.5,0)
			Else 0 End )as count , bk_student
			FROM bk_days INNER JOIN bk_day_groups ON bk_group = bd_id Where bk_Student = ' . $student;
		} else {
			$query = 'SELECT SUM(CASE sg_status 
			WHEN 0 Then TIME_TO_SEC(TIMEDIFF(hour_to,hour_from))/(60*60) 
			WHEN 3 Then TIME_TO_SEC(TIMEDIFF(hour_to,hour_from))/(60*60) 
			WHEN 2 Then (TIME_TO_SEC(TIMEDIFF(hour_to,hour_from))/(60*60))*if(TIME_TO_SEC(TIMEDIFF(sg_date_cancel,STR_TO_DATE(CONCAT(bd_date, " ", hour_to), "%Y-%m-%d %H:%m:%s")))/(60*60) < 2,0.5,0)
			Else 0 End )as count , bk_student
			FROM bk_days INNER JOIN bk_day_groups ON bk_group = bd_id Inner Join special_group On sg_day = bk_id Where sg_student = ' . $student;
		}
		$res = $this->db->query($query);

		//echo $this->db->last_query();	
		$res_f = $res->row();


		return isset($res_f->count) ? $res_f->count : 0;
	}

	private function interval_time($hours, $day)
	{
		// always from 10 am to 8pm.
		$config_v = $this->var_array();

		$this->db->where('dl_date', $day);
		$actualstate = $this->db->get('bk_day_limits');

		$new = 1;

		if ($actualstate->num_rows() > 0) {
			$new = 0;
			$res = $actualstate->row();
			$mem_ini = $res->dl_hour_ini;
			$mem_end = $res->dl_hour_end;
		} else {
			$mem_ini = $config_v['hour_ini'];
			$mem_end = $config_v['hour_end'];
		}



		$this->load->helper('misc');
		$interval = $hours * 60;
		$hour_ini = $mem_ini;
		$jump_hour = 1 * 60;
		$hour_end = getFutureTime($hour_ini, $interval);

		$date_int = array();

		while (strtotime($hour_end) < strtotime($mem_end)) {

			$date_int[] = array(
				'hour_ini' => $hour_ini,
				'hour_end' => $hour_end
			);
			$hour_ini = getFutureTime($hour_ini, $jump_hour);
			$hour_end = getFutureTime($hour_ini, $interval);
		}
		return $date_int;
	}

	public function find_next_booking_date()
	{
		if ($this->session->userdata('user_id')) {
			$day = $this->input->post('day');
			$hour = $this->input->post('hour');

			$this->db->where('usr_id', $this->session->userdata('user_id'));

			$this->db->join('student_details', 'st_id = usr_id');

			$user_qb = $this->db->get('bk_users');

			$user_info_b = $user_qb->row();
			$array_out = array();
			if ($user_info_b->st_special != 1) {

				$this->db->where('bd_date', $day);
				$count_spaces = $this->db->get('bk_day_groups');

				$array_a = $this->interval_time($hour, $day);

				foreach ($array_a as $interval) {
					foreach ($count_spaces->result() as $days_r) {
						// Validate Only Hours for Booking
						$where_val = " NOT ((hour_from >= Cast('" . $interval['hour_end'] . "' as time) AND hour_to > Cast('" . $interval['hour_end'] . "' as time)) 
								OR 
								(hour_from < Cast('" . $interval['hour_ini'] . "' as time) AND hour_to <= Cast('" . $interval['hour_ini'] . "' as time))) AND bk_group = " . $days_r->bd_id;
						$where_val .= ' AND bk_status In (0,3) and (bd_inactive != 1 or bd_inactive is null)';



						$this->db->where($where_val, NULL, false);
						$this->db->join('bk_day_groups', 'bd_id = bk_group');
						$query_val = $this->db->get('bk_days');




						$temp_date = $day;


						$where_val = " NOT ((hour_from >= Cast('" . $interval['hour_end'] . "' as time) AND hour_to > Cast('" . $interval['hour_end'] . "' as time)) 
								OR 
								(hour_from < Cast('" . $interval['hour_ini'] . "' as time) AND hour_to <= Cast('" . $interval['hour_ini'] . "' as time))) AND bd_date = '" . $temp_date . "'";
						$where_val .= ' AND bk_status In (0,3)   and (bd_inactive != 1 or bd_inactive is null) and bk_student = ' . $this->session->userdata('user_id');


						$this->db->where($where_val, NULL, false);
						$this->db->join('bk_day_groups', 'bd_id = bk_group');
						$this->db->order_by('hour_from');
						$query_val_same = $this->db->get('bk_days');
						$number_same = $query_val_same->num_rows();



						if ($query_val->num_rows() + $number_same == 0) {
							$interval['bd_id'] = $days_r->bd_id;
							$array_out[] = $interval;
							break;
						}
					}
				}
			} else {
				//Special - Selection First List only Level 2



				$query = 'Select ppal.* From bk_days ppal Inner Join bk_day_groups On ppal.bk_group = bd_id 
					Where (bd_inactive is null or bd_inactive <> 1) 
					AND 
					( ( (Select Count(*) From special_group where sg_day = ppal.bk_id) = 0 And ppal.bk_lesson_level is null) OR ifNull(ppal.bk_lesson_level,(Select st_level From student_details Inner Join special_group On sg_student = st_id Where sg_day = ppal.bk_id LIMIT 1)) = ' . $user_info_b->st_level . ')
					AND ppal.bk_level = 3 
					AND ppal.bk_status = 0
					AND TIME_TO_SEC(TIMEDIFF(hour_to,hour_from))/(60*60) = ' . $hour . ' 
					AND ppal.bk_id not in (Select sg_day from special_group where sg_student = ' . $this->session->userdata('user_id') . ' And sg_status = 0)
					AND bd_date ="' . $day . '" 
					AND ppal.bk_special_num >= (Select st_n_students from student_details where st_id = ' . $this->session->userdata('user_id') . ') + IFNULL((Select Sum(st_n_students) From student_details Inner Join special_group On sg_student = st_id Where sg_day = ppal.bk_id And sg_status = 0),0)
					ORDER BY hour_from';
				$thisdb = $this->db->query($query);
				foreach ($thisdb->result() as $item) {
					$interval['hour_ini'] = $item->hour_from;
					$interval['hour_end'] = $item->hour_to;
					$interval['bd_id'] = $item->bk_id;
					$interval['ss'] = $query;
					$array_out[] = $interval;
				}
			}
		}
		header("content-type: application/json");
		print json_encode($array_out);
	}

	public function forgot_password()
	{

		$email = $this->input->post('rec-email');

		$this->db->where('usr_email', $email);
		//$this->db->where_in('usr_type',array('1','2','4'));
		$query_t = $this->db->get('bk_users');
		$query_plus = 0;
		foreach ($query_t->result() as $pp) {
			$this->load->library('PasswordHash');

			$a = strtolower($pp->usr_name) . '123';

			$new_data['usr_pass'] = password_hash($a, PASSWORD_BCRYPT);

			$this->db->where('usr_id', $pp->usr_id);
			$this->db->update('bk_users', $new_data);

			$this->load->helper('misc');
			$from = 'school@kiterepublic.com.au';
			$subject = 'Kiterepublic Information';
			$to = $email;
			$message = '<h2>Hi ' . $pp->usr_name . ', your new password is </h2><br/>' . $a;
			send_email($from, $subject, $message, $to);
			$query_plus = 1;
		}
		if ($query_plus == 1) {
			$this->session->set_flashdata('warning', 'Your new password has been sent to your email');
		} else {
			$this->session->set_flashdata('warning', "This email doesn't appear in our data, please check");
		}



		header("Location: " . base_url());
	}

	public function validate_coupon()
	{

		$code_coupon = $this->input->post('coupon');
		$url = 'https://www.kiterepublic.com.au/wp-webservice.php';

		$data['warning'] = '';

		$where = " '" . $code_coupon . "' LIKE REPLACE( partial_code,  'X',  '_' )";

		$this->db->where($where, NULL, false);
		$temp_record = $this->db->get('voucher_details');



		if ($temp_record->num_rows() > 0) {


			if ($temp_record->num_rows() == 1) {

				$where = " '" . $code_coupon . "' LIKE REPLACE( partial_code,  'X',  '_' ) AND redeemed = 0";
				$this->db->where($where, NULL, false);
				$this->db->join('voucher_head', 'code_group = out_id');
				$real_record = $this->db->get('voucher_details');

				if ($real_record->num_rows() == 1) {

					$data_voucher = $real_record->row();

					$data_exp_sol = false;

					if ($data_voucher->expiry_date != '') {
						$date_exp = date_create_from_format('Y-m-d H:i:s', $data_voucher->expiry_date . ' 00:00:00');
						$now = new DateTime;
						if ($date_exp < $now) {
							$data_exp_sol = true;
						}
					}



					if (!$data_exp_sol) {


						if ($this->input->post('retrieve') == 1) {

							//persons

							$this->db->where('usr_email', $this->input->post('edit_email'));
							$query_q = $this->db->get('bk_users');



							if ($query_q->num_rows() > 0) {

								$data['warning'] = 'The email is already in the system';
							} else {

								$new_data = array(
									'usr_name' => $this->input->post('edit_name'),
									'usr_surname' => $this->input->post('edit_surname'),
									'usr_type' => '4',
									'usr_email' => $this->input->post('edit_email'),
									'usr_phone_main' => $this->input->post('phone_main'),
									'usr_phone_sec' => $this->input->post('sec_phone'),
									'usr_deactive' => 0
								);

								$data_student['st_level'] = $this->input->post('usr_level');
								$data_student['st_hours'] = $data_voucher->number_hours;
								$data_student['st_observations'] = $this->input->post('usr_obs');
								$data_student['st_special'] = $data_voucher->special_group;
								$data_student['st_n_students'] =  $data_voucher->number_student;


								if ($this->input->post('password') != '') {
									$this->load->library('PasswordHash');
									$new_data['usr_pass'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
									$a = $this->input->post('password');
								} else {
									$this->load->library('PasswordHash');
									$a = strtolower($this->input->post('edit_name')) . '123';
									$new_data['usr_pass'] = password_hash($a, PASSWORD_BCRYPT);
								}

								$this->load->helper('misc');
								$from = 'bookings@kiterepublic.com.au';
								$subject = 'New Password';
								$to = $this->input->post('edit_email');
								$message = '<h2>Your new password is </h2><br/>' . $a;
								$email_status = send_email($from, $subject, $message, $to);

								$debug_mail = NULL;

								if (!$email_status['success']) {
									$debug_mail = $email_status['debug'];
								}

								$this->db->insert('bk_users', $new_data);
								$new_id = $this->db->insert_id();
								$data_student['st_id'] = $new_id;
								$this->db->insert('student_details', $data_student);

								$data_voucher_update = array(
									'redeemed' => 1,
									'redeem_date' => date('Y-m-d h:m:s'),
									'user_id' => $new_id,
									'email_status' => $email_status['success'],
									'email_debug' => $debug_mail
								);

								$this->db->where('vid', $data_voucher->vid);
								$this->db->update('voucher_details', $data_voucher_update);

								$data['warning'] != 'You are now register, check your email to follow the instructions.';




								$this->session->set_flashdata('warning', $data['warning']);

								// Auto - Login

								$new_session = array(
									'user_type' => '4',
									'username' => $this->input->post('edit_email'),
									'user_id' => $new_id,
									'user_name' => $this->input->post('edit_name'),
									'user_surname' => $this->input->post('edit_surname'),
									'user_phone' => $this->input->post('phone_main'),
									'user_phone_sec' => $this->input->post('sec_phone')
								);
								$this->session->set_userdata($new_session);

								header("Location: " . base_url() . "main");


								//header("Location: ".base_url().'?reg=99');
								exit;
							} // Insert End (post variable)

						}

						$data['hours'] = $data_voucher->number_hours;
						$data['special'] = $data_voucher->special_group;
						$data['code'] = $code_coupon;
						$data['people'] = $data_voucher->number_student;
						$data['level'] = 1;
						$data['post_id'] = '';
						$data['config_var'] = $this->basic_var;
						$this->db->where('gn_code', 'terms_n_conditions');
						$this->db->select('gn_html');
						$temp_data = $this->db->get('bk_general_notes');
						$temp_r = $temp_data->row();
						$data['terms'] = $temp_r->gn_html;

						$this->load->view('coupon_registration', $data);
					} else {
						$data['warning'] = "Your coupon is expired!";
					}
				} else {
					$data['warning'] = "Your coupon is already reedem, if you don't remember your password, click in the link 'forgot your password'";
				}
			} else {
				$data['warning'] = "Please contact kite republic, this coupon can't be reedem online";
			} // More than two
		} else {


			$data = array('check_code' => $code_coupon, 'pass_code' => 'QKS123', 'validate' => 1);


			if (curl_version()["features"] & CURL_VERSION_HTTP2 !== 0) {
				// $url = "https://www.google.com/";
				$ch = curl_init();
				curl_setopt_array($ch, [
					CURLOPT_URL           	=> $url,
					CURLOPT_HEADER         	=> true,
					CURLOPT_NOBODY         	=> true,
					CURLOPT_POSTFIELDS		=> $data,
					CURLOPT_RETURNTRANSFER 	=> true,
					CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_2_0
				]);
				$response = curl_exec($ch);
				if ($response !== false && strpos($response, "HTTP/2") === 0) {
					$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
					$headers = substr($response, 0, $header_size);
					$body = substr($response, $header_size);
					$result = json_decode($response);
				} elseif ($response !== false) {
					echo "No HTTP/2 support on server.";
				} else {
					echo curl_error($ch);
				}
				curl_close($ch);
			} else {
				$options = array(
					'http' => array(
						'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query($data),
					),
				);
				$context  = stream_context_create($options);

				$json_result = file_get_contents($url, false, $context);


				$result = json_decode($json_result);
			}

			if (isset($result->status)) {
				if ($result->status == 'publish') {
					$this->db->where('tc_sku', $result->sku);
					$q_sku = $this->db->get('bk_table_coupons');
					$r_sku = $q_sku->row();
					if (isset($r_sku)) {
						$data['warning'] = '';
						$data['hours'] = $r_sku->tc_hours;
						$data['special'] = $r_sku->tc_special;

						if ($this->input->post('retrieve') == 1) {
							//persons
							$this->db->where('usr_email', $this->input->post('edit_email'));
							$query_q = $this->db->get('bk_users');
							if ($query_q->num_rows() > 0) {
								$data['warning'] = 'The email is already in the system';
							} // email
							else {
								$new_data = array(
									'usr_name' => $this->input->post('edit_name'),
									'usr_surname' => $this->input->post('edit_surname'),
									'usr_type' => '4',
									'usr_email' => $this->input->post('edit_email'),
									'usr_phone_main' => $this->input->post('phone_main'),
									'usr_phone_sec' => $this->input->post('sec_phone'),
									'usr_deactive' => 0
								);
								$data_student['st_level'] = $this->input->post('usr_level');
								$data_student['st_hours'] = $this->input->post('usr_hours');
								$data_student['st_observations'] = $this->input->post('usr_obs');
								$data_student['st_special'] = $this->input->post('usr_special');
								$data_student['st_n_students'] =  $this->input->post('usr_people');

								if ($this->input->post('password') != '') {
									$this->load->library('PasswordHash');
									$new_data['usr_pass'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
									$a = $this->input->post('password');
								} else {
									$this->load->library('PasswordHash');
									$a = strtolower($this->input->post('edit_name')) . '123';


									$new_data['usr_pass'] = password_hash($a, PASSWORD_BCRYPT);
								}

								$this->load->helper('misc');
								$from = 'bookings@kiterepublic.com.au';
								$subject = 'New Password';
								$to = $this->input->post('edit_email');
								$message = '<h2>New Password is </h2><br/>' . $a;
								send_email($from, $subject, $message, $to);


								$this->db->insert('bk_users', $new_data);
								$new_id = $this->db->insert_id();
								$data_student['st_id'] = $new_id;
								$this->db->insert('student_details', $data_student);

								$code_coupon = $this->input->post('coupon');
								$url = 'http://www.kiterepublic.com.au/wp-webservice.php';
								$data = array('check_code' => $code_coupon, 'pass_code' => 'QKS123',  'redeem' => 1);

								// use key 'http' even if you send the request to https://...
								$options = array(
									'http' => array(
										'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
										'method'  => 'POST',
										'content' => http_build_query($data),
									),
								);
								$context  = stream_context_create($options);
								$json_result = file_get_contents($url, false, $context);

								$result = json_decode($json_result);




								// Auto - Login

								$new_session = array(
									'user_type' => '4',
									'username' => $this->input->post('edit_email'),
									'user_id' => $new_id,
									'user_name' => $this->input->post('edit_name'),
									'user_surname' => $this->input->post('edit_surname'),
									'user_phone' => $this->input->post('phone_main'),
									'user_phone_sec' => $this->input->post('sec_phone')
								);
								$this->session->set_userdata($new_session);

								$data['warning'] = 'You are now register, check your email to follow the instructions.';
								$this->session->set_flashdata('warning', $data['warning']);

								header("Location: " . base_url() . "main");

								exit;
							}
						} // retrieve == 1
						$data['config_var'] = $this->basic_var;

						$data['code'] = $code_coupon;
						$data['people'] = $this->input->post('usr_people');
						$data['level'] = 1;
						$data['post_id'] = 0;

						$this->load->view('coupon_registration', $data);
					} // sku
					else {
						$data['warning'] = "Please contact kite republic, this coupon can't be reedem online";
					}
				} else // if publish or not
				{
					$data['warning'] = 'This coupon is already reedem';
				}
			} else {
				$data['warning'] = 'The code is invalid';
			}
		} // None voucher match

		if ($data['warning'] != '') {
			$this->session->set_flashdata('warning', $data['warning']);
			header("Location: " . base_url());
		}
	}

	public function lesson_hours()
	{
		if ($this->session->userdata('user_id') && $this->session->userdata('user_type') <= 2) {
			$data['basic_var'] = $this->basic_var;


			if ($this->input->post('date_ini') != "" && $this->input->post('date_end') != "") {

				$query = 'Select CONCAT(usr_name, " " , usr_surname) as instructor, Round(SUM(TIME_TO_SEC(TIMEDIFF(hour_to,hour_from))/(60*60) ),1) as Count 
				From bk_days 
				Inner Join bk_day_groups On bd_id = bk_group 
				Inner Join bk_users On bd_instructor = usr_id 
				Where bk_status = 3 And bd_date BETWEEN STR_TO_DATE("' . $this->input->post('date_ini') . '","%d/%m/%Y") 
						AND STR_TO_DATE("' . $this->input->post('date_end') . '","%d/%m/%Y")
				Group By CONCAT(usr_name, " " , usr_surname)
				Order By CONCAT(usr_name, " " , usr_surname)';
				$data['search'] = $this->db->query($query);

				//echo $this->db->last_query();

				// $query_search;
			}


			$this->load->view('lesson_hours', $data);
		} else {
			$data['basic_var'] = $this->basic_var;
			$this->load->view('index', $data);
		}
	}

	public function change_obs()
	{
		if ($this->session->userdata('user_id') && $this->session->userdata('user_type') <= 2) {

			$id_student = $this->input->post('id_student');
			$note = $this->input->post('note');

			if ($this->input->post('type') == 1) {


				$new_array = array('st_observations' => $note);
				$this->db->where('st_id', $id_student);
				$this->db->update('student_details', $new_array);
			} else {

				$new_array = array('bk_obs' => $note);
				$this->db->where('bk_id', $this->input->post('id_event'));
				$this->db->update('bk_days', $new_array);
			}

			$success = 0;

			if ($this->db->affected_rows() > 0) {
				$success = 1;
			}
			$group['success'] = $success;
			$group['test_ud'] = $this->input->post('id_event');
			$group['test_mess'] = $note;
			header("content-type: application/json");
			print json_encode($group);
		}
	}

	public function gen_bk_school()
	{

		$data['basic_var'] = $this->basic_var;
		$date = new DateTime();
		$date_show = $date->format('d/m/Y');

		$data['day_sel'] = $date_show;

		$currentdate = DateTime::createFromFormat('d/m/Y', $date_show);
		$current_date = '2013-11-26'; //$currentdate->format('Y-m-d');

		$query = "Select bd_date,Concat(instructor.usr_name, ' ', instructor.usr_surname  ) instructor, bd_id,hour_from,hour_to,Concat(students.usr_name, ' ', students.usr_surname  ) student
			From bk_day_groups
			Left Join bk_users instructor On instructor.usr_id  = bd_instructor
			Left Join bk_days On bk_group = bd_id AND bk_status in (0,3)
			Left Join bk_users students On students.usr_id = bk_student 
			Where (bd_inactive <> 1 OR bd_inactive is null) And bd_date = '" . $current_date . "'
			Order By hour_from,bd_id";
		$data_info = $this->db->query($query);
		$data['book'] = $data_info;
		$query_hd = "Select bd_date,Concat(instructor.usr_name, ' ', instructor.usr_surname  ) instructor, bd_id 
			From bk_day_groups
			Left Join bk_users instructor On instructor.usr_id  = bd_instructor
			Where (bd_inactive <> 1 OR bd_inactive is null) And bd_date = '" . $current_date . "'";
		$data['book_hd'] = $this->db->query($query_hd);
		$data['day'] = $current_date;

		$excel = $this->load->view('booking_manager_sh', $data, true);
		$namefile = $current_date . ".xls";
		file_put_contents("temp_files/" . $namefile, $excel);

		$attach[] = "temp_files/" . $namefile;

		$this->load->helper('misc');
		$from = 'bookings@kiterepublic.com.au';
		$subject = 'School Notifications';
		$to = 'nestor_ochoa99@hotmail.com';

		$message = '<h1>Lessons schelude for ' . $current_date . '</h1><p>Cheers!</p>';

		send_email($from, $subject, $message, $to, $attach);
	}

	public function test_saasu()
	{

		$params = array();
		$this->db->where('cfg_code', 'saasu_webkey');
		$query_wk = $this->db->get('config_var');
		$res_wk = $query_wk->row();
		$params['webaccess'] = $res_wk->cfg_value;



		$this->db->where('cfg_code', 'saasu_filekey');
		$query_wk = $this->db->get('config_var');
		$res_wk = $query_wk->row();
		$params['fileaccess'] = $res_wk->cfg_value;

		$this->load->library('Saasu');

		saasu_new_student($params['webaccess'], $params['fileaccess'], 161);
	}

	public function price_ajax()
	{
		if ($this->session->userdata('user_id') && $this->session->userdata('user_type') <= 2) {
			$hours = $this->input->post('hours') == '' ? 0 : $this->input->post('hours');
			$persons = $this->input->post('persons') == '' ? 0 : $this->input->post('persons');

			$this->db->where('shv_hours', $hours);
			$this->db->where('shv_people', $persons);
			$res_q = $this->db->get('student_hours_value');
			$res_row = $res_q->row();
			if (isset($res_row->shv_price)) {
				$group['price'] = $res_row->shv_price;
			} else {
				$group['price'] = '';
			}


			header("content-type: application/json");
			print json_encode($group);
		}
	}

	public function test_email()
	{
		$this->load->helper('misc');
		$data['basic_var'] = $this->basic_var;
		$tete = 'test';
		send_email('bookings@kiterepublic.com.au', 'test', $tete, 'nestor_ochoa99@hotmail.com', array());
	}

	public function operational_hours()
	{
		if ($this->session->userdata('user_id') && $this->session->userdata('user_type') <= 2) {
			$data['basic_var'] = $this->basic_var;

			$data['main_table'] = null;

			if ($this->input->post("month") != '' && $this->input->post("year") != '') {
				$query = "Select * From (SELECT ADDDATE('" . $this->input->post("year")  . "-" . $this->input->post("month") . "-01',i2.num*10+i1.num) day 
					 FROM ints i1, ints i2 
					 WHERE ADDDATE('" . $this->input->post("year") . "-" . $this->input->post("month") . "-01',i2.num*10+i1.num) < DATE_ADD('" . $this->input->post("year") . "-" . $this->input->post("month") . "-01', INTERVAL 1 MONTH) )
					day_month Left Join 
					(
					SELECT bd_date,Count(*) as instructors
					FROM `bk_day_groups` 
					Where (bd_inactive is NULL or bd_inactive != 1) 
					Group by bd_date Order by bd_date ) day_groups On bd_date = day 
					Left join bk_day_limits On bd_date = dl_date ";



				$data['main_table'] = $this->db->query($query);
			}




			$data["hours"] = $this->db->get("hours");

			$data["years"] = $this->db->get("years");
			$data["months"] = $this->db->get("months");

			$this->load->view('operational_hours', $data);
		} else {
			$data['basic_var'] = $this->basic_var;
			$this->load->view('index', $data);
		}
	}

	public function remove_wishlist()
	{
		if ($this->session->userdata('user_id') && $this->session->userdata('user_type') <= 2) {
			$id_book = $this->input->post('booking');
			$this->db->where('bk_id', $id_book);
			$this->db->where('bk_status', 1);
			$query_basis = $this->db->get('bk_days');
			$row_b = $query_basis->row();

			$success = 0;

			if ($row_b->bk_level == 1) {
				$student = $row_b->bk_student;
				$this->db->where('bk_student', $student);
				$this->db->where('bk_status', 1);
				$this->db->delete('bk_days');


				$array_log = array(
					'l_user' => $this->session->userdata('user_id'),
					'l_action' => 7,
					'l_reference' => '',
					'l_observations' => 'User remove bookings from wishlist  student code :' . $student,
					'l_student' => $student,
					'l_date' => date("Y-m-d H:i:s")
				);
				$this->db->insert("logs", $array_log);
			}

			if ($row_b->bk_level == 3) {
				$this->db->where('sg_day', $id_book);
				$this->db->delete('special_group');

				$this->db->where('bk_id', $id_book);
				$this->db->delete('bk_days');
			}

			if ($this->db->affected_rows() > 0) {
				$success = 1;
			}
			$data['success'] = $success;

			header("content-type: application/json");
			print json_encode($data);
		}
	}


	public function save_special_lesson()
	{
		if ($this->session->userdata('user_id') && $this->session->userdata('user_type') <= 2) {
			$group = $this->input->post("group");
			$student = $this->input->post("student");

			$this->db->where('st_id', $student);

			$student_info = $this->db->get('student_details');
			$s_info = $student_info->row();


			$this->db->where('sg_day', $group);
			$this->db->where('sg_status', '0');
			$this->db->join('student_details', 'sg_student = st_id');
			$this->db->select('sum(st_n_students) as total');
			$query_count = $this->db->get("special_group");
			$q_count = $query_count->row();
			$this->db->where('bk_id', $group);
			$query_day = $this->db->get('bk_days');
			$row_day = $query_day->row();

			$data['warning'] = '';
			if ($row_day->bk_special_num < $q_count->total + $s_info->st_n_students) {
				$data['warning'] = 'There is no more students allowed to this class.';
			} else {
				$this->db->where('sg_day', $group);
				$this->db->where('sg_student', $student);
				$this->db->where('sg_status', '0');
				$query_count_student = $this->db->get("special_group");
				if ($query_count_student->num_rows() > 0) {
					$data['warning'] = 'The student is already booked in this class';
				}
			}

			if ($data['warning'] == '') {
				$new_data = array(
					'sg_student' => $student,
					'sg_day' => $group,
					'sg_status' => '0'
				);
				$this->db->insert('special_group', $new_data);

				if ($this->db->affected_rows() > 0) {
					$data['success'] = 1;
					$this->db->where('st_id', $student);
					$this->db->join('bk_users', 'st_id = usr_id');
					$student_info = $this->db->get('student_details');
					$si_row = $student_info->row();
					$hours = $si_row->st_hours;
					$data['hours_left'] = $this->cal_hours_left_total($student);
					$data['name'] = $si_row->usr_name . ' ' . $si_row->usr_surname;
					$data['phone'] = $si_row->usr_phone_main;
					$data['usr_id'] = $si_row->usr_id;
					$data['number'] = $si_row->st_n_students;
					$data['level_updated'] = $this->check_special_level($group);

					$array_log = array(
						'l_user' => $this->session->userdata('user_id'),
						'l_action' => 4,
						'l_reference' => '',
						'l_observations' => 'User create special booking from event code : ' . $group . ', student code :' . $student,
						'l_student' => $student,
						'l_date' => date("Y-m-d H:i:s")
					);
					$this->db->insert("logs", $array_log);
				} else {
					$data['warning'] = "This booking couldn't be registered, please try again";
				}
			}


			header("content-type: application/json");
			print json_encode($data);
		}
	}

	public function remove_special()
	{
		if ($this->session->userdata('user_id') && $this->session->userdata('user_type') <= 2) {
			$group = $this->input->post("group");
			$student = $this->input->post("student");
			$data['warning'] = '';
			$this->db->where('sg_day', $group);
			$this->db->where('sg_student', $student);
			$query = $this->db->get('special_group');
			$data['success'] = 1;
			$data['student'] = $student;
			$data['group'] = $group;
			//$data['number'] = $this->db->last_query();
			if ($query->num_rows() > 0) {
				$row = $query->row();
				if ($row->sg_status == 3) {
					$data['warning'] = 'This student is confirmed already';
					$data['success'] = 0;
				} else {
					$this->db->where('sg_day', $group);
					$this->db->where('sg_student', $student);
					$this->db->delete('special_group');


					$data['level_updated'] = $this->check_special_level($group);

					// Log

					$array_log = array(
						'l_user' => $this->session->userdata('user_id'),
						'l_action' => 7,
						'l_reference' => '',
						'l_observations' => 'User delete special booking from event code : ' . $group . ', student code :' . $student,
						'l_student' => $student,
						'l_date' => date("Y-m-d H:i:s")
					);
					$this->db->insert("logs", $array_log);
				}
			}

			header("content-type: application/json");
			print json_encode($data);
		}
	}

	private function var_array()
	{
		$config_var_temp = $this->db->get('config_var');

		$response = array();
		foreach ($config_var_temp->result() as $item) {
			$response[$item->cfg_code] = $item->cfg_value;
		}
		return $response;
	}

	public function change_operational()
	{
		if ($this->session->userdata('user_id') && $this->session->userdata('user_type') <= 2) {
			$success = 0;
			$date = $this->input->post("date");
			$hour_ini = $this->input->post("hour_ini");
			$hour_end = $this->input->post("hour_end");
			$activate = $this->input->post("activate");
			$data["warning"] = '';

			if ($date != '' && $hour_ini != '' && $hour_end != '') {

				$config_v = $this->var_array();

				$this->db->where('dl_date', $date);
				$actualstate = $this->db->get('bk_day_limits');

				$new = 1;



				if ($actualstate->num_rows() > 0) {
					$new = 0;
					$res = $actualstate->row();
					$mem_ini = $res->dl_hour_ini;
					$mem_end = $res->dl_hour_end;
				} else {
					$mem_ini = $config_v['hour_ini'];
					$mem_end = $config_v['hour_end'];
				}

				$data["hour_ini"] = $mem_ini;
				$data["hour_end"] = $mem_end;



				$hourini = strtotime($hour_ini);
				$hourend = strtotime($hour_end);
				if ($hourini < $hourend) {
					$new_data = array(
						'dl_hour_ini' => $hour_ini,
						'dl_hour_end' => $hour_end
					);
					if ($new == 0) {
						$this->db->where('dl_date', $date);
						$this->db->update('bk_day_limits', $new_data);
					} else {
						$new_data['dl_date'] = $date;
						$this->db->insert('bk_day_limits', $new_data);
					}
					if ($this->db->affected_rows() > 0) {
						$data["hour_ini"] = $hour_ini;
						$data["hour_end"] = $hour_end;
					}
				} else {
					$data["warning"] = "The initial hour can't be greater than the final hour";
				}
			}

			header("content-type: application/json");
			print json_encode($data);
		}
	}

	public function optimize_table()
	{

		$query = $this->db->query('SHOW TABLES');
		foreach ($query->result() as $row) {
			$query_new = 'OPTIMIZE TABLE ' . $row->Tables_in_smartfol_book;
			$res = $this->db->query($query_new);
			var_dump($res->result());
		}
	}

	public function test_date()
	{
		$this->db->where('bk_id', 3891);
		$this->db->select('hour_from,bd_date,TIME_TO_SEC( TIMEDIFF( hour_to, hour_from ) ) / ( 60 *60 ) *0.5 as penalty');
		$this->db->join('bk_day_groups', 'bd_id = bk_group');
		$pre_select = $this->db->get('bk_days');
		$row_pre = $pre_select->row();


		$date_class = $row_pre->bd_date . 'T' . $row_pre->hour_from;
		$date_class_obj = new DateTime($date_class);
		$date_today = new DateTime('NOW');
		//$date_now = now();
		$diff = $date_today->diff($date_class_obj);
		var_dump($date_class_obj);
		echo '<br/>';
		var_dump($date_today);
		echo '<br/>';
		// Call the format method on the DateInterval-object
		$hours = $diff->h;
		$hours = $hours + ($diff->days * 24);
		echo $row_pre->penalty;
		echo $hours;
	}

	public function activate_user()
	{
		$id = $this->input->post("id");
		$this->db->where('usr_id', $id);
		$res = $this->db->get('bk_users')->row();

		$update_info['usr_deactive'] = $res->usr_deactive == 1 ? '0' : '1';

		$this->db->where('usr_id', $id);
		$this->db->update('bk_users', $update_info);

		$this->db->where('usr_id', $id);
		$res = $this->db->get('bk_users')->row();

		$data['deactive'] = $res->usr_deactive;

		header("content-type: application/json");
		print json_encode($data);
	}

	public function extract($id)
	{

		$this->db->where("usr_id", $id);
		$res = $this->db->get('bk_users')->row();


		$insert_user_stored_proc = "CALL hours_l2_l3(?)";
		$data = array('id' => $id);
		$result = $this->db->query($insert_user_stored_proc, $data);
		$name_m = "$res->usr_name.$res->usr_surname." . date('d.m.y_h.i.s') . "_$id.csv";

		header('Content-Type: text/csv; charset=utf-8');
		header("Content-Disposition: attachment; filename=\"$name_m\";");


		$fp = fopen("php://output", 'w');
		fputcsv($fp, array(
			"Name",
			"Surname", "Email", "Date", "Type", "Level", "Hours"
		));
		foreach ($result->result_array() as $item) {
			fputcsv($fp, array_values($item));
		}
		fclose($fp);
	}

	private function freeDate($year, $month)
	{

		$data['result'] = [];
		if ($year != '' && $month != '') {
			$query = "Select bd_date, SUM(books) > 0 OR SUM(timess) > 0 as available from 
			(SELECT  bd_date,bd_instructor, 
			(Select Count(*) = 0 from bk_days Where bk_group = bd_id and bk_status != 1) as books, 
			(Select SUM(TIME(hour_to) - TIME(hour_from)) < 80000 from bk_days Where bk_group = bd_id and bk_status != 1 ) as timess
			FROM `bk_day_groups` 
			Where   bd_inactive IS NULL And Date(bd_date) >= CURDATE()) as newone
			GROUP by bd_date  
			ORDER BY `newone`.`bd_date`  ASC";
			$result = $this->db->query($query);
			$data['result'] = $result->result();
		}

		return json_encode($data['result']);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */