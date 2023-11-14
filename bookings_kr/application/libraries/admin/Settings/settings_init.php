<?php
/**
 * Defines the array of settings which are displayed in admin.
 *
 * Settings are defined here and displayed via functions.
 *
 * @author 		Nestor Ochoa
 * @category 	Admin
 * @package 	Saasu_wordpress_orders/admin/settings
 * @version     0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WC_saasu;



$saasu_woo_settings['saasu_config'] = apply_filters('woo_saasu_settings', array(

	array(	'title' => __( 'Webservices Saasu Options', 'Saasu_Woo' ), 'type' => 'title','desc' => '', 'id' => 'saasu_options' ),


	array(
		'title' => __( 'Web services key', 'Saasu_Woo' ),
		'desc' 		=> '',
		'id' 		=> 'woocommerce_saasu_webkey',
		'default'	=> __( '', 'woocommerce' ),
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
	),

	array(
		'title' => __( 'File ID', 'Saasu_Woo' ),
		'desc' 		=> '',
		'id' 		=> 'woocommerce_saasu_filekey',
		'default'	=> __( '', 'Saasu_Woo' ),
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
	),


	

	array( 'type' => 'sectionend', 'id' => 'saasu_woo_options'),

)); // End inventory settings