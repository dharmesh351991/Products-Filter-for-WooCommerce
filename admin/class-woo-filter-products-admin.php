<?php
add_action( 'admin_menu', 'register_woo_filter_menu_page' );
function register_woo_filter_menu_page(){
	add_menu_page( 'WooCommerce Filter','WooCommerce Filter' , 'manage_options', 'woocommerce-filter-products', 'menu_woocommerce_filter_products_cb',WOO_PRO_FILTER_URL.'admin/img/icons-filter.png',57);
	add_submenu_page( 'woocommerce-filter-products', 'General Settings', 'General Settings', 'manage_options', 'woocom-general-settings', 'submenu_woocommerce_filter_general_cb' );
}
function menu_woocommerce_filter_products_cb(){
	global $wpdb;
	$table_name = $wpdb->prefix . "woocodex_filter";
	if( $_REQUEST['submit'] == 'save' || isset( $_REQUEST['submit'] ) ){
		$currentArray = $_REQUEST['wooCodex_settings'];
		$Input_taxonomies = $currentArray['taxonomies'];
		$input_types = $currentArray['input_type'];
		$status = ( isset($currentArray['is_enable']) && is_array($currentArray['is_enable']) ? $currentArray['is_enable'] : array() );
		$hide_emptyArr = ( isset($currentArray['hide_empty']) && is_array($currentArray['hide_empty']) ? $currentArray['hide_empty'] : array() );
		if( !empty($Input_taxonomies) ){
			$order = 0;
			foreach ( $Input_taxonomies as $taxo_name => $taxo_order) {
				$is_ID = WOO_is_metakey_exist($taxo_name);
				$input_type = $input_types[$taxo_name];
				$is_enable = ( isset($status[$taxo_name]) && $status[$taxo_name] == 'on' ? '1' : '0' );
				$hide_empty = ( isset($hide_emptyArr[$taxo_name]) && $hide_emptyArr[$taxo_name] == 'on' ? '1' : '0' );
				if( $is_ID == 'null' ){
					$wpdb->insert( 
						$table_name,
						array(
							'meta_key' => $taxo_name,
							'input_type' => $input_type,
							'is_enable' => $is_enable,
							'hide_empty' => $hide_empty,
							'meta_order' => $order,
							'create_date' => date('Y-m-d H:i:s')
						)
			        );
				}else{
					$wpdb->update( 
						$table_name,
						array(
							'meta_key' => $taxo_name,
							'input_type' => $input_type,
							'is_enable' => $is_enable,
							'hide_empty' => $hide_empty,
							'meta_order' => $order,
							'modified_date' => date('Y-m-d H:i:s')
						),
						array( 'id' => $is_ID )
			        );
				}
		        $order++;
			}
		}
		$is_filter = $_REQUEST['is_filter'];
		$is_ajax = $_REQUEST['is_ajax'];
		$tax_relation = $_REQUEST['tax_relation'];
		$in_shop_page = $_REQUEST['in_shop_page'];
		$in_shop_cat_page = $_REQUEST['in_shop_cat_page'];
		update_option( WOOCOM_FILTER_OPTS.'is_filter', $is_filter );
		update_option( WOOCOM_FILTER_OPTS.'is_ajax', $is_ajax );
		update_option( WOOCOM_FILTER_OPTS.'tax_relation', $tax_relation );
		update_option( WOOCOM_FILTER_OPTS.'in_shop_page', $in_shop_page );
		update_option( WOOCOM_FILTER_OPTS.'in_shop_cat_page', $in_shop_cat_page );
	}
	$is_filter = get_option( WOOCOM_FILTER_OPTS.'is_filter' );
	$is_ajax = get_option( WOOCOM_FILTER_OPTS.'is_ajax' );
	$in_shop_page = get_option( WOOCOM_FILTER_OPTS.'in_shop_page' );
	$in_shop_cat_page = get_option( WOOCOM_FILTER_OPTS.'in_shop_cat_page' );
	?>
	<div class="wrap">
		<h2>General Settings</h2>
		<form method="post">
			<p>
				<span>Enable Filter</span><br/>
				<label class="switch">
					  <input type="checkbox" name="is_filter" value="true" <?php echo $is_filter == 'true' ? 'checked' : '' ?> ><span class="slider round"></span>
				</label>
			</p>
			<p>
				<span>Enable AJAX</span><br/>
				<label class="switch">
					  <input type="checkbox" name="is_ajax" value="true" <?php echo $is_ajax == 'true' ? 'checked' : '' ?> ><span class="slider round"></span>
				</label>
			</p>
			<p>
				<span>in Shop Page ?</span><br/>
				<label class="switch">
					  <input type="checkbox" name="in_shop_page" value="true" <?php echo $in_shop_page == 'true' ? 'checked' : '' ?> ><span class="slider round"></span>
				</label>
			</p>
			<p>
				<span>in Shop Category Page ?</span><br/>
				<label class="switch">
					  <input type="checkbox" name="in_shop_cat_page" value="true" <?php echo $in_shop_cat_page == 'true' ? 'checked' : '' ?> ><span class="slider round"></span>
				</label>
			</p>
			<p>
				<span>Tax Query Relation</span><br/>
					  <input type="radio" name="tax_relation" value="or" <?php echo $tax_relation == 'or' ? 'checked' : '' ?> ><span>OR</span>
					  <input type="radio" name="tax_relation" value="and" <?php echo $tax_relation == 'and' ? 'checked' : '' ?> ><span>AND</span>
			</p>
			<?php
				$taxonomies = WOO_getTaxonomies();
				if( !empty( $taxonomies ) ){
					$taxonomies_output = '<div id="accordion">';
					$taxonomies_output_arr = [];
					$order_iter = 0;
					foreach ($taxonomies as $taxo) {
						$resultInfo = WOO_get_filter_info($taxo->name);
						$is_enable  = $resultInfo[0]->is_enable;
						$input_type = $resultInfo[0]->input_type;
						$meta_order = $resultInfo[0]->meta_order;
						$hide_empty = $resultInfo[0]->hide_empty;
						$meta_order = ( isset($meta_order) ? $meta_order : $order_iter );

						$taxonomies_output_arr[$meta_order] = '<div class="group">
						<h3><span class="sort-icon"><img src="'.WOO_PRO_FILTER_URL.'/admin/img/move.png" /></span>
						'.$taxo->label.'
						</h3>
					    	<div>
					    	<table class="wp-list-table widefat striped posts">
							<tr>
								<th>Enable</th>
								<td><input name="wooCodex_settings[is_enable]['.$taxo->name.']" type="checkbox" '.( $is_enable == "1" ? "checked" : "" ).' />
								<input name="wooCodex_settings[taxonomies]['.$taxo->name.'][]" type="hidden"/>
								</td>
							</tr>
							<tr>
								<th>Hide Empty</th>
								<td><input name="wooCodex_settings[hide_empty]['.$taxo->name.']" type="checkbox" '.( $hide_empty == "1" ? "checked" : "" ).' />
								</td>
							</tr>
							<tr>
								<th>Type</th>
								<td>
									<select name="wooCodex_settings[input_type]['.$taxo->name.']">
										<option value="radio" '.( $input_type == "radio" ? 'selected="selected"' : ""  ).'>Radio</option>
	                                    <option '.( $input_type == "checkbox" ? "selected" : ""  ).' value="checkbox">Checkbox</option>
	                                    <option '.( $input_type == "select" ? "selected" : ""  ).' value="select" >Drop-down</option>
	                                    <option '.( $input_type == "mselect" ? "selected" : ""  ).' value="mselect">Multi drop-down</option>
									</select>
								</td>
							</tr>
						</table>
					    	</div>
					  	</div>';
					  	$order_iter++;
					}
					ksort($taxonomies_output_arr);
					$taxonomies_output .= implode(' ', $taxonomies_output_arr );
					$taxonomies_output .= '</div>';
				}
				echo $taxonomies_output;
			?>
			<p>
				<input type="submit" name="submit" class="button button-primary button-large" value="Save">
			</p>
		</form>
	</div>
	<?php
}
function submenu_woocommerce_filter_general_cb(){
	
}