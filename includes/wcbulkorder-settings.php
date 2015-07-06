<?php

class WCBulkOrderForm_Settings {
	
	public function __construct() {
		add_action( 'admin_init', array( $this, 'init_settings' ) ); // Registers settings
		add_action( 'admin_menu', array( $this, 'wcbulkorderform_add_page' ) );
	}

	/**
	 * User settings.
	 */
	public function init_settings() {

		$option = 'wcbulkorderform';
	
		// Create option in wp_options.
		if ( false == get_option( $option ) ) {
			add_option( $option );
		}
	
		// Template Selection Section.
		add_settings_section(
			'plugin_settings',
			__( 'Select a Template', 'wcbulkorderform' ),
			array( $this, 'section_options_callback' ),
			$option
		);

		// Template Selection
		add_settings_field(
			'template_style',
			__( 'Select which template you want to use. More Templates Coming Soon!', 'wcbulkorderform' ),
			array( $this, 'radio_element_callback' ),
			$option,
			'plugin_settings',
			array(
				'menu'			=> $option,
				'id'			=> 'template_style',
				'options' 		=> $this->template_types(),
				'default'		=> ''
			)
		);

		// Register settings.
		register_setting( $option, $option, array( $this, 'wcbulkorderform_options_validate' ) );

		// Register defaults if settings empty (might not work in case there's only checkboxes and they're all disabled)
		$option_values = get_option($option);
		
		if ( empty( $option_values ) ) {
			$this->default_settings();
		}
	}

	/*
	 * Get Registered Templates
	*/
	function template_types(){
		$sections = array();
		$sections = get_option('wcbulkorderform_sections');
		$sections = $sections['templates'];
		$templates = array();
		if(!empty($sections)){
			foreach ($sections as $template){
				$templates[$template] = $template. __( ' Template' , 'wcbulkorderform' );
			}
		}
		return $templates;
	}

	/*
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
		} else {
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
		$sections = get_option('wcbulkorderform_sections');
		if(empty($sections['templates'])){
			$sections['templates'] = array();
		}
		update_option('wcbulkorderform_sections',$sections);
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
	        if (isset($_GET['tab'])) {
	            $active_tab = $_GET['tab'];
	        } else {
	            //set display_options tab as a default tab.
	            $active_tab = 'template_selection';
	        }
	   
	        if (is_plugin_active('wpovernight-sidekick/wpovernight-sidekick.php')) {
			?>
	        <h2 class="nav-tab-wrapper">
	            <a href="?page=wcbulkorderform_options_page&tab=template_selection" class="nav-tab <?php echo $active_tab == 'template_selection' ? 'nav-tab-active' : ''; ?>">Template Selection</a>  
	            <a href="?page=wcbulkorderform_options_page&tab=template_settings" class="nav-tab <?php echo $active_tab == 'template_settings' ? 'nav-tab-active' : ''; ?>">Template Settings</a>
	            <a href="?page=wcbulkorderform_options_page&tab=support" class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>">Help</a>
	        </h2>
			<?php
			} else {
			?>
			<h2 class="nav-tab-wrapper">
	            <a href="?page=wcbulkorderform_options_page&tab=template_selection" class="nav-tab <?php echo $active_tab == 'template_selection' ? 'nav-tab-active' : ''; ?>">Template Selection</a>  
	            <a href="?page=wcbulkorderform_options_page&tab=template_settings" class="nav-tab <?php echo $active_tab == 'template_settings' ? 'nav-tab-active' : ''; ?>">Template Settings</a>
	        	<a href="?page=wcbulkorderform_options_page&tab=support" class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>">Help</a>
	        </h2>
			<?php }
				global $options;
				$sections = get_option('wcbulkorderform_sections');
				//print_r($sections);
				//$option = get_option('wcbulkorderform');
				//print_r($option); //for debugging
				?>
				<form method="post" action="options.php">
				<?php
					if ($active_tab == 'template_selection') {
						settings_fields( 'wcbulkorderform' );
						do_settings_sections( 'wcbulkorderform' );
						submit_button('Save Template Selection');
			        } elseif ($active_tab == 'template_settings'){
			            do_action('wcbulkorderform_settings');
			            submit_button();
			        } else {
			        	?>
			        		<div style="margin-top:20px;margin-bottom:40px">
				        		<h2>Looking for help?</h2>
								<p>Here is how to find it:</p>
								<ol>
									<li><a href="https://wpovernight.com/2014/11/woocommerce-bulk-order-form-2-0-update/">Read the documentation</a> for the bulk order form plugin. It should answer most of your questions and may show you some other cool things you didn't know about the Bulk Order Form Plugin.</li>
									<li>Look for the answer to your question in the <a href="https://wpovernight.com/faq-category/bulk-order-form/">FAQs at WP Overnight</a>.</li>
									<li>Email support@wpovernight.com and we'll answer your question as quickly as possible.</li>
								</ol>
							</div>
			        	<?php
			        }
				?>
			</form>
			<?php if (!class_exists('WCBulkOrderFormPro') || !class_exists('WCBulkOrder_Product_Limiter') || !class_exists('WCBulkOrderForm_Prepopulated')){ ?>
			<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('.extensions .more').hide();
				jQuery('.extensions > li').click(function() {
					jQuery(this).toggleClass('expanded');
					jQuery(this).find('.more').slideToggle();
				});
			});
			</script>
			<div class="wcpdf-extensions-ad">
				<img src="<?php echo plugins_url( 'images/wpo-helper.png', __FILE__ ); ?>" class="wpo-helper">
				<h3><?php _e( 'Check out these premium extensions!', 'wcbulkorderform' ); ?></h3>
				<i>(<?php _e( 'Click items to read more', 'wcbulkorderform' ); ?>)</i>
				<ul class="extensions">
					<?php
					if (!class_exists('WCBulkOrderFormPro')) {
						?>
						<li>
							<?php _e('Go Pro: Get WooCommerce Bulk Order Form Pro!', 'wcbulkorderform')?>
							<div class="more" style="display:none;">
							<?php _e( 'Supercharge WooCommerce Bulk Order Form with the following features:', 'wcbulkorderform' ); ?>
							<ul>
								<li><?php _e('Let user search by product id, title, sku, or all','wcbulkorderform') ?></li>
								<li><?php _e('Choose from 5 different label outputs for the product field on your bulk order form','wcbulkorderform') ?></li>
								<li><?php _e('Automatically add extra product rows with the "add row" button (can be turned on or off)','wcbulkorderform') ?>*</li>
								<li><?php _e('Create and customize as many shortcodes as you want!','wcbulkorderform') ?></li>
								<li><?php _e('Display images in product search','wcbulkorderform') ?></li>
								<li><?php _e('Set custom add to cart success/failure messages','wcbulkorderform') ?></li>
								<li><?php _e('Pick to send user to cart or checkout','wcbulkorderform') ?></li>
								<li><?php _e('Option to include add to cart button next to each product field','wcbulkorderform') ?></li>
							</ul>
							<a href="https://wpovernight.com/downloads/woocommerce-bulk-order-form/" target="_blank"><?php _e("Get WooCommerce Bulk Order Form Pro!", 'wcbulkorderform'); ?></a>
						</li>
					<?php } ?>
					<?php
					if (!class_exists('WCBulkOrder_Product_Limiter')) {
						?>
						<li>
							<?php _e('Bulk Order Form Limit Products', 'wcbulkorderform')?>
							<div class="more" style="display:none;">
								<?php _e( 'Exclude or include specific products or variations with a simple checkbox, or by category', 'wcbulkorderform' ); ?><br/>
								<a href="https://wpovernight.com/downloads/bulk-order-form-limit-products/" target="_blank"><?php _e("Get Bulk Order Form Limit Products!", 'wcbulkorderform'); ?></a>
							</div>
						</li>
					<?php } ?>
					<?php
					if (!class_exists('WCBulkOrderForm_Prepopulated')) {
						?>
						<li>
							<?php _e('Bulk Order Form Prepopulated Template', 'wcbulkorderform')?>
							<div class="more" style="display:none;">
								<?php _e( 'Remove the autocomplete search and pre-populate the form with your products and variations. (Very Popular)!', 'wcbulkorderform' ); ?><br/>
								<a href="https://wpovernight.com/downloads/wc-bulk-order-form-prepopulated/" target="_blank"><?php _e("Get Bulk Order Form Prepopulated Template!", 'wcbulkorderform'); ?></a>
							</div>
						</li>
					<?php } ?>
				</ul>
			</div>
			<style>
				.wcpdf-extensions-ad {
					position: relative;
					min-height: 90px;
					border: 1px solid #3D5C99;
					background-color: #EBF5FF;	
					border-radius: 5px;
					padding: 15px;
					padding-left: 100px;
					margin-top: 15px;
					margin-bottom: 15px;
				}
				img.wpo-helper {
					position: absolute;
					top: -20px;
					left: 3px;
				}
				.wcpdf-extensions-ad h3 {
					margin: 0;
				}
				.wcpdf-extensions-ad ul {
					margin: 0;
					margin-left: 1.5em;
				}
				.extensions li {
					margin: 0;
				}
				.extensions li ul {
					list-style-type: square;
					margin-top: 0.5em;
					margin-bottom: 0.5em;
				}
				.extensions > li:before { 
					content: "";
					border-color: transparent transparent transparent #111;
					border-style: solid;
					border-width: 0.35em 0.35em 0.35em 0.45em;
					display: block;
					height: 0;
					width: 0;
					left: -1em;
					top: 0.9em;
					position: relative;
				}
				.extensions .expanded:before {
					border-color: #111 transparent transparent transparent;
					left: -1.17em;
					border-width: 0.45em 0.45em 0.35em 0.35em !important;
				}
				.extensions .more {
					padding: 10px;
					background-color: white;
					border: 1px solid #ccc;
					border-radius: 5px;
				}
				.extensions table td {
					vertical-align: top;
				}
			</style>
			<?php } ?>
			<script type="text/javascript">
			jQuery('.hidden-input').click(function() {
				jQuery(this).closest('.hidden-input').prev('.pro-feature').show('slow');
				jQuery(this).closest('.hidden-input').hide();
			});
			jQuery( document ).ready(function( $ ) {
			    $("input.wcbulkorder-disabled").attr('disabled',true);
			});
		</script>
		</div>
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