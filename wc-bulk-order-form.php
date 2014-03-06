<?php

/*
  Plugin Name: WooCommerce Bulk Order Form Free
  Plugin URI: http://wpovernight.com/
  Description: Adds the [wcbulkorder] shortcode which allows you to display bulk order forms on any page in your site
  Version: 1.0.1
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
		
		add_shortcode('wcbulkorder', array( &$this, 'wc_bulk_order_form' ) );
		
		// Functions to deal with the AJAX request - one for logged in users, the other for non-logged in users.
		add_action( 'wp_ajax_myprefix_autocompletesearch', array( &$this, 'myprefix_autocomplete_suggestions' ));
		add_action( 'wp_ajax_nopriv_myprefix_autocompletesearch', array( &$this, 'myprefix_autocomplete_suggestions' ));	
		add_action('wp_enqueue_scripts', array( &$this, 'load_jquery' ), 0 );
		add_action('wp_print_styles', array( &$this, 'load_styles' ), 0 );
	}
	
	/**
	 * Load additional classes and functions
	 */
	public function includes() {
		include_once( 'includes/wcbulkorder-settings.php' );
	}
	
	/**
	 * Load CSS
	 */
	public function load_styles() {
		wp_register_style('wcbulkorder-jquery-ui','http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
		wp_enqueue_style( 'wcbulkorder-jquery-ui' );
		
		$css = file_exists( get_stylesheet_directory() . '/wcbulkorderform.css' )
			? get_stylesheet_directory_uri() . '/wcbulkorderform.css'
			: plugins_url( '/css/wcbulkorderform.css', __FILE__ );
			
		wp_register_style( 'wcbulkorderform', $css, array(), '', 'all' );
		wp_enqueue_style( 'wcbulkorderform' );
	}
	
	/**
	 * Load JS
	 */   
	public function load_jquery() {
		wp_enqueue_script( 'wcbulkorder_acsearch', plugins_url( '/js/wcbulkorder_acsearch.js' , __FILE__ ), array('jquery','jquery-ui-autocomplete'),null,false);
		wp_localize_script( 'wcbulkorder_acsearch', 'WCBulkOrder', array('url' => admin_url( 'admin-ajax.php' ), 'search_products_nonce' => wp_create_nonce('wcbulkorder-search-products')));
	}
	
	/**
	 * Create Bulk Order Form Shortcode
	 * Source: http://wordpress.stackexchange.com/questions/53280/woocommerce-add-a-product-to-cart-programmatically-via-js-or-php
	*/ 
	public function wc_bulk_order_form ($atts){
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
		'price' => 'true',
		'price_label' => $this->options['price_field_title'],
		'product_label' => $this->options['product_field_title'],
		'quantity_label' => $this->options['quantity_field_title'],
		'add_rows' => 'false'
		), $atts ) );
		$i = 0;
		$html = '';

		if (($_POST['wcbulkorderid'][0] > 0) && ($_POST['wcbulkorderid'][1] > 0)) {
			echo "<p class='bulkorder-message'>Success! Your products have been added to your shopping cart.</p>";
		} else if($_POST['wcbulkorderid'][0] > 0){
			echo "<p class='bulkorder-message'>Success! Your product has been added to your shopping cart.</p>";
		} else if((isset($_POST['submit'])) && ($_POST['wcbulkorderid'][0] <= 0)) {
			echo "<p class='bulkorder-message fail'>Invalid submission - please try again.</p>";
		}

		?>
		<form action="" method="post" id="BulkOrderForm">
		<table class="wcbulkorderformtable">
			<tbody class="wcbulkorderformtbody">
				<tr>
					<th style="width: 60%"><?php echo $product_label ?></th>
					<th style="width: 20%"><?php echo $quantity_label ?></th>
					<?php if ($price == 'true'){ ?>
						<th style="width: 20%;text-align:center"><?php echo $price_label ?></th>
					<?php } ?>	
				</tr>
			<?php		
			while($i < $rows) {
				++$i;
			?>
				<tr class="wcbulkorderformtr">
					<td style="width: 60%">
						<input type="text" name="wcbulkorderproduct[]" class="wcbulkorderproduct" style="width: 100%" />
					</td>
					<td style="width: 20%">
						<input type="text" name="wcbulkorderquantity[]" class="wcbulkorderquantity" style="width: 100%" />
					</td>
					<?php if ($price == 'true'){ ?>
					<td style="width: 20%;text-align:center;color: green" class="wcbulkorderprice"></td>
					<?php } ?>	
					<input type="hidden" name="wcbulkorderid[]" class="wcbulkorderid" value="" />
				</tr>
				
			<?php } ?>
				
			</tbody>
		</table>	
		<table class="wcbulkorderformtable">
			<tbody>
				<?php if ($price == 'true'){ ?>
				<tr class="wcbulkorderformtr">
					<td style="width: 60%">
						
					</td>
					<td style="width: 20%">
						Total Price:
					</td>
					
					<td style="width: 20%;text-align:center;color: green" class="wcbulkorderpricetotal"></td>
					
				</tr>
				<?php } ?>	
				<tr>
					<td style="width: 60%"></td>
					<td style="width: 20%"><?php if (($add_rows == 'true') && ($price == 'true')){ ?>
						<button class="wcbulkordernewrowprice">Add Row</button>
						<?php }
						elseif (($add_rows == 'true') && ($price != 'true')) { ?>
						<button class="wcbulkordernewrow">Add Row</button>
						<?php } ?>
					</td>
					<td style="width: 20%"><input type="submit" value="Add To Cart" name="submit" /></td>
				</tr>
			</tbody>
		</table>
		</form>
		<?php
	}

	function myprefix_autocomplete_suggestions(){
		// Query for suggestions

		$term = $_REQUEST['term'];
		if (empty($term)) die();
		if ( is_numeric( $term ) ) {
		
			if (($this->options['search_by'] == 2) || ($this->options['search_by'] == 4)){
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
			if (($this->options['search_by'] == 1) || ($this->options['search_by'] == 4)){
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
			if($this->options['search_by'] == 1) {
				$products = array_unique(array_merge(get_posts( $products3 ) ));
			} elseif ($this->options['search_by'] == 2){
				$products = array_unique(array_merge( get_posts( $products1 ), get_posts( $products2 ) ));
			} else {
				$products = array_unique(array_merge( get_posts( $products1 ), get_posts( $products2 ), get_posts( $products3 ) ));
			}
		} else {
		
			if (($this->options['search_by'] == 1) || ($this->options['search_by'] == 4)){
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
			if (($this->options['search_by'] == 3) || ($this->options['search_by'] == 4)){
				$products2 = array(
					'post_type' 			=> array ('product', 'product_variation'),
					'post_status'         	=> 'publish',
					'posts_per_page'         => -1,
					's'                 	=> $term,
					'fields'                        => 'ids'
				);
			}
		
		}
			if($this->options['search_by'] == 1) {
				$products = array_unique(array_merge(get_posts( $products1 ) ));
			} elseif($this->options['search_by'] == 3) {
				$products = array_unique(array_merge(get_posts( $products2 ) ));
			} else {
				$products = array_unique(array_merge( get_posts( $products1 ), get_posts( $products2 ) ));
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
				$sku = $product->get_sku();//get_post_meta( $id, '_sku');
				$title = get_the_title($product->id);
			}
			
			elseif ( 'product_variation' == $post_type ) {
				//$product = get_product($prod);
				//$products = get_product($prod->ID);	
				$product = new WC_Product_Variation($prod);
				//print_r($product);
				$id = $product->variation_id;
				$price = number_format((float)$product->price, 2, '.', '');
				$sku = $product->get_sku();
				$title = get_the_title($id);
			}
			
			$symbol = get_woocommerce_currency_symbol();
			// Initialise suggestion array
			$suggestion = array();
			if (isset($this->options['search_format'])) {
				switch ($this->options['search_format']) {
					case 1:
						if (!empty($sku)) {
							$suggestion['label'] = html_entity_decode($sku.' - '.$title. ' - '.$symbol.$price);
						} else {
							$suggestion['label'] = html_entity_decode($title. ' - '.$symbol.$price);
						}
						break;
					case 2:
						if (!empty($sku)) {
							$suggestion['label'] = html_entity_decode($title. ' - '.$symbol.$price.' - '.$sku);
						} else {
							$suggestion['label'] = html_entity_decode($title. ' - '.$symbol.$price);
						}
						break;
					case 3:
						$suggestion['label'] = html_entity_decode($title .' - '.$symbol.$price);
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
			}

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