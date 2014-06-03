<?php

/*
  Plugin Name: WooCommerce Bulk Order Form
  Plugin URI: http://wpovernight.com/
  Description: Adds the [wcbulkorder] shortcode which allows you to display bulk order forms on any page in your site
  Version: 1.0.7
  Author: Jeremiah Prummer
  Author URI: http://wpovernight.com/
  License: GPL2
 */
/*  Copyright 2014 Jeremiah Prummer (email : jeremiah@wpovernight.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
 */
?>
<?php

class WCBulkOrderForm {

	private static $add_script;
	/**
	 * Construct.
	 */
	public function __construct() {
		
		$this->includes();
		$this->options = get_option('wcbulkorderform');
		if(empty($this->options)) {
			register_activation_hook( __FILE__, array( 'WCBulkOrderForm_Settings', 'default_settings' ) );
			$this->options = get_option('wcbulkorderform');
		}
		$this->settings = new WCBulkOrderForm_Settings();
		
		add_action( 'plugins_loaded', array( &$this, 'languages' ), 0 ); // or use init?
		add_shortcode('wcbulkorder', array( &$this, 'wc_bulk_order_form' ) );
		
		// Functions to deal with the AJAX request - one for logged in users, the other for non-logged in users.
		
		add_action( 'wp_ajax_myprefix_autocompletesearch', array( &$this, 'myprefix_autocomplete_suggestions' ));
		add_action( 'wp_ajax_nopriv_myprefix_autocompletesearch', array( &$this, 'myprefix_autocomplete_suggestions' ));	
		add_action('wp_print_styles', array( &$this, 'load_styles' ), 0 );

		add_action('init', array( &$this, 'register_script'));
		add_action('wp_footer', array( &$this, 'print_script'));
		
	}
	
	/**
	 * Load additional classes and functions
	 */
	public function includes() {
		include_once( 'includes/wcbulkorder-settings.php' );
		include_once( 'includes/wc-bulk-order-form-compatibility.php' );
	}
	
	/**
	 * Load CSS
	 */
	public function load_styles() {
		
		if (empty($this->options['no_load_css'])) {
			$autocomplete = file_exists( get_stylesheet_directory() . '/jquery-ui.css' )
			? get_stylesheet_directory_uri() . '/jquery-ui.css'
			: plugins_url( '/css/jquery-ui.css', __FILE__ );

			wp_register_style( 'wcbulkorder-jquery-ui', $autocomplete, array(), '', 'all' );
			//wp_enqueue_style( 'wcbulkorder-jquery-ui' );
		}
		
		$css = file_exists( get_stylesheet_directory() . '/wcbulkorderform.css' )
			? get_stylesheet_directory_uri() . '/wcbulkorderform.css'
			: plugins_url( '/css/wcbulkorderform.css', __FILE__ );
			
		wp_register_style( 'wcbulkorderform', $css, array(), '', 'all' );
		//wp_enqueue_style( 'wcbulkorderform' );
	}
	
	/**
	 * Load JS
	 */   
	public function load_jquery() {
		wp_enqueue_script( 'wcbulkorder_acsearch', plugins_url( '/js/wcbulkorder_acsearch.js' , __FILE__ ), array('jquery','jquery-ui-autocomplete'),null,true);
		wp_localize_script( 'wcbulkorder_acsearch', 'WCBulkOrder', array('url' => admin_url( 'admin-ajax.php' ), 'search_products_nonce' => wp_create_nonce('wcbulkorder-search-products')));
	}


	/**
	 * Load translations.
	 */
	public function languages() {
		load_plugin_textdomain( 'wcbulkorderform', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Load JS
	 */   
	static function register_script() {
		wp_register_script('wcbulkorder_acsearch', plugins_url( '/js/wcbulkorder_acsearch.js' , __FILE__ ), array('jquery','jquery-ui-autocomplete'),null,true);
		wp_localize_script( 'wcbulkorder_acsearch', 'WCBulkOrder', array('url' => admin_url( 'admin-ajax.php' ), 'search_products_nonce' => wp_create_nonce('wcbulkorder-search-products')));
	}

	static function print_script() {
		if ( ! self::$add_script )
			return;

		wp_print_scripts('wcbulkorder_acsearch');
		wp_enqueue_style( 'wcbulkorderform' );
		wp_enqueue_style( 'wcbulkorder-jquery-ui' );
	}
	
	/**
	 * Create Bulk Order Form Shortcode
	 * Source: http://wordpress.stackexchange.com/questions/53280/woocommerce-add-a-product-to-cart-programmatically-via-js-or-php
	*/ 
	public function wc_bulk_order_form ($atts){

		$html = '';
		self::$add_script = true;
		$prod_name = '';
		if(isset($_POST['submit'])) {
			$prod_name = $_POST['wcbulkorderproduct'];
			$prod_quantity = $_POST['wcbulkorderquantity'];
			$prod_id = $_POST['wcbulkorderid'];
			$i = 0;
			foreach($prod_id as $key => $value) {
				$ancestors = '';
				$ancestors = get_post_ancestors( $prod_id );
				
				if (is_array($ancestors)) {
					global $woocommerce;
					$woocommerce->cart->add_to_cart($ancestors[0], $quantity = $_POST['wcbulkorderquantity'][$i], $variation_id = $_POST['wcbulkorderid'][$i]);
					++$i;
				}
				else {
					global $woocommerce;
					$woocommerce->cart->add_to_cart($_POST['wcbulkorderid'][$i], $quantity = $_POST['wcbulkorderquantity'][$i]);
					++$i;
				}
			}
			
		}
		extract( shortcode_atts( array(
		'rows' => $this->options['bulkorder_row_number'],
		'price' => $this->options['display_price'],
		'price_label' => $this->options['price_field_title'],
		'product_label' => $this->options['product_field_title'],
		'quantity_label' => $this->options['quantity_field_title'],
		'add_rows' => 'false'
		), $atts ) );
		$i = 0;

		if (isset($_POST['wcbulkorderid'])) {
			if (($_POST['wcbulkorderid'][0] > 0) && ($_POST['wcbulkorderid'][1] > 0)) {
				echo '<p class="bulkorder-message">'.__( 'Success! Your products have been added to your shopping cart.', 'wcbulkorderform' ).'</p>';
			} else if($_POST['wcbulkorderid'][0] > 0){
				echo '<p class="bulkorder-message">'.__( 'Success! Your product has been added to your shopping cart.', 'wcbulkorderform' ).'</p>';
			} else if((isset($_POST['submit'])) && ($_POST['wcbulkorderid'][0] <= 0)) {
				echo '<p class="bulkorder-message fail">'.__( 'Invalid submission - please try again.', 'wcbulkorderform' ).'</p>';
			}
		}

		$html = <<<HTML

		<form action="" method="post" id="BulkOrderForm">
		<table class="wcbulkorderformtable">
			<tbody class="wcbulkorderformtbody">
				<tr>
					<th style="width: 60%">$product_label</th>
					<th style="width: 20%">$quantity_label</th>
HTML;
					if ($price == 'true'){
						$html .= '<th style="width: 20%;text-align:center">'.$price_label.'</th>';
					}
				$html .= '</tr>';

			while($i < $rows) {
				++$i;
				$html .= <<<HTML2
				<tr class="wcbulkorderformtr">
					<td style="width: 60%">
						<input type="text" name="wcbulkorderproduct[]" class="wcbulkorderproduct" style="width: 100%" />
					</td>
					<td style="width: 20%">
						<input type="text" name="wcbulkorderquantity[]" class="wcbulkorderquantity" style="width: 100%" />
					</td>
HTML2;
					if($price == 'true'){
					$html .= '<td style="width: 20%;text-align:center;color: green" class="wcbulkorderprice"></td>';
					}
					$html .= <<<HTML7
					<input type="hidden" name="wcbulkorderid[]" class="wcbulkorderid" value="" />
				</tr>
HTML7;
			}
		$html .= <<<HTML3
			</tbody>
		</table>	
		<table class="wcbulkorderformtable">
			<tbody>
HTML3;
				if ($price == 'true'){
				$html .= <<<HTML4
				<tr class="wcbulkorderformtr">
					<td style="width: 60%">
						
					</td>
					<td style="width: 20%">
HTML4;
						$html .= __( 'Total Price:' , 'wcbulkorderform' );
					$html .= <<<HTML6
					</td>
					
					<td style="width: 20%;text-align:center;color: green" class="wcbulkorderpricetotal"></td>
					
				</tr>
HTML6;
				}
				$html .= '<tr>';
					$html .= '<td style="width: 60%"></td>';
					$html .='<td style="width: 20%">';
						if (($add_rows == 'true') && ($price == 'true')){
						$html .='<button class="wcbulkordernewrowprice">'.__( 'Add Row' , 'wcbulkorderform' ).'</button>';
						}
						elseif (($add_rows == 'true') && ($price != 'true')) {
						$html .='<button class="wcbulkordernewrow">'.__( 'Add Row' , 'wcbulkorderform' ).'</button>';
						}
					
					$html .='</td>';
					$html .='<td style="width: 20%"><input type="submit" value="'.__( 'Add To Cart' , 'wcbulkorderform' ).'" name="submit" /></td>';
					$html .= <<<HTML5
				</tr>
			</tbody>
		</table>
		</form>
HTML5;
		return $html;
		
	}

	function myprefix_autocomplete_suggestions(){
		// Query for suggestions

		$term = '';
		$term = $_REQUEST['term'];
		$search_by = isset($this->options['search_by']) ? $this->options['search_by'] : '4';
		if (empty($term)) die();
		if ( is_numeric( $term ) ) {
		
			if (($search_by == 2) || ($search_by == 4)){
				$products1 = array(
					'post_type'                        => array ('product', 'product_variation'),
					'post_status'                 => 'publish',
					'posts_per_page'         => -1,
					'post__in'                         => array(0, $term),
					'fields'                        => 'ids'
				);
				
				$products2 = array(
					'post_type'                        => array ('product', 'product_variation'),
					'post_status'                 => 'publish',
					'posts_per_page'         => -1,
					'post_parent'                 => $term,
					'fields'                        => 'ids'
				);
			}
			if (($search_by == 1) || ($search_by == 4)){
				$products3 = array(
					'post_type'                        => array ('product', 'product_variation'),
					'post_status'                 => 'publish',
					'posts_per_page'         => -1,
					'meta_query'                 => array(
							array(
							'key'         => '_sku',
							'value' => $_REQUEST['term'],
							'compare' => 'LIKE'
							)
					),
					'fields'                        => 'ids'
				);
			}
			if($search_by == 1) {
				$products = array_unique(array_merge(get_posts( $products3 ) ));
			} elseif ($search_by == 2){
				$products = array_unique(array_merge( get_posts( $products1 ), get_posts( $products2 ) ));
			} else {
				$products = array_unique(array_merge( get_posts( $products1 ), get_posts( $products2 ), get_posts( $products3 ) ));
			}
		} else {
		
			if (($search_by == 1) || ($search_by == 4)){
				$products1 = array(
						'post_type'                        => array ('product', 'product_variation'),
						'post_status'                 => 'publish',
						'posts_per_page'         => -1,
						'meta_query'                 => array(
								array(
								'key'         => '_sku',
								'value' => $_REQUEST['term'],
								'compare' => 'LIKE'
								)
						),
						'fields'                        => 'ids'
					);
			}
			if (($search_by == 3) || ($search_by == 4)){
				$products2 = array(
					'post_type' 			=> array ('product', 'product_variation'),
					'post_status'         	=> 'publish',
					'posts_per_page'         => -1,
					's'                 	=> $term,
					'fields'                        => 'ids'
				);
			}
		
			if($search_by == 1) {
				$products = array_unique(array_merge(get_posts( $products1 ) ));
			} elseif($search_by == 3) {
				$products = array_unique(array_merge(get_posts( $products2 ) ));
			} else {
				$products = array_unique(array_merge( get_posts( $products1 ), get_posts( $products2 ) ));
			}
		}
			
		
		// JSON encode and echo
		// Initialise suggestions array
		
		global $post, $woocommerce, $product;
		
		
		foreach ($products as $prod): setup_postdata($prod);	

			$post_type = get_post_type($prod);

			if ( 'product' == $post_type ) {
				$product = get_product($prod);
				$id = $product->id;
				$price = number_format((float)$product->get_price(), 2, '.', '');
				$sku = $product->get_sku();
				$title = get_the_title($product->id);
			}
			
			elseif ( 'product_variation' == $post_type ) {
				$product = new WC_Product_Variation($prod);
				$parent = get_product($prod);
				$id = $product->variation_id;
				$price = number_format((float)$product->price, 2, '.', '');
				$sku = $product->get_sku();
				$title = $product->get_title();
				$attributes = $product->get_variation_attributes();
				foreach ( $attributes as $name => $value) {
					$attr_name = $name;                       
					$attr_value = $value;
					$attr_value = str_replace('-', ' ', $value);
					if (strstr($attr_name, 'pa_')){
                        $atts = get_the_terms($parent->id ,$attr_name);
                   		$attr_name_clean = WC_Bulk_Order_Form_Compatibility::wc_attribute_label($attr_name);
                    }
                    else {
                        $np = explode("-",str_replace("attribute_","",$attr_name));
                        $attr_name_clean = ucwords(implode(" ",$np));
                    }
					$title .= " - " . $attr_name_clean . ": " . $attr_value;
                }
			}
			
			$symbol = get_woocommerce_currency_symbol();
			// Initialise suggestion array
			$suggestion = array();
			$switch_data = isset($this->options['search_format']) ? $this->options['search_format'] : '1';
				switch ($switch_data) {
					case 1:
						if (!empty($sku)) {
							$suggestion['label'] = html_entity_decode($sku.' - '.$title. ' - '.$symbol.apply_filters('wc_bulk_order_form_price' , $price, $product));
						} else {
							$suggestion['label'] = html_entity_decode($title. ' - '.$symbol.apply_filters('wc_bulk_order_form_price' , $price, $product));
						}
						break;
					case 2:
						if (!empty($sku)) {
							$suggestion['label'] = html_entity_decode($title. ' - '.$symbol.apply_filters('wc_bulk_order_form_price' , $price, $product).' - '.$sku);
						} else {
							$suggestion['label'] = html_entity_decode($title. ' - '.$symbol.apply_filters('wc_bulk_order_form_price' , $price, $product));
						}
						break;
					case 3:
						$suggestion['label'] = html_entity_decode($title .' - '.$symbol.apply_filters('wc_bulk_order_form_price' , $price, $product));
						break;
					case 4:
						if (!empty($sku)) {
							$suggestion['label'] = html_entity_decode($title. ' - '.$sku);
						} else {
							$suggestion['label'] = html_entity_decode($title);
						}
						break;
					case 5:
						$suggestion['label'] = html_entity_decode($title);
						break;
				}

			$suggestion['price'] = apply_filters('wc_bulk_order_form_price' , $price, $product);
			$suggestion['symbol'] = $symbol;
			$suggestion['id'] = $id;
			if (!empty($variation_id)) {
				$suggestion['variation_id'] = $variation_id;
			}

			// Add suggestion to suggestions array
			$suggestions[]= $suggestion;
		endforeach;

		// JSON encode and echo
		$response = $_GET["callback"] . "(" . json_encode($suggestions) . ")";
		//print_r($response);
		echo $response;

		// Don't forget to exit!
		exit;
	}
	
}

$WCBulkOrderForm = new WCBulkOrderForm();