<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sms extends CI_Controller
{
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
      $this->load->helper('mysqli');
      $user_type = $this->session->userdata('user_type');

      // $this->basic_var['recordset_menu'] = $this->db
      // 	->join('user_access', 'id_menu like concat(menu_id, "%")', 'left')
      // 	->order_by('id_menu', 'asc')
      // 	->where('user_type', $user_type)->get('Menu')->result();

      $this->basic_var['recordset_menu'] = $this->model_procedure->menu_user_type($user_type);
    }
  }
  public function sms_templates()
  {
    if (!$this->session->userdata('user_type')) {
      header("Location: " . base_url() . "main");
    } else {
      $data['data'] = $this->db->from('sms_templates')->get()->result();
      $data['basic_var'] = $this->basic_var;
      $data['content'] = 'sms_templates.inc.php';
      $this->load->view('layout', $data);
    }
  }

  public function update()
  {
    foreach ($this->input->post() as $key => $value) {
      $tempId = str_replace("text-", "", $key);
      if ($tempId && $value !== '') {
        $this->db
          ->where('st_id', $tempId)
          ->set('st_template', trim($value, " "))
          ->update('sms_templates');
      }
    }

    echo "<h2>Updated</h2>";
  }
}