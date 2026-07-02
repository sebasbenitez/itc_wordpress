/* global woodmart_settings */
(function($) {
	woodmartThemeModule.$document.on('wdQuickViewOpen', function () {
		woodmartThemeModule.variationsPrice();
	});

	$.each([
		'frontend/element_ready/wd_single_product_add_to_cart.default',
	], function(index, value) {
		woodmartThemeModule.wdElementorAddAction(value, function() {
			woodmartThemeModule.variationsPrice();
		});
	});

	woodmartThemeModule.variationsPrice = function() {
		if ('no' === woodmart_settings.single_product_variations_price) {
			return;
		}

		$('.variations_form').each(function() {
			var $form = $(this);
			var isQuickView = $form.parents('.product-quick-view').length;

			var getPrice = function() {
				if ( $('.wd-content-layout').hasClass('wd-builder-on') && ! isQuickView ) {
					return $form.parents('.single-product-page').find('.wd-single-price .price:not(.price-unit)').first();
				}

				return $form.parent().find('> .price:not(.price-unit), > div > .price:not(.price-unit)').first();
			};

			var $price = getPrice();
			var $priceOriginal = $price.clone();

			$form.on('found_variation', function(e, variation) {
				if (variation.price_html.length > 1) {
					$price.replaceWith(variation.price_html);
					$price = getPrice();
				}
			});

			$form.on('reset_data', function() {
				$price.replaceWith($priceOriginal.clone());
				$price = getPrice();
			});
		});
	};

	$(document).ready(function() {
		woodmartThemeModule.variationsPrice();
	});
})(jQuery);
