<?php
add_action( 'wp_enqueue_scripts', 'wooCodex_public_enqueue_script' );
function wooCodex_public_enqueue_script(){
	wp_register_style( 'design-public', WOO_PRO_FILTER_URL.'/public/css/design.css', '' , null);
	wp_register_script( 'developer-public', WOO_PRO_FILTER_URL.'/public/js/developer.js', array(), null, true );
	$options = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
	wp_localize_script( 'developer-public', 'object', $options );
}
add_action( 'wp_ajax_woocodex_filter_wpajax', 'woocodex_filter_wpajax_cb' );
add_action( 'wp_nopriv_ajax_woocodex_filter_wpajax', 'woocodex_filter_wpajax_cb' );
function woocodex_filter_wpajax_cb(){
	$response = array();
	$tax_relation = get_option( WOOCOM_FILTER_OPTS.'tax_relation' );
	if ( ! isset( $_POST['woocodex_filter_form_nonce'] ) 
	    || ! wp_verify_nonce( $_POST['woocodex_filter_form_nonce'], 'woocodex_filter_action' ) 
	) {
	   $response['status'] = false;
	   $response['result'] = 'Sorry, your nonce did not verify.';
	} else {
	   $inputsArr = $_REQUEST;
	   $tax_query= array();
	   if( !empty($inputsArr) && is_array($inputsArr) ){
	   	foreach ( $inputsArr as $taxonomy_key=>$taxonomy_value ) {
	   		$terms = ( is_array($taxonomy_value) && !empty($taxonomy_value) ? $taxonomy_value : array($taxonomy_value)  );
	   		$is_taxo = explode('_', $taxonomy_key );
	   		if( $is_taxo[0] == 'taxo' ){
	   			$taxonomy_slug = str_replace('taxo_', '', $taxonomy_key);
	   			$taxonomy_exist = taxonomy_exists( $taxonomy_slug );
	   			if( $taxonomy_exist ){
	   				$tax_query[] = array(
	   					'taxonomy' => $taxonomy_slug,
			            'field'    => 'slug',
			            'terms'    => $terms
	   				);
	   			}
	   		}
	   	}
	   }
	   $args['post_type'] = 'product';
	   $args['tax_query'] = $tax_query;
	   $args['tax_query']['relation'] = strtoupper($tax_relation);
	   $products = new WP_Query( $args );
	   ob_start();
	   while ($products->have_posts()) : $products->the_post();
            wc_get_template_part('content', 'product');
       endwhile;
       $data = ob_get_contents();
       ob_end_clean();
       $response['status'] = true;
       $response['result'] = $data;
	}
	echo json_encode( $response );
	exit;
}