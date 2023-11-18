<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bookings extends CI_Controller
{
  public $basic_var = [];

  public function __construct()
  {
    parent::__construct();
  }
  public function init()
  {
    // if ($this->session->userdata('user_id') && $this->session->userdata('user_type') <= 2) {
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

    header("Access-Control-Allow-Origin: *");
    header("content-type: application/json");
    print json_encode($data);
    // } else {
    //   echo "NO AUTH";
    // }
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