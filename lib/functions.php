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

	if ( ! it_exchange_product_supports_feature( $product_id, 'purchase-buttons' ) )
		return $incoming;

	$product_settings = it_exchange_get_product_feature( $product_id, 'purchase-buttons' );

	if ( ! empty( $product_settings['buy-now'] ) && 'default' != $product_settings['buy-now'] ) {
		if ( 'never' == $product_settings['buy-now'] )
			return true;
		else
			return false;
	}
}
add_filter( 'it_exchange_disable_buy_now', 'it_exchange_purchase_buttons_addon_maybe_disable_buy_now', 10, 2 );
