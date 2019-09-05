<?php
add_shortcode( 'woocodex-filter-form' , 'WOO_get_filter_form_html' );
if( !function_exists('WOO_get_filter_form_html') ){
	function WOO_get_filter_form_html(){
		wp_enqueue_style( 'design-public');
		wp_enqueue_script( 'developer-public');
		$form = WOO_get_filter_form();
		if( !empty( $form ) ){
			$form_html = do_action( 'before_woocodex_filter_form' );
			$form_html .= '<div class="woocodex-filter-form-wrap">';
			$form_html .= '<form name="woocodex-filter-form" id="woocodex-filter-form" method="get">';
			foreach ($form as $form_value) {
				$meta_key = $form_value->meta_key;
				$name_prefix = 'filter_input_';
				$input_name = 'taxo_'.$meta_key;
				$input_type = $form_value->input_type;
				$hide_empty = $form_value->hide_empty;
				$terms = get_terms( $meta_key ,array( 'hide_empty' => $hide_empty ) );
				if( !empty( $terms ) ){
					$form_html .= '<div class="woocodex-filter-form-repeate">';
					$form_html .= '<h2>'.ucfirst( str_replace('_', ' ', $meta_key) ).'</h2>';
					if( $input_type == 'select' || $input_type == 'mselect' ){
						$inputName = ( $input_type == 'mselect' ? $input_name.'[]' : $input_name );
						$form_html .= do_action( 'before_select_woocodex_filter_form' );
						$selectClass = apply_filters( 'woocodex_filter_select_class', ''  );
						$form_html .= '<select name="'.$inputName.'" class="common-'.$input_type.'-class '.$selectClass.'" '.( $input_type == 'mselect' ? 'multiple' : '' ).'>';
					}
					foreach ($terms as $term) {
						if( $input_type == 'radio' ){
							if( file_exists( WOOWPCODEX_TEMPLATES_PATH.'/woocodex-templates/radio.php' ) ){	
							    $form_html .= include( WOOWPCODEX_TEMPLATES_PATH.'/woocodex-templates/radio.php' );
							}else{
								$form_html .= include( WOO_PRO_FILTER_DIR.'/template/inputs/radio.php' );
							}
						}
						if( $input_type == 'checkbox' ){
							if( file_exists( WOOWPCODEX_TEMPLATES_PATH.'/woocodex-templates/checkbox.php' ) ){	
							    $form_html .= include( WOOWPCODEX_TEMPLATES_PATH.'/woocodex-templates/checkbox.php' );
							}else{
								$form_html .= include( WOO_PRO_FILTER_DIR.'/template/inputs/checkbox.php' );
							}
						}
						if( $input_type == 'select' ){
							if( file_exists( WOOWPCODEX_TEMPLATES_PATH.'/woocodex-templates/select.php' ) ){	
							    $form_html .= include( WOOWPCODEX_TEMPLATES_PATH.'/woocodex-templates/select.php' );
							}else{
								$form_html .= include( WOO_PRO_FILTER_DIR.'/template/inputs/select.php' );
							}
						}
						if( $input_type == 'mselect' ){
							if( file_exists( WOOWPCODEX_TEMPLATES_PATH.'/woocodex-templates/mselect.php' ) ){	
							    $form_html .= include( WOOWPCODEX_TEMPLATES_PATH.'/woocodex-templates/mselect.php' );
							}else{
								$form_html .= include( WOO_PRO_FILTER_DIR.'/template/inputs/mselect.php' );
							}
						}
					}
					if( $input_type == 'select' || $input_type == 'mselect' ){
						$form_html .= '</select>';
						$form_html .= do_action( 'after_select_woocodex_filter_form' );
					}
					$form_html .= '</div>';
				}
			}
			$form_html .= wp_nonce_field( 'woocodex_filter_action', 'woocodex_filter_form_nonce' , true , false );
			$form_html .= '<input type="hidden" name="action" value="woocodex_filter_wpajax"/>';
			$form_html .= '<input type="submit" name="submit" value="Filter"/>';
			$form_html .= '</form>';
			$form_html .= '</div>';
			$form_html .= do_action( 'after_woocodex_filter_form' );
		}
		return $form_html;
	}
}