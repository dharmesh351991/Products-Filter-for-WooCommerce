<?php

/**
 * Plugin Name:       WPCodex WooCommerce Products Filter
 * Plugin URI:        https://wordpress.org
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            WP Codex team
 * Author URI:        https://wordpress.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpcodex-woo-products-filter
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
if ( ! defined( 'WOO_PRO_FILTER_DIR' ) ) {
    define( 'WOO_PRO_FILTER_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'WOO_PRO_FILTER_URL' ) ) {
    define( 'WOO_PRO_FILTER_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'WOOWPCODEX_TEMPLATES_PATH' ) ) {
    define( 'WOOWPCODEX_TEMPLATES_PATH', plugin_dir_path( __DIR__ ).'woocommerce/templates' );
}
if ( ! defined( 'WOOCOM_FILTER_OPTS' ) ) {
    define( 'WOOCOM_FILTER_OPTS', 'woocom_filter_opts_' );
}
class Woo_productFilter{
	public function __construct(){
		register_activation_hook( __FILE__, array($this, 'Woo_proFilter_activate' ) );
		add_action('plugins_loaded', array($this, 'Woo_proFilter_loaded_notice' ) );
		add_action( 'load_text_domain_hook', array($this, 'Woo_proFilter_load_text_domain' ) );
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_action_links') );
	}
	public function Woo_proFilter_activate(){
		flush_rewrite_rules();
		global $wpdb;
		$table_name = $wpdb->prefix . "woocodex_filter"; 
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE IF NOT EXISTS  $table_name (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  meta_key varchar(55) DEFAULT '' NOT NULL,
		  input_type varchar(55) DEFAULT '' NOT NULL,
		  is_enable varchar(10) DEFAULT '' NOT NULL,
		  hide_empty varchar(10) DEFAULT '' NOT NULL,
		  meta_order varchar(10) DEFAULT '' NOT NULL,
		  create_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  modified_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  PRIMARY KEY  (id)
		) $charset_collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
	public function Woo_proFilter_loaded_notice(){
		if ( ! function_exists( 'is_plugin_active' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }
        if ( ! function_exists( 'WC' ) ) {
            add_action( 'admin_notices', array($this, 'Woo_proFilter_install_admin_notice' ) );
        }else{
        	do_action( 'load_text_domain_hook');
        }
	}
	public function Woo_proFilter_install_admin_notice(){
		?>
		<div class="error">
            <p><?php _e( 'Woocommerce Product Filter is enabled but not effective. It requires <b>WooCommerce</b> in order to work.', 'wpcodex-woo-products-filter' ); ?></p>
        </div>
		<?php
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}
	public function Woo_proFilter_load_text_domain(){
		load_plugin_textdomain( 'wpcodex-woo-products-filter', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	public function add_action_links ( $links ) {
		$mylinks = array('<a href="' . admin_url( 'admin.php?page=woocommerce-filter-products' ) . '">Settings</a>');
		return array_merge( $links, $mylinks );
	}
}
if(class_exists('Woo_productFilter')){
	new Woo_productFilter(TRUE);
}
require 'includes/class-woo-filter-products.php';
require 'public/hooks-woo-filter-products-public.php';

if( !file_exists( get_template_directory().'/product-filter-form.php' ) ){	
    include( get_template_directory().'/product-filter-form.php' );
}else{
	include( WOO_PRO_FILTER_DIR.'/template/product-filter-form.php' );
}

