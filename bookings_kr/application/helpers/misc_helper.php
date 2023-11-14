<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
if (!function_exists('li_menu')) {
	function li_menu($recordset)
	{

		$level_bef = 1;
		$entry = 3;
		$time = 0;
		$mem = '';
		$html = '<ul>';
		foreach ($recordset->result() as $item) {

			$level = strlen($item->menu_id) / $entry;

			if ($time == 1) {
				if ($level_bef == $level && $time == 1) {
					$html .= '</li>';
				}
				if ($level_bef < $level && $time == 1) {
					$html .= '<ul>';
				}
				if ($level_bef > $level) {
					for ($i = 1; $i <= $level; $i++) {
						$html .= '</li></ul>';
					}
					$html .= '</li>';
				}
			} else {
				$time = 1;
			}


			$html .= '<li><a>' . $item->description . '</a>';
		}
		if ($time == 1) {
			$html .= '</li>';
		}
		$html = '</ul>';
	}
}
if (!function_exists('users_selection')) {
	function users_selection($user_type, $limit_low, $limit_up, $name = NULL, $phone = NULL, $level = NULL)
	{
		$CI = &get_instance();
		$CI->db->select('*');
		$CI->db->where('usr_type', $user_type);
		$CI->db->from('bk_users');
		if ($user_type == 4) {
			$CI->db->join('student_details', 'st_id = usr_id');
		}
		//$CI->db->limit($limit_low,$limit_up);

		if ($name == NULL && $phone == NULL && $level == NULL) {
			$CI->db->order_by('usr_id', 'desc');
		} else {
			$split_spaces = explode(" ", $name);
			$where = '';
			foreach ($split_spaces as $item) {
				if ($where != '') {
					$where .= ' AND ';
				}
				$where .= '(usr_name like "%' . $name . '%" OR usr_surname  like "%' . $name . '%" OR usr_phone_main  like "%' . $name . '%" OR usr_email  like "%' . $name . '%")';
			}
			$CI->db->where($where);
			$CI->db->order_by('usr_name', 'asc');
			$CI->db->order_by('usr_surname', 'asc');
		}

		$query_r['without'] = $CI->db->get();


		$CI->db->select('*');
		$CI->db->where('usr_type', $user_type);
		$CI->db->from('bk_users');
		if ($user_type == 4) {
			$CI->db->join('student_details', 'st_id = usr_id');
		}
		$CI->db->limit($limit_low, $limit_up);

		if ($name == NULL && $phone == NULL && $level == NULL) {
			$CI->db->order_by('usr_id', 'desc');
		} else {
			$split_spaces = explode(" ", $name);
			$where = '';
			foreach ($split_spaces as $item) {
				if ($where != '') {
					$where .= ' AND ';
				}
				$where .= '(usr_name like "%' . $name . '%" OR usr_surname  like "%' . $name . '%" OR usr_phone_main  like "%' . $name . '%" OR usr_email  like "%' . $name . '%")';
			}
			$CI->db->where($where);
			$CI->db->order_by('usr_name', 'asc');
			$CI->db->order_by('usr_surname', 'asc');
		}
		$query_r['with'] = $CI->db->get();



		return $query_r;
	}
}
if (!function_exists('getTimeDuration')) {
	function getTimeDuration($startTime, $endTime)
	{
		$start_time = explode(':', $startTime);
		$end_time = explode(':', $endTime);

		if (count($start_time) < 2) {
			return null;
		}
		if (count($end_time) < 2) {
			return null;
		}

		$end_ts = $end_time[0] * 60 + $end_time[1];
		$start_ts = $start_time[0] * 60 + $start_time[1];

		if ($start_time[0] > $end_time[0]) {
			$diff_ts = (24 * 60) - $start_ts + $end_ts;
		} else {
			$diff_ts = $end_ts - $start_ts;
		}

		return $diff_ts;
	}
}
if (!function_exists('formatTime')) {
	function formatTime($time, $format = '12')
	{
		$times = explode(':', $time);
		if (count($times) < 2) {
			return null;
		}

		$hour = $times[0];
		$minutes = $times[1];
		$suffix = '';
		if ($format == '12') {
			if ($hour >= 12) {
				if ($hour > 12) {
					$displayHours = intval($hour - 12);
				} else {
					$displayHours = intval($hour);
				}
				$suffix = 'pm';
			} else {
				if ($hour == '0') {
					$hour = '12';
				}
				$displayHours = intval($hour);
				$suffix = 'am';
			}
			$minutes = ($minutes == '00' ? '' : $minutes);
		} else if ($format == '24') {
			$displayHours = $hour;
		}

		return $displayHours . ($minutes != '' ? ':' . $minutes : $minutes) . $suffix;
	}
}
if (!function_exists('formatDescriptiveTimes')) {
	function formatDescriptiveTimes($startTime, $endTime)
	{
		return formatTime($startTime) . '-' . formatTime($endTime);
	}
}
if (!function_exists('getFutureDate')) {
	function getFutureDate($date, $daysFrom)
	{
		$time = strtotime($date);
		if ($time == false) {
			return null;
		}
		$ddate = getdate($time);

		$time = mktime(0, 0, 0, $ddate['mon'], ($ddate['mday'] + $daysFrom), $ddate['year']);

		return date('Y-m-d', $time);
	}
}

if (!function_exists('getFutureTime')) {
	function getFutureTime($time, $minutes)
	{
		$startTime = explode(':', $time);
		if (count($startTime) < 2) {
			return null;
		}

		$startHours = intval($startTime[0]);
		$startMinutes = intval($startTime[1]);

		$now = mktime($startHours, $startMinutes, 0);
		$futureTime = $now + ($minutes * 60);

		$formattedFutureTime = date('H:i:s', $futureTime);
		return $formattedFutureTime;
	}
}

if (!function_exists('send_email')) {

	function send_email($from, $subject, $message, $to, $attachments = NULL)
	{
		$CI = &get_instance();

		$debug = '';
		$config['protocol'] = "smtp";
		$config['smtp_host'] = "ssl://smtp.gmail.com"; # Not tls:// 
		$config['mail_path'] = 'ssl://smtp.gmail.com';
		$config['smtp_port'] = "465";
		$config['mailtype'] = "html";
		//$config['auth'] = true;
		$config['smtp_user'] = "school@kiterepublic.com.au";
		$config['smtp_pass'] = "schoolnaish";
		$config['smtp_timeout'] = 5;
		$CI->load->library('email', $config);

		$CI->email->set_newline("\r\n");
		$config['crlf'] = "\r\n";
		$CI->email->from($from, 'Kite Republic');
		$CI->email->to($to);
		$CI->email->subject($subject);
		$data['message'] = $message;

		$data['basic_var'] = $CI->basic_var;


		$message_html = $CI->load->view('email_template', $data, true);


		$CI->email->message($message_html);

		if ($attachments != NULL) {
			foreach ($attachments as $attach) {
				$CI->email->attach($attach);
			}
		}

		// Set to, from, message, etc.
		$return['success'] = false;



		if (!$CI->email->send()) {
			$debug = $CI->email->print_debugger();
		} else {
			$return['success'] = true;
		}
		$return['debug'] = $debug;

		sleep(7);
		return $return;
	}
}

if (!function_exists("get_fcontent")) {
	function get_fcontent($url)
	{
		$url = str_replace("&amp;", "&", urldecode(trim($url)));

		$cookie = tempnam("/tmp", "CURLCOOKIE");
		$ch = curl_init();
		curl_reset($ch);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		// curl_setopt($ch, CURLOPT_ENCODING, "");
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);    # required for https urls
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		$content = curl_exec($ch);
		$response = curl_getinfo($ch);
		curl_close($ch);
		return array($content, $response);
		// if ($response['http_code'] == 301 || $response['http_code'] == 302) {
		// 	ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");

		// 	if ($headers = get_headers($response['url'])) {
		// 		foreach ($headers as $value) {
		// 			if (substr(strtolower($value), 0, 9) == "location:")
		// 				return get_url(trim(substr($value, 9, strlen($value))));
		// 		}
		// 	}
		// }

		// if ((preg_match("/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content, $value) || preg_match("/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value)) && $javascript_loop < 5) {
		// 	return get_url($value[1], $javascript_loop + 1);
		// } else {
		// 	return array($content, $response);
		// }
	}
}

if (!function_exists('send_sms')) {

	function send_sms($admin_id, $user, $message)
	{

		$data_book = array(
			'sh_from' => $admin_id,
			'sh_to' => $user,
			'sh_sms' => $message,

		);

		$CI = &get_instance();

		$CI->db->where('usr_id', $user);
		$query_mob = $CI->db->get('bk_users');
		$q_row = $query_mob->row();

		//$mobile ='0451425811';
		$mobile = str_replace(" ", "", $q_row->usr_phone_main);
		$call = 'https://smsgw.exetel.com.au/sendsms/api_sms.php?username=shopkite&password=Kite@phiePee0&mobilenumber=' . urlencode($mobile) . '&message=' . urlencode($message) . '&sender=61395370644&messagetype=Text';

		// $homepage = get_fcontent($call);
		$homepage = file_get_contents($call);

		$totalm = explode('|', $homepage);

		/*$detail = array(
				'Talent' => $value,
				'Mobile' => $totalm[1],
				'StatusM' => $totalm[0],
				'ExtId'  => $totalm[3],
				'Message' => $totalm[4]
					
			);*/
		$data_book['sh_status'] = $totalm[0];
		$data_book['sh_ext_id'] = $totalm[3];
		$data_book['sh_mobile'] = $totalm[1];
		$CI->db->insert('sms_history', $data_book);

		$data_book['name'] = $q_row->usr_name . ' ' . $q_row->usr_surname;

		return $data_book;
	}
}


if (!function_exists('check_sms_credit')) {
	function check_sms_credit()
	{
		$homepage = file_get_contents('https://smsgw.exetel.com.au/sendsms/api_sms_credit.php?username=shopkite&password=Kite@phiePee0');
		$totalm = explode('|', $homepage);
		return $totalm[1];
	}
}

if (!function_exists('cal_hours_left')) {
	function cal_hours_left($student)
	{

		$CI = &get_instance();
		$CI->db->where('st_id', $student);
		$query_q = $CI->db->get('student_details');
		$row_q = $query_q->row();

		$hours = $row_q->st_hours;
		$h_confirm = round(cal_hours_left_confirmed($student), 2);
		$h_booked = round(cal_hours_left_booked($student), 2);
		$h_penalty = $row_q->st_penalty;

		$h_left = $hours - ($h_confirm + $h_booked + $h_penalty);



		return isset($h_left) ? $h_left  : 0;
	}
}


if (!function_exists('cal_hours_left_confirmed')) {
	function cal_hours_left_confirmed($student)
	{
		$CI = &get_instance();
		$CI->db->where('st_id', $student);
		$query_q = $CI->db->get('student_details');
		$row_q = $query_q->row();

		$special = $row_q->st_special == null ? 0 : $row_q->st_special;




		$query = 'SELECT SUM( TIME_TO_SEC( TIMEDIFF( hour_to, hour_from ) ) / ( 60 *60 ) ) as count, ' . $student . ' as bk_student
			FROM bk_day_groups
			INNER JOIN bk_days ON bk_group = bd_id
			LEFT JOIN special_group ON sg_day = bk_id And sg_status = 0
			WHERE bd_inactive IS NULL 
			AND bk_status = 3
			AND ( sg_student = ' . $student . ' OR bk_student = ' . $student . ' )';

		$res = $CI->db->query($query);
		//echo $this->db->last_query();	
		$res_f = $res->row();
		return isset($res_f->count) ? $res_f->count : 0;
	}
}

if (!function_exists('cal_hours_left_booked')) {
	function cal_hours_left_booked($student)
	{
		$CI = &get_instance();
		$CI->db->where('st_id', $student);
		$query_q = $CI->db->get('student_details');
		$row_q = $query_q->row();

		$special = $row_q->st_special == null ? 0 : $row_q->st_special;



		$query = 'SELECT SUM(TIME_TO_SEC(TIMEDIFF(hour_to,hour_from))/(60*60)) as count , ' . $student . ' as bk_student
				FROM bk_day_groups
				INNER JOIN bk_days On bk_group = bd_id
				LEFT JOIN  special_group On sg_day = bk_id AND sg_status =0
				WHERE bd_inactive is Null And bk_status = 0 AND (sg_student = ' . $student . ' OR bk_student = ' . $student . ')';


		$res = $CI->db->query($query);
		//echo $this->db->last_query();	
		$res_f = $res->row();
		return isset($res_f->count) ? $res_f->count : 0;
	}
}
if (!function_exists('cal_hours_left_penalty')) {
	function cal_hours_left_penalty($student)
	{
		$CI = &get_instance();
		$CI->db->where('st_id', $student);
		$query_q = $CI->db->get('student_details');
		$row_q = $query_q->row();

		$special = $row_q->st_special == null ? 0 : $row_q->st_special;

		/*	if($special!=1){
		$query = 'SELECT SUM(CASE bk_status  
			WHEN 2 Then (TIME_TO_SEC(TIMEDIFF(hour_to,hour_from))/(60*60))*if(TIME_TO_SEC(TIMEDIFF(bk_canceldate,STR_TO_DATE(CONCAT(bd_date, " ", hour_to), "%Y-%m-%d %H:%m:%s")))/(60*60) < 2,0.5,0)
			Else 0 End )as count , bk_student
			FROM bk_days INNER JOIN bk_day_groups ON bk_group = bd_id Where bk_Student = ' . $student;
		}else{
		$query = 'SELECT SUM(CASE sg_status 
			WHEN 2 Then (TIME_TO_SEC(TIMEDIFF(hour_to,hour_from))/(60*60))*if(TIME_TO_SEC(TIMEDIFF(sg_date_cancel,STR_TO_DATE(CONCAT(bd_date, " ", hour_to), "%Y-%m-%d %H:%m:%s")))/(60*60) < 2,0.5,0)
			Else 0 End )as count , bk_student
			FROM bk_days INNER JOIN bk_day_groups ON bk_group = bd_id Inner Join special_group On sg_day = bk_id Where sg_student = ' . $student;
		}*/

		$query = "SELECT SUM( TIME_TO_SEC( TIMEDIFF( hour_to, hour_from ) ) / ( 60 *60 ) ) * 0.5 as count, " . $student . " as bk_student
			FROM bk_day_groups
			INNER JOIN bk_days On bk_group = bd_id
			LEFT JOIN  special_group On sg_day = bk_id
			WHERE bd_inactive is Null And (bk_status = 2 OR sg_status = 2) 
			AND timediff(STR_TO_DATE(CONCAT(bd_date, ' ', '10:00:00'), '%Y-%m-%d %H:%i:%s'),IFNULL(sg_date_cancel,bk_canceldate))  < TIME('24:00:00')
			AND (sg_student = " . $student . " OR bk_student = " . $student . ")";


		$res = $CI->db->query($query);
		//echo $this->db->last_query();	
		$res_f = $res->row();
		return isset($res_f->count) ? $res_f->count : 0;
	}
}
