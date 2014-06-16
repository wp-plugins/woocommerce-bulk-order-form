<?php
class WCBulkOrderForm_Settings {
	
	public function __construct() {
		add_action( 'admin_init', array( &$this, 'init_settings' ) ); // Registers settings
		add_action( 'admin_menu', array( &$this, 'wcbulkorderform_add_page' ) );
	}
	/**
	 * User settings.
	 */
	public function init_settings() {
		//wp_register_style( 'wcbulkorderform-admin', plugins_url( 'css/wcbulkorderform-icons-pro.css', dirname(__FILE__) ), array(), '', 'all' );

		$option = 'wcbulkorderform';
	
		// Create option in wp_options.
		if ( false == get_option( $option ) ) {
			add_option( $option );
		}
	
		// Section.
		add_settings_section(
			'plugin_settings',
			__( 'Plugin Settings', 'wcbulkorderform' ),
			array( &$this, 'section_options_callback' ),
			$option
		);
		
		add_settings_field(
			'search_by',
			__( 'When searching for products search by:', 'wcbulkorderform' ),
			array( &$this, 'radio_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'search_by',
				'options' 		=> array(
					'1'			=> __( 'SKU' , 'wcbulkorderform' ),
					'2'			=> __( 'ID' , 'wcbulkorderform' ),
					'3'			=> __( 'Title' , 'wcbulkorderform' ),
					'4'			=> __( 'All' , 'wcbulkorderform' )
				),
				'disabled'		=> true,
				'default'		=> '4'
			)
		);
		
		add_settings_field(
			'search_format',
			__( 'Choose your product search results format', 'wcbulkorderform' ),
			array( &$this, 'radio_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'search_format',
				'options' 		=> array(
					'1'			=> __( 'Title - Price' , 'wcbulkorderform' ),
					'2'			=> __( 'Title - Price - SKU' , 'wcbulkorderform' ),
					'3'			=> __( 'SKU - Title - Price' , 'wcbulkorderform' ),
					'4'			=> __( 'Title - SKU' , 'wcbulkorderform' ),
					'5'			=> __( 'Title' , 'wcbulkorderform' )
				),
				'disabled'		=> true,
				'default'		=> '1'
			)
		);
		
		add_settings_field(
			'new_row_button',
			__( 'Display "Add New Row" Button?', 'wcbulkorderform' ),
			array( &$this, 'radio_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'new_row_button',
				'options' 		=> array(
					'true'		=> __( 'Yes' , 'wcbulkorderform' ),
					'false'		=> __( 'No' , 'wcbulkorderform' )
				),
				'disabled'		=> true,
				'default'		=> 'false'
			)
		);

		add_settings_field(
			'display_images',
			__( 'Display product images in autocomplete search?', 'wcbulkorderform' ),
			array( &$this, 'radio_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'display_images',
				'options' 		=> array(
					'true'		=> __( 'Yes' , 'wcbulkorderform' ),
					'false'		=> __( 'No' , 'wcbulkorderform' )
				),
				'disabled'		=> true,
				'default'		=> 'false'
			)
		);
		
		// Section.
		add_settings_section(
			'advanced_settings',
			__( 'Default Shortcode Options', 'wcbulkorderform' ),
			array( &$this, 'section_options_callback' ),
			$option
		);
		
		add_settings_field(
			'bulkorder_row_number',
			__( 'Number of rows to display on the bulk order form', 'wcbulkorderform' ),
			array( &$this, 'text_element_callback' ),
			$option,
			'advanced_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'bulkorder_row_number'
			)
		);

		add_settings_field(
			'max_items',
			__( 'Maximum Items to Display in a Search', 'wcbulkorderform' ),
			array( &$this, 'text_element_callback' ),
			$option,
			'advanced_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'max_items'
			)
		);
		
		add_settings_field(
			'display_price',
			__( 'Display price on bulk order form?', 'wcbulkorderform' ),
			array( &$this, 'radio_element_callback' ),
			$option,
			'advanced_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'display_price',
				'options' 		=> array(
					'true'			=> __( 'Yes' , 'wcbulkorderform' ),
					'false'			=> __( 'No' , 'wcbulkorderform' )
				),
			)
		);
		
		add_settings_field(
			'product_field_title',
			__( 'Title for product fields', 'wcbulkorderform' ),
			array( &$this, 'text_element_callback' ),
			$option,
			'advanced_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'product_field_title'
			)
		);
		
		add_settings_field(
			'quantity_field_title',
			__( 'Title for quantity fields', 'wcbulkorderform' ),
			array( &$this, 'text_element_callback' ),
			$option,
			'advanced_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'quantity_field_title'
			)
		);
		
		add_settings_field(
			'price_field_title',
			__( 'Title for price fields', 'wcbulkorderform' ),
			array( &$this, 'text_element_callback' ),
			$option,
			'advanced_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'price_field_title'
			)
		);

		add_settings_field(
			'no_load_css',
			__( "Don't load jquery ui styles. (Don't check this unless you know your site is loading jquery ui styles from another source)", 'wcbulkorderform' ),
			array( &$this, 'checkbox_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'no_load_css',
			)
		);
		
		// Register settings.
		register_setting( $option, $option, array( &$this, 'wcbulkorderform_options_validate' ) );

		// Register defaults if settings empty (might not work in case there's only checkboxes and they're all disabled)
		$option_values = get_option($option);
		if ( empty( $option_values ) ) {
			$this->default_settings();
		}
	}

	/**
	 * Add menu page
	 */
	public function wcbulkorderform_add_page() {
		if (class_exists('WPOvernight_Core')) {
			$wcbulkorderform_page = add_submenu_page(
				'wpo-core-menu',
				__( 'WC Bulk Order Form', 'wcbulkorderform' ),
				__( 'WC Bulk Order Form', 'wcbulkorderform' ),
				'manage_options',
				'wcbulkorderform_options_page',
				array( $this, 'wcbulkorderform_options_do_page' )
			);
		}
		else {
			$wcbulkorderform_page = add_submenu_page(
				'options-general.php',
				__( 'WC Bulk Order Form', 'wcbulkorderform' ),
				__( 'WC Bulk Order Form', 'wcbulkorderform' ),
				'manage_options',
				'wcbulkorderform_options_page',
				array( $this, 'wcbulkorderform_options_do_page' )
			);
		}
		add_action( 'admin_print_styles-' . $wcbulkorderform_page, array( &$this, 'wcbulkorderform_admin_styles' ) );
	}

	/**
	 * Add settings link to plugins page
	 */
	public function wcbulkorderform_add_settings_link( $links ) {
	    $settings_link = '<a href="options-general.php?page=wcbulkorderform_options_page">'. __( 'Settings', 'woocommerce' ) . '</a>';
	  	array_push( $links, $settings_link );
	  	return $links;
	}
	
	/**
	 * Styles for settings page
	 */
	public function wcbulkorderform_admin_styles() {
		wp_enqueue_style( 'wcbulkorderform-admin' );
	}
	 
	/**
	 * Default settings.
	 */
	public function default_settings() {
		global $options;
		$default = array(
			'search_by'				=> '4',
			'search_format'			=> '1',
			'new_row_button'		=> 'false',
			'bulkorder_row_number'	=> '5',
			'max_items'				=> '-1',
			'display_price'			=> 'true',
			'product_field_title'	=> 'Product',
			'quantity_field_title'	=> 'Quantity',
			'price_field_title'		=> 'Price',
			'no_load_css'			=> '',
			'display_images'		=> 'false'
		);
		
		update_option( 'wcbulkorderform', $default );
	}

	/**
	 * Build the options page.
	 */
	public function wcbulkorderform_options_do_page() {		
		?>
	
		<div class="wrap">
			<div class="icon32" id="icon-options-general"><br /></div>
			<h2><?php _e('WC Bulk Order Form','wcbulkorderform') ?></h2>
				<?php 
				global $options;
				//$option = get_option('wcbulkorderform');
				//print_r($option); //for debugging
				?>
				<form method="post" action="options.php">
				<?php
					//delete_option('wcbulkorderform');		
					settings_fields( 'wcbulkorderform' );
					do_settings_sections( 'wcbulkorderform' );

					submit_button();
				?>

			</form>
		</div>
		<div style="line-height: 20px; background: #F3F3F3;-moz-border-radius: 3px;border-radius: 3px;padding: 10px;-moz-box-shadow: 0 0 5px #ff0000;-webkit-box-shadow: 0 0 5px#ff0000;box-shadow: 0 0 5px #ff0000;padding: 10px;margin:0px auto; font-size: 13.8px;width: 60%;float: left"> 
			<h2><?php _e('Sell In Style With WooCommerce Bulk Order Pro!','wcbulkorderform') ?> - <span style="color:green"><a href="https://wpovernight.com/downloads/woocommerce-bulk-order-form/?utm_source=wordpress&utm_medium=wcbulkorderformfree&utm_campaign=bulkorderformbuy" style="color:green"><?php _e('Only $19!','wcbulkorderform') ?></a></span></h2>
			<br>
			<?php _e('Go Pro with WooCommerce Bulk Order Pro. Includes all the great standard features found in this free version plus:','wcbulkorderform') ?>
			<br>
			<ul style="list-style-type:circle;margin-left: 40px">
				<li><?php _e('Let user search by product id, title, sku, or all','wcbulkorderform') ?></li>
				<li><?php _e('Choose from 5 different label outputs for the product field on your bulk order form','wcbulkorderform') ?></li>
				<li><?php _e('Automatically add extra product rows with the "add row" button (can be turned on or off)','wcbulkorderform') ?>*</li>
				<li><?php _e('Create and customize as many shortcodes as you want!','wcbulkorderform') ?></li>
			</ul>
			*<?php _e('Customizable for each shortcode.','wcbulkorderform') ?>
			<br><br>
			<a class="button button-primary" style="text-align: center;margin: 0px auto" href="https://wpovernight.com/downloads/woocommerce-bulk-order-form/?utm_source=wordpress&utm_medium=wcbulkorderformfree&utm_campaign=bulkorderformbuy"><?php _e('Buy Now','wcbulkorderform') ?></a>
		</div>
		<script type="text/javascript">
			jQuery('.hidden-input').click(function() {
				jQuery(this).closest('.hidden-input').prev('.pro-feature').show('slow');
				jQuery(this).closest('.hidden-input').hide();
			});
			jQuery( document ).ready(function( $ ) {
			    $("input[id^=wcbulkorderform]:radio:lt(13)").attr('disabled',true);
			});
		</script>
		<?php
	}

	/**
	 * Text field callback.
	 *
	 * @param  array $args Field arguments.
	 *
	 * @return string	  Text field.
	 */
	public function text_element_callback( $args ) {
		$menu = $args['menu'];
		$id = $args['id'];
		$size = isset( $args['size'] ) ? $args['size'] : '25';
	
		$options = get_option( $menu );
	
		if ( isset( $options[$id] ) ) {
			$current = $options[$id];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '';
		}

		$disabled = (isset( $args['disabled'] )) ? ' disabled' : '';
		$html = sprintf( '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" size="%4$s"%5$s/>', $id, $menu, $current, $size, $disabled );
	
		// Displays option description.
		if ( isset( $args['description'] ) ) {
			$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
		}
	
		echo $html;
	}
	
	/**
	 * Displays a selectbox for a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function select_element_callback( $args ) {
		$menu = $args['menu'];
		$id = $args['id'];
		
		$options = get_option( $menu );
		
		if ( isset( $options[$id] ) ) {
			$current = $options[$id];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '';
		}

		$disabled = (isset( $args['disabled'] )) ? ' disabled' : '';
		
		$html = sprintf( '<select name="%1$s[%2$s]" id="%1$s[%2$s]"%3$s>', $menu, $id, $disabled );
		$html .= sprintf( '<option value="%s"%s>%s</option>', '0', selected( $current, '0', false ), '' );
		
		foreach ( $args['options'] as $key => $label ) {
			$html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $current, $key, false ), $label );
		}
		$html .= sprintf( '</select>' );

		if ( isset( $args['description'] ) ) {
			$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
		}
		
		echo $html;
	}

	/**
	 * Displays a multiple selectbox for a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function multiple_select_element_callback( $args ) {
		$html = '';
		foreach ($args as $id => $boxes) {
			$menu = $boxes['menu'];
			
			$options = get_option( $menu );
			
			if ( isset( $options[$id] ) ) {
				$current = $options[$id];
			} else {
				$current = isset( $boxes['default'] ) ? $boxes['default'] : '';
			}
			
			$disabled = (isset( $boxes['disabled'] )) ? ' disabled' : '';
			
			$html .= sprintf( '<select name="%1$s[%2$s]" id="%1$s[%2$s]"%3$s>', $menu, $id, $disabled);
			$html .= sprintf( '<option value="%s"%s>%s</option>', '0', selected( $current, '0', false ), '' );
			
			foreach ( (array) $boxes['options'] as $key => $label ) {
				$html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $current, $key, false ), $label );
			}
			$html .= '</select>';
	
			if ( isset( $boxes['description'] ) ) {
				$html .= sprintf( '<p class="description">%s</p>', $boxes['description'] );
			}
			$html .= '<br />';
		}
		
		
		echo $html;
	}

	/**
	 * Checkbox field callback.
	 *
	 * @param  array $args Field arguments.
	 *
	 * @return string	  Checkbox field.
	 */
	public function checkbox_element_callback( $args ) {
		$menu = $args['menu'];
		$id = $args['id'];
	
		$options = get_option( $menu );
	
		if ( isset( $options[$id] ) ) {
			$current = $options[$id];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '';
		}
	
		$disabled = (isset( $args['disabled'] )) ? ' disabled' : '';
		$html = sprintf( '<input type="checkbox" id="%1$s" name="%2$s[%1$s]" value="1"%3$s %4$s/>', $id, $menu, checked( 1, $current, false ), $disabled );
	
		// Displays option description.
		if ( isset( $args['description'] ) ) {
			$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
		}
	
		echo $html;
	}

	/**
	 * Displays a multicheckbox a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function radio_element_callback( $args ) {
		$menu = $args['menu'];
		$id = $args['id'];
	
		$options = get_option( $menu );
	
		if ( isset( $options[$id] ) ) {
			$current = $options[$id];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '';
		}

		$html = '';
		foreach ( $args['options'] as $key => $label ) {
			$html .= sprintf( '<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s"%4$s />', $menu, $id, $key, checked( $current, $key, false ) );
			$html .= sprintf( '<label for="%1$s[%2$s][%3$s]"> %4$s</label><br>', $menu, $id, $key, $label);
		}
		
		// Displays option description.
		if ( isset( $args['description'] ) ) {
			$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
		}

		if (isset( $args['disabled'] )) {
			$html .= ' <span style="display:none;" class="pro-feature"><i>'. __('This feature only available in', 'wcbulkorderform') .' <a href="https://wpovernight.com/downloads/woocommerce-bulk-order-form/?utm_source=wordpress&utm_medium=wcbulkorderformfree&utm_campaign=bulkorderformfree">Bulk Order Form Pro</a></i></span>';
			$html .= '<div style="position:absolute; left:0; right:0; top:0; bottom:0; background-color:white; -moz-opacity: 0; opacity:0;filter: alpha(opacity=0);" class="hidden-input"></div>';
			$html = '<div style="display:inline-block; position:relative;">'.$html.'</div>';
		}
			
		echo $html;
	}

	/**
	 * Displays a multicheckbox a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function icons_radio_element_callback( $args ) {
		$menu = $args['menu'];
		$id = $args['id'];
	
		$options = get_option( $menu );
	
		if ( isset( $options[$id] ) ) {
			$current = $options[$id];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '';
		}

		$icons = '';
		$radios = '';
		
		foreach ( $args['options'] as $key => $iconnumber ) {
			$icons .= sprintf( '<td style="padding-bottom:0;font-size:16pt;" align="center"><label for="%1$s[%2$s][%3$s]"><i class="wcbulkorderform-icon-shopping-cart-%4$s"></i></label></td>', $menu, $id, $key, $iconnumber);
			$radios .= sprintf( '<td style="padding-top:0" align="center"><input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s"%4$s /></td>', $menu, $id, $key, checked( $current, $key, false ) );
		}

		$html = '<table><tr>'.$icons.'</tr><tr>'.$radios.'</tr></table>';
		$html .= '<p class="description"><i>'. __('<strong>Please note:</strong> you need to open your website in a new tab/browser window after updating the cart icon for the change to be visible!','wcbulkorderform').'</p>';
		
		echo $html;
	}

	/**
	 * Section null callback.
	 *
	 * @return void.
	 */
	public function section_options_callback() {
	
	}

	/**
	 * Validate/sanitize options input
	 */
	public function wcbulkorderform_options_validate( $input ) {
		// Create our array for storing the validated options.
		$output = array();

		// Loop through each of the incoming options.
		foreach ( $input as $key => $value ) {

			// Check to see if the current option has a value. If so, process it.
			if ( isset( $input[$key] ) ) {

				// Strip all HTML and PHP tags and properly handle quoted strings.
				$output[$key] = strip_tags( stripslashes( $input[$key] ) );
			}
		}

		// Return the array processing any additional functions filtered by this action.
		return apply_filters( 'wcbulkorderform_validate_input', $output, $input );
	}

}