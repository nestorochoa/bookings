<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Instructors extends CI_Controller
{
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
    } else {
      redirect('/', 'refresh');
    }
  }

  public function instructors()
  {

    if ($this->session->userdata('user_id')) {
      $user_access = 3;
      $data['user_level'] = $user_access;
      $data['warning'] = '';
      if ($this->session->userdata('user_type') >= $user_access) {
        $data['warning'] = 'Access forbidden';
      }

      $this->load->library('pagination');
      $this->load->helper('misc');
      $this->config_pag['base_url'] = base_url() . 'instructors';

      $temp_res = users_selection(3, $this->config_pag['per_page'], $this->uri->segment(2), $this->input->get("search_user"), $phone = NULL, $level = NULL);
      $this->config_pag['total_rows'] = $temp_res['without']->num_rows();
      $this->pagination->initialize($this->config_pag);
      $data['links_pag'] = $this->pagination->create_links();
      $data['user_list'] = $temp_res['with'];
      $data['basic_var'] = $this->basic_var;
      $data['show_hours'] = true;
      $this->load->view('users', $data);
    } else {
      header("Location: " . base_url());
    }
  }

  public function scheduleInstructor($instructorId)
  {
    $data['basic_var'] = $this->basic_var;
    $data['instructorId'] = $instructorId;
    $date = new DateTime();
    $date_show = $date->format('d/m/Y');
    $currentdate = DateTime::createFromFormat('d/m/Y', $date_show);
    $current_date = $currentdate->format('Y-m-d');

    $data['day_schedule'] = $this->db
      ->where('bd_date', $current_date)
      ->where('(bd_inactive != 1 OR bd_inactive is NULL)')
      ->join('bk_users', 'usr_id = bd_instructor', 'left')
      ->get('bk_day_groups');

    $this->load->view('instructor/schedule', $data);
  }
}
