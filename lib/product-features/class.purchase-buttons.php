<?php
/**
 * This will load UI for purchase buttons product-feature. It allows store owners to modify where and how
 * the Buy Now and the Add to Cart buttons are placed globally or for specific products
 *
 * @since 1.0.0
 * @package IT_Exchange
*/


class IT_Exchange_Product_Feature_Purchase_Buttons extends IT_Exchange_Product_Feature_Abstract {

	/**
	 * Constructor. Registers hooks
	 *
	 * @since 1.0.0
	 * @return void
	*/
	function IT_Exchange_Product_Feature_Purchase_Buttons( $args=array() ) {
		parent::__construct( $args );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_add_edit_styles' ) );
	}

	/**
	 * Enqueue the add/edit styles
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function enqueue_add_edit_styles( $page ) {
		$current_screen = get_current_screen();
		if ( empty( $current_screen->base ) || 'post' != $current_screen->base || empty( $current_screen->post_type ) || 'it_exchange_prod' != $current_screen->post_type )
			return;

		$product_type = it_exchange_get_product_type();
		if ( ! it_exchange_product_type_supports_feature( $product_type, 'purchase-buttons' ) )
			return;

		wp_enqueue_style( 'it-exchange-addon-purchase-buttons-add-edit-product', ITUtility::get_url_from_file( dirname( dirname( __FILE__ ) ) ) . '/styles/add-edit-product.css' );
	}

	/**
	 * This echos the feature metabox.
	 *
	 * @since 1.0.0
	 * @return void
	*/
	function print_metabox( $post ) {
		// Grab the iThemes Exchange Product object from the WP $post object
		$product = it_exchange_get_product( $post );

		// Set the value of the feature for this product
		$feature_value = it_exchange_get_product_feature( $product->ID, 'purchase-buttons' );
		$buy_now       = empty( $feature_value['buy-now'] ) ? 'default' : $feature_value['buy-now'];
		$add_to_cart   = empty( $feature_value['add-to-cart'] ) ? 'default' : $feature_value['add-to-cart'];

		// Set description
		$description = __( 'Use the below settings to determine which purchase buttons are available for this product.', 'LION' );
		$description = apply_filters( 'it_exchange_product_purchase_buttons_metabox_description', $description );

		?>
			<?php if ( $description ) : ?>
				<p class="intro-description"><?php echo $description; ?></p>
			<?php endif; ?>
			<div class="it-exchange-enable-product-purchase-buttons">
				<div class="it-exchange-core-product-purchase-buttons-fields">
					<label for="it-exchange-product-purchase-buttons-disable-buy-now">
						<?php _e( 'Buy Now button settings for this product', 'LION' ); ?><br />
						<select id="it-exchange-product-purchase-buttons-buy-now" name="it-exchange-product-purchase-buttons[buy-now]"> 
							<option value="default" <?php selected( 'default', $buy_now ); ?>><?php _e( 'Use the default setting', 'LION' ); ?></option>
							<option value="always" <?php selected( 'always', $buy_now ); ?>><?php _e( 'Always display', 'LION' ); ?></option>
							<option value="never" <?php selected( 'never', $buy_now ); ?>><?php _e( 'Never display', 'LION' ); ?></option>
						</select>
					</label>
					<label for="it-exchange-product-purchase-buttons-disable-add-to-cart">
						<?php _e( 'Add to Cart button settings for this product', 'LION' ); ?><br />
						<select id="it-exchange-product-purchase-buttons-add-to-cart" name="it-exchange-product-purchase-buttons[add-to-cart]"> 
							<option value="default" <?php selected( 'default', $add_to_cart ); ?>><?php _e( 'Use the default setting', 'LION' ); ?></option>
							<option value="always" <?php selected( 'always', $add_to_cart ); ?>><?php _e( 'Always display', 'LION' ); ?></option>
							<option value="never" <?php selected( 'never', $add_to_cart ); ?>><?php _e( 'Never display', 'LION' ); ?></option>
						</select>
					</label>
				</div>
				<?php wp_nonce_field( 'it-exchange-update-product-purchase-buttons-' . get_current_user_id(), 'it-exchange-update-product-purchase-buttons' ); ?>
			</div>
		<?php
	}

	/**
	 * This saves the value
	 *
	 * @since 1.0.0
	 *
	 * @param object $post wp post object
	 * @return void
	*/
	function save_feature_on_product_save() {
		// Abort if we can't determine a product type
		if ( ! $product_type = it_exchange_get_product_type() )
			return;

		// Abort if we don't have a product ID
		$product_id = empty( $_POST['ID'] ) ? false : $_POST['ID'];
		if ( ! $product_id )
			return;

		// Abort if this product type doesn't support this feature
		if ( ! it_exchange_product_type_supports_feature( $product_type, 'purchase-buttons' ) )
			return;

		// Check nonce
		if ( empty( $_POST['it-exchange-update-product-purchase-buttons'] ) 
			|| ! wp_verify_nonce( $_POST['it-exchange-update-product-purchase-buttons'], 'it-exchange-update-product-purchase-buttons-' . get_current_user_id() ) ) {
			return;
		}

		// Save settings
		$_POST['it-exchange-product-purchase-buttons']['buy-now']     = empty( $_POST['it-exchange-product-purchase-buttons']['buy-now'] ) ? 'default' : $_POST['it-exchange-product-purchase-buttons']['buy-now'];
		$_POST['it-exchange-product-purchase-buttons']['add-to-cart'] = empty( $_POST['it-exchange-product-purchase-buttons']['add-to-cart'] ) ? 'default' : $_POST['it-exchange-product-purchase-buttons']['add-to-cart'];
		it_exchange_update_product_feature( $product_id, 'purchase-buttons', $_POST['it-exchange-product-purchase-buttons'] );
	}

	/**
	 * This updates the feature for a product
	 *
	 * @since 1.0.0
	 *
	 * @param integer $product_id the product id
	 * @param mixed $new_value the new value
	 * @return bolean
	*/
	function save_feature( $product_id, $new_value, $options=array() ) {
		update_post_meta( $product_id, '_it-exchange-product-purchase-buttons', $new_value );
	}

	/**
	 * Return the product's features
	 *
	 * @since 1.0.0
	 * @param mixed $existing the values passed in by the WP Filter API. Ignored here.
	 * @param integer product_id the WordPress post ID
	 * @return string product feature
	*/
	function get_feature( $existing, $product_id, $options=array() ) {

		if ( it_exchange_product_supports_feature( $product_id, 'purchase-buttons' ) )
			return get_post_meta( $product_id, '_it-exchange-product-purchase-buttons', true );
        return false;
	}

	/**
	 * Does the product have this feature?
	 *
	 * @since 1.0.0
	 * @param mixed $result Not used by core
	 * @param integer $product_id
	 * @return boolean
	*/
	function product_has_feature( $result, $product_id, $options=array() ) {
		// Does this product type support this feature?
		if ( false === $this->product_supports_feature( false, $product_id ) )
			return false;
		return (boolean) $this->get_feature( false, $product_id );
	}

	/**
	 * Does the product support this feature?
	 *
	 * This is different than if it has the feature, a product can
	 * support a feature but might not have the feature set.
	 *
	 * @since 1.0.0
	 * @param mixed $result Not used by core
	 * @param integer $product_id
	 * @return boolean
	*/
	function product_supports_feature( $result, $product_id, $options=array() ) {
		return true;
	}
}
$purchase_buttons_product_types = it_exchange_get_enabled_addons( array( 'category' => 'product-type' ) );
if ( isset( $purchase_buttons_product_types['invoices-product-type'] ) ) {
	unset( $purchase_buttons_product_types['invoices-product-type'] );
}
$purchase_buttons_product_feature_options = array(
	'slug'          => 'purchase-buttons',
	'metabox_title' => __( 'Purchase Buttons', 'LION' ),
	'description'   => __( 'Which purchase buttons are available for this product', 'LION' ),
	'product_types' => array_keys( $purchase_buttons_product_types ),
);
$IT_Exchange_Product_Feature_Purchase_Buttons = new IT_Exchange_Product_Feature_Purchase_Buttons( $purchase_buttons_product_feature_options ); 
