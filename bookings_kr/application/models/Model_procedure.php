<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_procedure extends CI_Model
{
  public function hours_l2_l3($instructorId)
  {

    $firstQuery = $this->db
      ->select("iFNULL(ss.usr_name,su.usr_name) Name , IFNULL(ss.usr_surname, su.usr_surname) surname ,
    IFNULL(ss.usr_email,su.usr_email) email , bd_date, CASE WHEN ss.usr_name IS NOT NULL
    THEN ''
    ELSE 'Special'
    END AS Type, CONCAT('L', bk_lesson_level) as Level, TIMESTAMPDIFF(hour,hour_from,hour_to)")
      ->from('bk_day_groups')
      ->join('bk_days', 'bd_id = bk_group')
      ->join('bk_users AS ss', 'bk_student = ss.usr_id', 'left')
      ->join('special_group', 'bk_id = sg_day', 'left')
      ->join('bk_users AS su', 'su.usr_id = sg_student', 'left')
      ->where([
        'bd_instructor' => $instructorId,
        'bk_lesson_level' => 2,
        'bk_status' => 3,
        'TIMESTAMPDIFF(hour,hour_from,hour_to)', 3
      ])
      ->_compile_select();
    $this->db->_reset_select();

    $secondQuery = $this->db
      ->select("iFNULL(ss.usr_name,su.usr_name) Name , IFNULL(ss.usr_surname, su.usr_surname) surname ,
        IFNULL(ss.usr_email,su.usr_email) email , bd_date, CASE WHEN ss.usr_name IS NOT NULL
        THEN ''
        ELSE 'Special'
        END AS Type, CONCAT('L', bk_lesson_level) as Level, TIMESTAMPDIFF(hour,hour_from,hour_to)")
      ->from('bk_day_groups')
      ->join('bk_days', 'bd_id = bk_group')
      ->join('bk_users AS ss', 'bk_student = ss.usr_id', 'left')
      ->join('special_group', 'bk_id = sg_day', 'left')
      ->join('bk_users AS su', 'su.usr_id = sg_student', 'left')
      ->where(['bk_lesson_level' => 3,   'bk_status' => 3])
      ->_compile_select();
    $this->db->_reset_select();

    return $this->db->query("$firstQuery UNION $secondQuery")->get();
  }

  public function menu_user_type($user_type_sp)
  {
    return $this->db
      ->select("a.id_menu,a.description,a.link, (Select Count(*) From Menu b Inner Join user_access c On b.id_menu Like CONCAT(c.menu_id,'%') Where b.id_menu like Concat(a.id_menu, '%') And length(a.id_menu) < length(b.id_menu)) As Counter")
      ->from('Menu as a')
      ->join('user_access', "a.id_menu LIKE CONCAT( menu_id,  '%' ) 
    OR id_menu = SUBSTRING( menu_id, 1, LENGTH( a.id_menu ) ) ")
      ->where(['user_type' => $user_type_sp])
      ->order_by('id_menu', 'asc')->get()->result();
  }
}
