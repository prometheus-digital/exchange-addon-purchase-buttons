jQuery(document).ready(function($){
	// Toggle buy-now options
	$('#disable-buy-now', '#it-exchange-addon-purchase-buttons-settings').on('change', function() {
		if ( $(this).is(':checked') ){
			$('.disable-buy-now-settings').removeClass('ite-purchase-buttons-disabled').find(':radio').removeAttr('disabled');
		} else {
			$('.disable-buy-now-settings').addClass('ite-purchase-buttons-disabled').find(':radio').attr('disabled', true);
		}
	});
	// Toggle add-to-cart options
	$('#disable-add-to-cart', '#it-exchange-addon-purchase-buttons-settings').on('change', function() {
		if ( $(this).is(':checked') ){
			$('.disable-add-to-cart-settings').removeClass('ite-purchase-buttons-disabled').find(':radio').removeAttr('disabled');
		} else {
			$('.disable-add-to-cart-settings').addClass('ite-purchase-buttons-disabled').find(':radio').attr('disabled', true);
		}
	});

	// Toggle Buy Now product type options
	$('.disable-buy-now-scope-option', '#it-exchange-addon-purchase-buttons-settings').on('change', function() {
		if ( $(this).is(':checked') && $(this).val() == 'product-type' ) {
			$('.buy-now-product-type-scope-ul').show();
		} else {
			$('.buy-now-product-type-scope-ul').hide();
		}
	});
	// Toggle Add to Cart type options
	$('.disable-add-to-cart-scope-option', '#it-exchange-addon-purchase-buttons-settings').on('change', function() {
		if ( $(this).is(':checked') && $(this).val() == 'product-type' ) {
			$('.add-to-cart-product-type-scope-ul').show();
		} else {
			$('.add-to-cart-product-type-scope-ul').hide();
		}
	});
});
