<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "welcome";
$route['404_override'] = '';



$route['validate_user'] = "welcome/validate_user";
$route['main'] = "welcome/main";

$route['user_edit'] = "welcome/user_edit";
$route['user_edit/(:any)'] = "welcome/user_edit/$0";

$route['users'] = "welcome/users";
$route['instructors'] = "welcome/instructors";
$route['students'] = "welcome/students";
$route['extract/(:any)'] = "welcome/extract/$1";
$route['users/(:any)'] = "welcome/users/$0";
$route['instructors/(:any)'] = "welcome/instructors/$0";
$route['students/(:any)'] = "welcome/students/$0";
$route['lesson_hours'] = "welcome/lesson_hours";
$route['forgot_password'] = "welcome/forgot_password";
$route['user_school'] = "welcome/user_school";
$route['user_school/(:any)'] = "welcome/user_school/$0";
$route['validate_coupon'] = "welcome/validate_coupon";
$route['log_out'] = "welcome/log_out";
$route['operational_hours'] = "welcome/operational_hours";
$route['status'] = "welcome/status";
$route['start'] = "welcome/start";
$route['bookings'] = "welcome/bookings";
// Voucher

$route['coupon_list'] = "voucher/coupon_list";
$route['voucher_details/(:any)'] = "voucher/voucher_details/$0";
$route['student_details/(:any)'] = "reports/student_info/$0";


/* End of file routes.php */
/* Location: ./application/config/routes.php */