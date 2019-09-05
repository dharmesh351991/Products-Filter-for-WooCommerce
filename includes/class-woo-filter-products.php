<?php
include WOO_PRO_FILTER_DIR.'admin/class-woo-filter-products-admin.php';
include WOO_PRO_FILTER_DIR.'public/class-woo-filter-products-public.php';
add_action( 'admin_enqueue_scripts', 'wooCodex_admin_enqueue_script' );
function wooCodex_admin_enqueue_script($hook){
	if( 'toplevel_page_woocommerce-filter-products' == $hook ){
		wp_enqueue_style( 'developer-admin', WOO_PRO_FILTER_URL.'/admin/css/design.css');
		wp_enqueue_script('jquery-ui-accordion');
		wp_enqueue_script('jquery-ui-sortable');	
		wp_enqueue_script( 'developer-admin', WOO_PRO_FILTER_URL.'/admin/js/developer.js', array(), null, true );
	}
}
if( !function_exists('WOO_getTaxonomies') ){
	function WOO_getTaxonomies(){
		$taxonomies = array();
        if (empty($taxonomies)) {
            $taxonomies = get_object_taxonomies('product', 'objects');
            unset($taxonomies['product_shipping_class']);
            unset($taxonomies['product_type']);
        }
        return $taxonomies;
	}
}
if( !function_exists('WOO_is_metakey_exist') ){
	function WOO_is_metakey_exist($meta_key){
		global $wpdb;
		$table_name = $wpdb->prefix . "woocodex_filter";
		$sql = "SELECT id FROM $table_name WHERE meta_key = '".$meta_key."' ";
		$results = $wpdb->get_results( $sql , OBJECT );
		$meta_id = ( !empty( $results[0]->id ) && is_numeric($results[0]->id) ? $results[0]->id : 'null' );
        return $meta_id;
	}
}
if( !function_exists('WOO_get_filter_info') ){
	function WOO_get_filter_info($meta_key){
		global $wpdb;
		$table_name = $wpdb->prefix . "woocodex_filter";
		$sql = "SELECT * FROM $table_name WHERE meta_key = '".$meta_key."' ";
		$results = $wpdb->get_results( $sql , OBJECT );
        return $results;
	}
}
if( !function_exists('WOO_get_filter_form') ){
	function WOO_get_filter_form(){
		global $wpdb;
		$table_name = $wpdb->prefix . "woocodex_filter";
		$sql = "SELECT * FROM $table_name WHERE is_enable = '1' order by meta_order ASC ";
		$results = $wpdb->get_results( $sql , OBJECT );
        return $results;
	}
}