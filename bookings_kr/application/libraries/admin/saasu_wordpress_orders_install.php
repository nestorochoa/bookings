<?php
/**
 * Saasu Woocommerce Orders Install
 *
 * Plugin install script which adds default pages, taxonomies, and database tables to WordPress. Runs on activation and upgrade.
 *
 * @author 		Nestor Ochoa
 * @category 	Admin
 * @package 	WooCommerce/Admin/Install
 * @version     0.0.1
 */
 
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
 
	function do_saasu_woo_install(){
	 	//saasu_woocommerce_default_taxonomies();
		global $wpdb;
		
		// create custom order data table
		$wpdb->query('
		  CREATE TABLE IF NOT EXISTS wp_woocommerce_saasu_data (
			order_id INT NOT NULL,
			custom_key VARCHAR(32) NOT NULL,
			custom_value TEXT
		  ) ENGINE = MYISAM;
		');
	}
 
 function saasu_woocommerce_default_taxonomies() {

	$taxonomies = array(
		'shop_order_status' => array(
			'sync'
		)
	);

	foreach ( $taxonomies as $taxonomy => $terms ) {
		foreach ( $terms as $term ) {
			if ( ! get_term_by( 'slug', sanitize_title( $term ), $taxonomy ) ) {
				wp_insert_term( $term, $taxonomy );
			}
		}
	}
}
?>