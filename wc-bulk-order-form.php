<?php
/*
  Plugin Name: WooCommerce Bulk Order Form
  Plugin URI: http://wpovernight.com/
  Description: Adds the [wcbulkorder] shortcode which allows you to display bulk order forms on any page in your site
  Version: 2.2
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

class WCBulkOrderForm {

	/**
	 * Construct.
	 */
	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'delete_old_options' ) );
		$this->includes();
		$mainoptions = get_option('wcbulkorderform');
		$this->settings = new WCBulkOrderForm_Settings();
		if(empty($this->options)) {
			register_activation_hook( __FILE__, array( $this->settings, 'default_settings' ) );
			$this->options = get_option('wcbulkorderform');
		}
		register_activation_hook( __FILE__, array( $this, 'register_templates' ) );
		register_activation_hook( __FILE__, array( $this, 'register_default_template' ) );
		add_action( 'plugins_loaded', array( $this, 'languages' ), 0 ); // or use init?
	}
	
	/**
	 * Delete Options starting in version 2.2. Remove by version 2.3
	 */
	function delete_old_options(){
		delete_option( 'wcbulkorderform' );
	}

	/**
	 * Load additional classes and functions
	 */
	public function includes() {
		$mainoptions = get_option('wcbulkorderform');
		//print_r($mainoptions);
		$template = isset($mainoptions['template_style']) ? $mainoptions['template_style'] : '';
		if($template === 'Standard'){
			include_once( 'includes/templates/standard_template/standard_template.php' );
			$WCBulkOrderForm_Standard_Template = new WCBulkOrderForm_Standard_Template();
		}
		if($template === 'Variation'){
			include_once( 'includes/templates/variation_template/variation_search_template.php' );
		    $WCBulkOrderForm_Variation_Template = new WCBulkOrderForm_Variation_Template();
		}
		
		include_once( 'includes/wcbulkorder-settings.php' );
		include_once( 'includes/wc-bulk-order-form-compatibility.php' );
	}
	
	/**
	 * Load translations.
	 */
	public function languages() {
		load_plugin_textdomain( 'wcbulkorderform', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Register Standard Templates
	 */
	function register_templates() {
		global $options;
		$sections = get_option('wcbulkorderform_sections');
		if(empty($sections['templates'])){
			$sections['templates'] = array();
		}
		if(!in_array('Variation',$sections['templates'])){
			$sections['templates'][] = 'Variation';
		}
		if(!in_array('Standard',$sections['templates'])){
			$sections['templates'][] = 'Standard';
		}
		update_option('wcbulkorderform_sections',$sections);
	}

	/**
	 * set Standard Template as the Default 
	*/

	function register_default_template(){
		include_once( 'includes/templates/standard_template/standard_template.php' );
		$WCBulkOrderForm_Standard_Template = new WCBulkOrderForm_Standard_Template();
	}

}
$WCBulkOrderForm = new WCBulkOrderForm();