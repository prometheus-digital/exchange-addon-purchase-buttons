<?php
/**
 * Functions and hooks related to settings
 * @since 1.0.0
*/

/**
 * Enqueue scripts for admin page
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_purchase_buttons_enqueue_admin_scripts( $prefix ) {
	if ( empty( $prefix ) || 'exchange_page_it-exchange-addons' != $prefix ) {
		return;
	}

	if ( empty( $_GET['add-on-settings'] ) || 'purchase-buttons' != $_GET['add-on-settings'] ) {
		return;
	}

	// Enqueue JS
	 wp_enqueue_script( 'it-exchange-addon-purchase-buttons-settings', ITUtility::get_url_from_file( dirname( __FILE__ ) ) . '/js/settings.js', array( 'jquery') );

	// Enqueue CSS
	 wp_enqueue_style( 'it-exchange-addon-purchase-buttons-settings', ITUtility::get_url_from_file( dirname( __FILE__ ) ) . '/styles/settings.css' );
}
add_action( 'admin_enqueue_scripts', 'it_exchange_purchase_buttons_enqueue_admin_scripts' );

/**
 * This function prints the settings page
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_purchase_buttons_addon_print_settings() {
	$settings = it_exchange_get_option( 'addon_purchase_buttons', true );
	$form_options = array(
		'id'      => 'it-exchange-addon-purchase-buttons-settings',
		'action'  => 'admin.php?page=it-exchange-addons&add-on-settings=purchase-buttons',
	);
	$form                      = new ITForm( array(), array( 'prefix' => 'it-exchange-addon-purchase-buttons' ) );
	$product_types             = it_exchange_get_enabled_addons( array( 'category' => 'product-type' ) );
	$disable_buy_now_class     = empty( $settings['disable-buy-now'] ) ? 'ite-purchase-buttons-disabled' : '';
	$disable_add_to_cart_class = empty( $settings['disable-add-to-cart'] ) ? 'ite-purchase-buttons-disabled' : '';
	$buy_now_scope_class       = empty( $settings['disable-buy-now-scope'] ) || 'product-type' != $settings['disable-buy-now-scope'] ? 'ite-purchase-buttons-hidden-ul' : '';
	$add_to_cart_scope_class   = empty( $settings['disable-add-to-cart-scope'] ) || 'product-type' != $settings['disable-add-to-cart-scope'] ? 'ite-purchase-buttons-hidden-ul' : '';
	?>
	<div class="wrap">
		<?php ITUtility::screen_icon( 'it-exchange' ); ?>
		<h2><?php _e( 'Purchase Button Default Settings', 'LION' ); ?></h2>

		<?php do_action( 'it_exchange_purchase_buttons_settings_page_top' ); ?>
		<?php do_action( 'it_exchange_addon_settings_page_top' ); ?>
		<?php $form->start_form( $form_options, 'it-exchange-purchase-buttons-settings' ); ?>

		<?php
		do_action( 'it_exchange_purchase_buttons_settings_form_top' );
		if ( ! empty( $_POST['__it-form-prefix'] ) && 'it-exchange-addon-purchase-buttons' == $_POST['__it-form-prefix'] )
			ITUtility::show_status_message( __( 'Options Saved', 'LION' ) );

		?>

		<h2><?php _e( 'Free Offer License', 'LION' ); ?></h2>

		<?php
		$exchangewp_purchase_buttons_options = get_option( 'it-storage-exchange_purchase_buttons-addon' );
		$license = trim( $exchangewp_purchase_buttons_options['purchase_buttons-license-key'] );
		// var_dump($license);
		$exstatus = trim( get_option( 'exchange_purchase_buttons_license_status' ) );
		//  var_dump($exstatus);

		$after_license = wp_nonce_field( 'exchange_purchase_buttons_nonce', 'exchange_purchase_buttons_nonce' );

		if( $exstatus !== false && $exstatus == 'valid' ) {

			$after_license .= '<span style="color:green;">active</span>';
			$after_license .= '<input type="submit" class="button-secondary" name="exchange_purchase_buttons_license_deactivate" value="Deactivate License"/>';
		} else {
			$after_license .= '<input type="submit" class="button-secondary" name="exchange_purchase_buttons_license_activate" value="Activate License"/>';
		}

		$options = array(
			'prefix'      => 'purchase_buttons-addon',
			'form-fields' => array(
				array(
					'type'	=> 'heading',
					'label' => __('License Key', 'LION' ),
					'slug' => 'purchase_buttons-license-key-heading',
				),
				array(
					'type' => 'text_box',
					'label' => __('Enter License Key', 'LION'),
					'slug' => 'purchase_buttons-license-key',
					'after' => $after_license,
				),
			),
		);

		it_exchange_print_admin_settings_form( $options );
		?>
		<p><?php _e( 'You can override the below settings on a per-product basis in the Advanced tab of the New / Edit Product screen for each product.', 'LION' ); ?></p>

		<ul class="puchase-button-settings">
			<li class="top-level-setting">
				<label for="disable-buy-now">
					<?php
					$field_options = array();
					if ( ! empty( $settings['disable-buy-now'] ) ) {
						$field_options['checked'] = true;
					}
					$form->add_check_box( 'disable-buy-now', $field_options ); ?> <?php _e( 'Disable Buy Now buttons', 'LION' );
					?>
				</label>
				<ul class="disable-buy-now-settings <?php esc_attr_e( $disable_buy_now_class ); ?>">
					<li>
						<label for="disable-buy-now-scope-globally">
							<?php
							$radio_options = array(
								'id'      => 'disable-buy-now-scope-globally',
								'value'   => 'globally',
								'class'   => 'disable-buy-now-scope-option',
							);
							if ( empty( $settings['disable-buy-now-scope'] ) || ( ! empty( $settings['disable-buy-now-scope'] ) && 'globally' == $settings['disable-buy-now-scope'] ) ) { $radio_options['checked'] = true; }
							if ( ! empty( $disable_buy_now_class ) ) { $radio_options['disabled'] = 'disabled'; }
							$form->add_radio( 'disable-buy-now-scope', $radio_options ); ?> <?php _e( 'Disable Buy Now globally', 'LION' );
							?>
						</label>
					</li>
					<li>
						<label for="disable-buy-now-scope-product-type">
							<?php
							$radio_options = array(
								'id'      => 'disable-buy-now-scope-product-type',
								'class'   => 'disable-buy-now-scope-option',
								'value'   => 'product-type',
							);
							if ( ! empty( $settings['disable-buy-now-scope'] ) && 'product-type' == $settings['disable-buy-now-scope'] ) { $radio_options['checked'] = true; }
							if ( ! empty( $disable_buy_now_class ) ) { $radio_options['disabled'] = 'disabled'; }
							$form->add_radio( 'disable-buy-now-scope', $radio_options ); ?> <?php _e( 'Disable Buy Now for specific product types', 'LION' );
							?>
						</label>
						<ul class="buy-now-product-type-scope-ul <?php esc_attr_e( $buy_now_scope_class ); ?>">
						<?php
						$buy_now_product_types = $product_types;
						if ( isset( $buy_now_product_types['invoices-product-type'] ) ) {
							unset( $buy_now_product_types['invoices-product-type'] );
						}
						$buy_now_product_types = apply_filters( 'it_exchange_purchase_buttons_addon_buy_now_product_types_options', $buy_now_product_types );
						?>
						<?php foreach( (array) $buy_now_product_types as $type ) { ?>
							<li>
								<label for="disable-buy-now-for-product-type-<?php esc_attr_e( $type['slug'] ); ?>">
									<?php
									$field_options = array();
									if ( ! empty( $settings['disable-buy-now-for-product-type'][$type['slug']] ) ) {
										$field_options['checked'] = true;
									}
									$form->add_check_box( 'disable-buy-now-for-product-type[' . esc_attr( $type['slug'] ) . ']', $field_options ); ?> <?php echo $type['name'];
									?>
								</label>
							</li>
						<?php } ?>
						</ul>
					</li>
				</ul>
			</li>

			<li class="top-level-setting">
				<label for="disable-add-to-cart">
					<?php
					$field_options = array();
					if ( ! empty( $settings['disable-add-to-cart'] ) ) {
						$field_options['checked'] = true;
					}
					$form->add_check_box( 'disable-add-to-cart', $field_options ); ?> <?php _e( 'Disable Add to Cart buttons', 'LION' );
					?>
				</label>
				<ul class="disable-add-to-cart-settings <?php esc_attr_e( $disable_add_to_cart_class ); ?>">
					<li>
						<label for="disable-add-to-cart-scope-globally">
							<?php
							$radio_options = array(
								'id'      => 'disable-add-to-cart-scope-globally',
								'class'   => 'disable-add-to-cart-scope-option',
								'value'   => 'globally',
							);
							if ( empty( $settings['disable-add-to-cart-scope'] ) || ( ! empty( $settings['disable-add-to-cart-scope'] ) && 'globally' == $settings['disable-add-to-cart-scope'] ) ) { $radio_options['checked'] = true; }
							if ( ! empty( $disable_add_to_cart_class ) ) { $radio_options['disabled'] = true; }
							$form->add_radio( 'disable-add-to-cart-scope', $radio_options ); ?> <?php _e( 'Disable Add to Cart globally', 'LION' ); ?>
						</label>
					</li>
					<li>
						<label for="disable-add-to-cart-scope-product-type">
							<?php
							$radio_options = array(
								'id'      => 'disable-add-to-cart-scope-product-type',
								'class'   => 'disable-add-to-cart-scope-option',
								'value'   => 'product-type',
							);
							if ( ! empty( $settings['disable-add-to-cart-scope'] ) && 'product-type' == $settings['disable-add-to-cart-scope'] ) { $radio_options['checked'] = true; }
							if ( ! empty( $disable_add_to_cart_class ) ) { $radio_options['disabled'] = true; }
							$form->add_radio( 'disable-add-to-cart-scope', $radio_options ); ?> <?php _e( 'Disable Add to Cart for specific product types', 'LION' );
							?>
						</label>
						<ul class="add-to-cart-product-type-scope-ul <?php esc_attr_e( $add_to_cart_scope_class ); ?>">
							<?php
							$add_to_cart_product_types = $product_types;
							if ( isset( $add_to_cart_product_types['invoices-product-type'] ) ) {
								unset( $add_to_cart_product_types['invoices-product-type'] );
							}
							$add_to_cart_product_types = apply_filters( 'it_exchange_purchase_buttons_addon_add_to_cart_product_types_options', $add_to_cart_product_types );
							?>
							<?php foreach( (array) $add_to_cart_product_types as $type ) { ?>
								<li>
									<label for="disable-add-to-cart-for-product-type-<?php esc_attr_e( $type['slug'] ); ?>">
										<?php
										$field_options = array();
										if ( ! empty( $settings['disable-add-to-cart-for-product-type'][$type['slug']] ) ) {
											$field_options['checked'] = true;
										}
										$form->add_check_box( 'disable-add-to-cart-for-product-type[' . esc_attr( $type['slug'] ) . ']', $field_options ); ?> <?php echo $type['name'];
										?>
									</label>
								</li>
							<?php } ?>
						</ul>
					</li>
				</ul>
			</li>
		</ul>
		<?php $form->add_submit( 'purchase-buttons' ); ?>
		<?php do_action( 'it_exchange_purchase_buttons_settings_form_bottom' ); ?>
		<?php $form->end_form(); ?>
		<?php do_action( 'it_exchange_addon_settings_page_bottom' ); ?>
		<?php do_action( 'it_exchange_purchase_buttons_settings_page_bottom' ); ?>
	</div>
	<?php
}

/**
 * Saves the form settings
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_purchase_buttons_addon_save_settings() {
	// Abandon if not updating our settings
	if ( empty( $_POST['__it-form-prefix'] ) || 'it-exchange-addon-purchase-buttons' !== $_POST['__it-form-prefix'] )
		return;

	// Check the nonce
	check_admin_referer( 'it-exchange-purchase-buttons-settings' );

	// Get submitted values
	$values = ITForm::get_post_data();

	// Replace disabled fields with old data if they didnn't come through
	if ( empty( $values['disable-buy-now-scope'] ) || empty( $values['disable-add-to-cart-scope'] ) ) {
		$old_settings = it_exchange_get_option( 'addon_purchase_buttons', true );
		$values['disable-buy-now-scope'] = empty( $values['disable-buy-now-scope'] ) ? ( empty( $old_values['disable-buy-now-scope'] ) ? 'globally' : $old_values['disable-buy-now-scope'] ) : $values['disable-buy-now-scope'];
		$values['disable-add-to-cart-scope'] = empty( $values['disable-add-to-cart-scope'] ) ? ( empty( $old_values['disable-add-to-cart-scope'] ) ? 'globally' : $old_values['disable-add-to-cart-scope'] ) : $values['disable-add-to-cart-scope'];
	}

	// Save values
	it_exchange_save_option( 'addon_purchase_buttons', $values );
}
add_action( 'admin_init', 'it_exchange_purchase_buttons_addon_save_settings' );

function exchange_purchase_buttons_license_activate() {

	if( isset( $_POST['exchange_purchase_buttons_license_activate'] ) ) {

			// run a quick security check
		 	if( ! check_admin_referer( 'exchange_purchase_buttons_nonce', 'exchange_purchase_buttons_nonce' ) )
				return; // get out if we didn't click the Activate button

			// retrieve the license from the database
			// $license = trim( get_option( 'exchange_purchase_buttons_license_key' ) );
	   $exchangewp_purchase_buttons_options = get_option( 'it-storage-exchange_purchase_buttons-addon' );
	   $license = trim( $exchangewp_purchase_buttons_options['purchase_buttons-license-key'] );

			// 	var_dump($license);
			// data to send in our API request
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license,
				'item_name'  => urlencode( 'purchase-buttons' ), // the name of our product in EDD
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_post( 'https://exchangewp.com', array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.' );
				}

			} else {

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				if ( false === $license_data->success ) {

					switch( $license_data->error ) {

						case 'expired' :

							$message = sprintf(
								__( 'Your license key expired on %s.' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
							);
							break;

						case 'revoked' :

							$message = __( 'Your license key has been disabled.' );
							break;

						case 'missing' :

							$message = __( 'Invalid license.' );
							break;

						case 'invalid' :
						case 'site_inactive' :

							$message = __( 'Your license is not active for this URL.' );
							break;

						case 'item_name_mismatch' :

							$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), 'purchase_buttons' );
							break;

						case 'no_activations_left':

							$message = __( 'Your license key has reached its activation limit.' );
							break;

						default :

							$message = __( 'An error occurred, please try again.' );
							break;
					}

				}

			}

			// Check if anything passed on a message constituting a failure
			if ( ! empty( $message ) ) {
				$base_url = admin_url( 'admin.php?page=' . 'purchase_buttons' );
				$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

				wp_redirect( $redirect );
				exit();
			}

			//$license_data->license will be either "valid" or "invalid"
			update_option( 'exchange_purchase_buttons_license_status', $license_data->license );
			wp_redirect( admin_url( 'admin.php?page=it-exchange-addons&add-on-settings=purchase-buttons' ) );
			exit();
		}

}
add_action('admin_init', 'exchange_purchase_buttons_license_deactivate');
add_action('admin_init', 'exchange_purchase_buttons_license_activate');

function exchange_purchase_buttons_license_deactivate() {

	 // deactivate here
	 // listen for our activate button to be clicked
		if( isset( $_POST['exchange_purchase_buttons_license_deactivate'] ) ) {

			// run a quick security check
		 	if( ! check_admin_referer( 'exchange_purchase_buttons_nonce', 'exchange_purchase_buttons_nonce' ) )
				return; // get out if we didn't click the Activate button

			// retrieve the license from the database
			// $license = trim( get_option( 'exchange_purchase_buttons_license_key' ) );

			$exchangewp_purchase_buttons_options = get_option( 'it-storage-exchange_purchase_buttons-addon' );
 	    $license = trim( $exchangewp_purchase_buttons_options['purchase_buttons-license-key'] );



			// data to send in our API request
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $license,
				'item_name'  => urlencode( 'purchase-buttons' ), // the name of our product in EDD
				'url'        => home_url()
			);
			// Call the custom API.
			$response = wp_remote_post( 'https://exchangewp.com', array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.' );
				}

				// $base_url = admin_url( 'admin.php?page=' . 'purchase_buttons-license' );
				// $redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

				wp_redirect( 'admin.php?page=it-exchange-addons&add-on-settings=purchase-buttons' );
				exit();
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			// $license_data->license will be either "deactivated" or "failed"
			if( $license_data->license == 'deactivated' ) {
				delete_option( 'exchange_purchase_buttons_license_status' );
			}

			wp_redirect( admin_url( 'admin.php?page=it-exchange-addons&add-on-settings=purchase-buttons' ) );
			exit();

		}
}
