<?php

/*
  Plugin Name: WooCommerce Bulk Order Form
  Plugin URI: http://wpovernight.com/
  Description: Adds the [wcbulkorder] shortcode which allows you to display bulk order forms on any page in your site
  Version: 1.1.2
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
		add_action( 'wp_print_styles', array( &$this, 'load_styles' ), 0 );
        add_action( 'wp', array($this,'process_bulk_order_form') );
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
	 * Load translations.
	 */
	public function languages() {
		load_plugin_textdomain( 'wcbulkorderform', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Load JS
	 */   
	static function register_script() {
		$options = get_option('wcbulkorderform');
		wp_register_script('wcbulkorder_acsearch', plugins_url( '/js/wcbulkorder_acsearch.js' , __FILE__ ), array('jquery','jquery-ui-autocomplete'),null,true);
		$display_images = isset($options['display_images']) ? $options['display_images'] : '';
		$noproductsfound = __( 'No Products Were Found', 'wcbulkorderform' );
		$variation_noproductsfound = __( 'No Variations', 'wcbulkorderform' );
		$selectaproduct = __( 'Please Select a Product', 'wcbulkorderform' );
		$enterquantity = __( 'Enter Quantity', 'wcbulkorderform' );
		wp_localize_script( 'wcbulkorder_acsearch', 'WCBulkOrder', array('url' => admin_url( 'admin-ajax.php' ), 'search_products_nonce' => wp_create_nonce('wcbulkorder-search-products'), 'display_images' => $display_images, 'noproductsfound' => $noproductsfound, 'selectaproduct' => $selectaproduct, 'enterquantity' => $enterquantity, 'variation_noproductsfound' => $variation_noproductsfound,'variation_noproductsfound' => $variation_noproductsfound));
	}

	static function print_script() {
		if ( ! self::$add_script )
			return;

		wp_print_scripts('wcbulkorder_acsearch');
		wp_enqueue_style( 'wcbulkorder-jquery-ui' );
		wp_enqueue_style( 'wcbulkorderform' );
	}

	function process_bulk_order_form() {
        if(isset($_POST['wcbulkorderproduct'])) {
            global $woocommerce;

			$prod_name = $_POST['wcbulkorderproduct'];
			$prod_quantity = $_POST['wcbulkorderquantity'];
			$prod_id = $_POST['wcbulkorderid'];
			$i = 0;
			foreach($prod_id as $key => $value) {
				$variation_id = '';
                $product_id = $value;
				if ( 'product_variation' == get_post_type( $product_id ) ) {
                    $variation_id = $product_id;
                    $product_id = wp_get_post_parent_id( $variation_id );
            	}
                $woocommerce->cart->add_to_cart($product_id,$prod_quantity[$key],$variation_id,'',array());
			}
			
		}
    }
	
	/**
	 * Create Bulk Order Form Shortcode
	 * Source: http://wordpress.stackexchange.com/questions/53280/woocommerce-add-a-product-to-cart-programmatically-via-js-or-php
	*/ 
	public function wc_bulk_order_form ($atts){
		global $woocommerce;
		self::$add_script = true;

		extract( shortcode_atts( array(
		'rows' => $this->options['bulkorder_row_number'],
		'price' => $this->options['display_price'],
		'price_label' => $this->options['price_field_title'],
		'product_label' => $this->options['product_field_title'],
		'quantity_label' => $this->options['quantity_field_title'],
		'add_rows' => 'false'
		), $atts ) );
		$i = 0;
		$html = '';
		$items = '';
		$cart_url = $woocommerce->cart->get_cart_url();
		
		if (isset($_POST['wcbulkorderid'])) {
			if (($_POST['wcbulkorderid'][0] > 0) && ($_POST['wcbulkorderid'][1] > 0)){
				$items = 2;
			} else if($_POST['wcbulkorderid'][0] > 0){
				$items = 1;
			} else if((isset($_POST['submit'])) && ($_POST['wcbulkorderid'][0] <= 0)){
				$items = 0;
			}
			switch($items){
				case 0:
					$message = '<div class="woocommerce-message" style="border-color: red">'.__("Looks like there was an error. Please try again.", "wcbulkorderform").'</div>';
					break;
				case 1:
					$message = '<div class="woocommerce-message"><a class="button wc-forward" href="'.$cart_url.'">View Cart</a>'.__("Your product was successfully added to your cart.", "wcbulkorderform").'</div>';
					break;
				case 2:
					$message = '<div class="woocommerce-message"><a class="button wc-forward" href="'.$cart_url.'">'.__("View Cart</a> Your products were successfully added to your cart.", "wcbulkorderform").'</div>';
					break;
			}
			$message = apply_filters('wc_bulk_order_form_message', $message, $items, $cart_url);
			echo $message;
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
						<i class="bulkorder_spinner"></i>
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
		$max_items = isset($this->options['max_items']) ? $this->options['max_items'] : '-1';
		if (empty($term)) die();
		if ( is_numeric( $term ) ) {
		
			if (($search_by == 2) || ($search_by == 4)){
				$products1 = array(
					'post_type'                        => array ('product', 'product_variation'),
					'post_status'                 => 'publish',
					'posts_per_page'         => $max_items,
					'post__in'                         => array(0, $term),
					'fields'                        => 'ids'
				);
				
				$products2 = array(
					'post_type'                        => array ('product', 'product_variation'),
					'post_status'                 => 'publish',
					'posts_per_page'         => $max_items,
					'post_parent'                 => $term,
					'fields'                        => 'ids'
				);
			}
			if (($search_by == 3) || ($search_by == 4)){
				$products4 = array(
					'post_type' 			=> array ('product', 'product_variation'),
					'post_status'         	=> 'publish',
					'posts_per_page'         => $max_items,
					's'                 	=> $term,
					'fields'                        => 'ids'
				);
			}
			if (($search_by == 1) || ($search_by == 4)){
				$products3 = array(
					'post_type'                        => array ('product', 'product_variation'),
					'post_status'                 => 'publish',
					'posts_per_page'         => $max_items,
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
			} elseif ($search_by == 3){
				$products = array_unique(array_merge(get_posts( $products4 ) ));
			} else {
				$products = array_unique(array_merge( get_posts( $products1 ), get_posts( $products2 ), get_posts( $products3 ), get_posts( $products4 ) ));
			}
		} else {
		
			if (($search_by == 1) || ($search_by == 4)){
				$products1 = array(
						'post_type'                        => array ('product', 'product_variation'),
						'post_status'                 => 'publish',
						'posts_per_page'         => $max_items,
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
					'posts_per_page'         => $max_items,
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
		
		
		foreach ($products as $prod):	

			$post_type = get_post_type($prod);

			if ( 'product' == $post_type ) {
				$product = get_product($prod);
				$id = $product->id;
				$price = number_format((float)$product->get_price(), 2, '.', '');
				$sku = $product->get_sku();
				$title = get_the_title($product->id);
				$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');
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
                    $attr_name_clean = str_replace("attribute_pa_","",$attr_name_clean);
					$title .= " - " . $attr_name_clean . ": " . $attr_value;
					$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');
                }
			}
			
			$symbol = get_woocommerce_currency_symbol();
			$symbol = html_entity_decode($symbol, ENT_COMPAT, 'UTF-8');
			// Initialise suggestion array
			$suggestion = array();
			$switch_data = isset($this->options['search_format']) ? $this->options['search_format'] : '1';
			$price = apply_filters('wc_bulk_order_form_price' , $price, $product);
				switch ($switch_data) {
					case 1:
						if (!empty($sku)) {
							$label = $sku.' - '.$title. ' - '.$symbol.$price;
						} else {
							$label = $title. ' - '.$symbol.$price;
						}
						break;
					case 2:
						if (!empty($sku)) {
							$label = $title. ' - '.$symbol.$price.' - '.$sku;
						} else {
							$label = $title. ' - '.$symbol.$price;
						}
						break;
					case 3:
						$label = $title .' - '.$symbol.$price;
						break;
					case 4:
						if (!empty($sku)) {
							$label = $title. ' - '.$sku;
						} else {
							$label = $title;
						}
						break;
					case 5:
						$label = $title;
						break;
				}
			$suggestion['label'] = apply_filters('wc_bulk_order_form_label', $label, $price, $title, $sku, $symbol);
			$suggestion['price'] = $price;
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