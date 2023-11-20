<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bookings extends CI_Controller
{
  public $basic_var = [];

  public function __construct()
  {
    parent::__construct();
    // if (!$this->session->userdata('user_id')) {
    //   header("content-type: application/json");
    //   $json["error"] = 403;
    //   $json["message"] = 'NOT AUTH';
    //   print json_encode($json);
    // }
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
  }

  public function baseInfo()
  {
    $data['instructors'] = $this->db
      ->where(array('usr_type' => '3', 'usr_deactive !=' => '1'))
      ->order_by('usr_name asc, usr_surname asc')
      ->get('bk_users')->result();
    $data['select_booking_types'] = $this->db->get('booking_type')->result();
    $data['select_student_level'] = $this->db->get('student_level')->result();

    header("content-type: application/json");
    print json_encode($data);
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

    $data['day_schedule'] = $this->db->where('bd_date', $current_date)
      ->where('(bd_inactive != 1 OR bd_inactive is NULL)')
      ->join('bk_users', 'usr_id = bd_instructor', 'left')
      ->get('bk_day_groups')->result();


    $data['typeUser'] = $this->session->userdata('user_type');


    header("content-type: application/json");
    print json_encode($data);
    // } else {
    //   echo "NO AUTH";
    // }
  }

  public function wishlist()
  {
    // if ($this->session->userdata('user_id')) { 
    $date_t = $this->input->post("date_a");

    $date_current = DateTime::createFromFormat('d/m/Y', $date_t);


    $date_correct_sql = $date_current->format('Y-m-d');

    $this->load->helper('misc');
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
    // } else {
    //   header("content-type: application/json");
    //   $json["rc"] = 99;
    //   print json_encode($json);
    // }
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

    return  $data['result'];
  }

  public function json_event()
  {
    // if ($this->session->userdata('user_id')) {
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
    // } else {
    //   header("content-type: application/json");
    //   $json["rc"] = 99;
    //   print json_encode($json);
    // }
  }

  public function json_save_lesson()
  {
    // if ($this->session->userdata('user_id')) {
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
    // } else {
    // 	header("content-type: application/json");
    // 	$json["rc"] = 99;
    // 	print json_encode($json);
    // }
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
}