<?php
/**
 * Functions and hooks related to settings
 * @since 1.0.0
*/

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
	$form         = new ITForm( array(), array( 'prefix' => 'it-exchange-addon-purchase-buttons' ) );

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
		<p><?php _e( 'These settings allow you to determine what purchase buttons appear by default for all products.', 'LION' ); ?> <?php _e( 'Some product types like membership will override these settings.', 'LION' ); ?></p>
		<p><?php _e( 'You can override the below settings on a per-product basis in the Advanced tab of the New / Edit Product screen for each product.', 'LION' ); ?></p>
		<?php do_action( 'it_exchange_purchase_buttons_settings_form_bottom' ); ?>
		<?php do_action( 'it_exchange_addon_settings_page_bottom' ); ?>
		<?php do_action( 'it_exchange_purchase_buttons_settings_page_bottom' ); ?>
	</div>
	<?php
}
