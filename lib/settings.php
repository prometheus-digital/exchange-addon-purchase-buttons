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
		$values['disable-buy-now-scope'] = empty( $values['disable-buy-now-scope'] ) ? ( empty( $old_values['disable-buy-now-scope'] ) ? 'globally' : $old_values['disable-buy-now-scope'] ) : $values['disabled-buy-now-scope'];
		$values['disable-add-to-cart-scope'] = empty( $values['disable-add-to-cart-scope'] ) ? ( empty( $old_values['disable-add-to-cart-scope'] ) ? 'globally' : $old_values['disable-add-to-cart-scope'] ) : $values['disable-add-to-cart-scope'];
	}

	// Save values
	it_exchange_save_option( 'addon_purchase_buttons', $values );
}
add_action( 'admin_init', 'it_exchange_purchase_buttons_addon_save_settings' );
