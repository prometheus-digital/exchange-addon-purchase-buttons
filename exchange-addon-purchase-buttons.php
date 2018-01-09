<?php
/*
 * Plugin Name: ExchangeWP - Purchase Buttons
 * Version: 0.0.1
 * Description: Allows you to customize which purchase buttons are available for what products
 * Plugin URI: https://exchangewp.com/downloads/purchase-buttons/
 * Author: ExchangeWP
 * Author URI: https://exchangewp.com
 * ExchangeWP Package: exchange-addon-purchase-buttons

 * Installation:
 * 1. Download and unzip the latest release zip file.
 * 2. If you use the WordPress plugin uploader to install this plugin skip to step 4.
 * 3. Upload the entire plugin directory to your `/wp-content/plugins/` directory.
 * 4. Activate the plugin through the 'Plugins' menu in WordPress Administration.
 *
*/

/**
 * This registers our plugin as a membership addon
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_register_purchase_buttons_addon() {
	$options = array(
		'name'              => __( 'Purchase Buttons', 'LION' ),
		'description'       => __( 'Allows you to customize which purchase buttons are available for what products.', 'LION' ),
		'author'            => 'ExchangeWP',
		'author_url'        => 'https://exchangewp.com/downloads/purchase-buttons/',
		'icon'              => ITUtility::get_url_from_file( dirname( __FILE__ ) . '/lib/images/purchase-buttons-50px.png' ),
		'file'              => dirname( __FILE__ ) . '/init.php',
		'category'          => 'product-feature',
		'basename'          => plugin_basename( __FILE__ ),
		'settings-callback' => 'it_exchange_purchase_buttons_addon_print_settings',
	);
	it_exchange_register_addon( 'purchase-buttons', $options );
}
add_action( 'it_exchange_register_addons', 'it_exchange_register_purchase_buttons_addon' );

/**
 * Loads the translation data for WordPress
 *
 * @uses load_plugin_textdomain()
 * @since 1.0.0
 * @return void
*/
function it_exchange_purchase_buttons_set_textdomain() {
	load_plugin_textdomain( 'LION', false, dirname( plugin_basename( __FILE__  ) ) . '/lang/' );
}
add_action( 'plugins_loaded', 'it_exchange_purchase_buttons_set_textdomain' );

/**
 * Registers Plugin with ExchangeWP updater class
 *
 * @since 1.0.0
 *
 * @param object $updater exchangewp updater object
 * @return void
*/
function exchange_purchase_buttons_plugin_update() {

	$license_check = get_transient( 'exchangewp_license_check' );

	if ($license_check->license == 'valid' ) {
		$license_key = it_exchange_get_option( 'exchangewp_licenses' );
		$license = $license_key['exchange_license'];

		$edd_updater = new EDD_SL_Plugin_Updater( 'https://exchangewp.com', __FILE__, array(
				'version' 		=> '0.0.1', 				// current version number
				'license' 		=> $license, 				// license key (used get_option above to retrieve from DB)
				'item_id'		 	=> 536,					 	  // name of this plugin
				'author' 	  	=> 'ExchangeWP',    // author of this plugin
				'url'       	=> home_url(),
				'wp_override' => true,
				'beta'		  	=> false
			)
		);
	}

}

add_action( 'admin_init', 'exchange_purchase_buttons_plugin_update', 0 );
