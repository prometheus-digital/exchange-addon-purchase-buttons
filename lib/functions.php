<?php

/**
 * Turns the buy now on or off for a specefic product
 *
 * @since 1.0.0
 *
 * @return boolean
*/
function it_exchange_purchase_buttons_addon_maybe_disable_buy_now( $incoming, $product_id ) {
	if ( is_object( $product_id ) && ! empty( $product_id->ID ) )
		$product_id = $product_id->ID;

	// Abandon if the product type doesn't support this
	$this_product_type = it_exchange_get_product_type( $product_id );
	if ( ! it_exchange_product_type_supports_feature( $this_product_type, 'purchase-buttons' ) )
		return $incoming;

	// Get the product settings
	$product_settings = it_exchange_get_product_feature( $product_id, 'purchase-buttons' );

	// Return true/false if product settings is overriding globals
	if ( ! empty( $product_settings['buy-now'] ) && 'default' != $product_settings['buy-now'] ) {
		if ( 'never' == $product_settings['buy-now'] )
			return true;
		else
			return false;
	}

	// Grab global settings and return infoming if not disabling globally
	$global_settings = it_exchange_get_option( 'addon_purchase_buttons' );
	if ( empty( $global_settings['disable-buy-now'] ) )
		return $incoming;

	// Are we disabling all buy now buttons everywhere?
	if ( ! empty( $global_settings['disable-buy-now-scope'] ) && 'globally' == $global_settings['disable-buy-now-scope'] )
		return true;

	// Are we disabling all buy now buttons for this product type?
	$product_types = empty( $global_settings['disable-buy-now-for-product-type'] ) ? array() : (array) $global_settings['disable-buy-now-for-product-type'];
	if ( ! empty( $product_types[$this_product_type] ) )
		return true;
	
	// If we made it here, just return the default
	return $incoming;
}
add_filter( 'it_exchange_disable_buy_now', 'it_exchange_purchase_buttons_addon_maybe_disable_buy_now', 9, 2 );

/**
 * Turns the add to cart now on or off for a specefic product
 *
 * @since 1.0.0
 *
 * @return boolean
*/
function it_exchange_purchase_buttons_addon_maybe_disable_add_to_cart( $incoming, $product_id ) {
	if ( is_object( $product_id ) && ! empty( $product_id->ID ) )
		$product_id = $product_id->ID;

	// Abandon if the product type doesn't support this
	$this_product_type = it_exchange_get_product_type( $product_id );
	if ( ! it_exchange_product_type_supports_feature( $this_product_type, 'purchase-buttons' ) )
		return $incoming;

	// Get the product settings
	$product_settings = it_exchange_get_product_feature( $product_id, 'purchase-buttons' );

	// Return true/false if product settings is overriding globals
	if ( ! empty( $product_settings['add-to-cart'] ) && 'default' != $product_settings['add-to-cart'] ) {
		if ( 'never' == $product_settings['add-to-cart'] )
			return true;
		else
			return false;
	}

	// Grab global settings and return infoming if not disabling globally
	$global_settings = it_exchange_get_option( 'addon_purchase_buttons' );
	if ( empty( $global_settings['disable-add-to-cart'] ) )
		return $incoming;

	// Are we disabling all add to cart now buttons everywhere?
	if ( ! empty( $global_settings['disable-add-to-cart-scope'] ) && 'globally' == $global_settings['disable-add-to-cart-scope'] )
		return true;

	// Are we disabling all add to cart now buttons for this product type?
	$product_types = empty( $global_settings['disable-add-to-cart-for-product-type'] ) ? array() : (array) $global_settings['disable-add-to-cart-for-product-type'];
	if ( ! empty( $product_types[$this_product_type] ) )
		return true;
	
	// If we made it here, just return the default
	return $incoming;
}
add_filter( 'it_exchange_disable_add_to_cart', 'it_exchange_purchase_buttons_addon_maybe_disable_add_to_cart', 9, 2 );
