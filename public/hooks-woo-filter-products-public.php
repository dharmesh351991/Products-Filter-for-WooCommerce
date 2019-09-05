<?php
/**
 * @author : WPCodex team
 * @package : Hooks for filter & action for code manipulation  
 */
if( class_exists('WPCodexHooks') ){
	new WPCodexHooks();
}
class WPCodexHooks
{
	
	function __construct()
	{
		add_action( 'woocommerce_before_shop_loop', array($this,'show_filter_form_on_shop_category') );
	}
	function show_filter_form_on_shop_category(){
		$is_filter = get_option( WOOCOM_FILTER_OPTS.'is_filter' );
		if( ( is_shop() || is_product_category() ) && $is_filter == true ){
			echo do_shortcode('[woocodex-filter-form]');
		}
	}
}