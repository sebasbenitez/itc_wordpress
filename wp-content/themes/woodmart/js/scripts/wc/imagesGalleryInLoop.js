/* global woodmart_settings */
(function($) {
	woodmartThemeModule.$document.on('pjax:complete wdProductsTabsLoaded wdShopPageInit wdLoadMoreLoadProducts wdArrowsLoadProducts', function() {
		woodmartThemeModule.imagesGalleryInLoop();
	});

	woodmartThemeModule.$document.on('wdRecentlyViewedProductLoaded', function() {
		$('.wd-products-element .products, .wd-carousel-container.products .wd-product')
			.each(function ( key, product ) {
				let $product = $(this);

				$product.trigger('wdImagesGalleryInLoopOn', $product);
			});
	});

	$.each([
		'frontend/element_ready/wd_products.default',
		'frontend/element_ready/wd_products_tabs.default',
		'frontend/element_ready/wd_archive_products.default',
	], function(index, value) {
		woodmartThemeModule.wdElementorAddAction(value, function() {
			woodmartThemeModule.imagesGalleryInLoop();
		});
	});

	woodmartThemeModule.imagesGalleryInLoop = function() {
		function addGalleryLoopEvents( neededProduct ) {
			$( neededProduct )
				.on('mouseover mouseout', '.wd-product-grid-slide', function( e ) {
					let $hoverSlide = $(this);
					let $product    = $hoverSlide.closest('.wd-product');

					if ( woodmartThemeModule.$window.width() <= 1024 ) {
						return;
					}

					let $imagesIndicator    = $product.find('.wd-product-grid-slider-pagin');
					let $productImage       = $product.find('.wd-product-img-link > img, .wd-product-img-link > picture > img');
					let $productImageSource = $product.find('.wd-product-img-link > picture source');
					let hoverImageUrl;
					let hoverImageSrcSet;
					let currentImagesIndicator;

					if ( 'mouseover' === e.type ) {
						let hoverSliderId      = $hoverSlide.data('image-id');
						hoverImageUrl          = $hoverSlide.data('image-url');
						hoverImageSrcSet       = $hoverSlide.data('image-srcset');
						currentImagesIndicator = $imagesIndicator.find(`[data-image-id="${hoverSliderId}"]`);
					} else {
						hoverImageUrl          = $product.find('.wd-product-grid-slide[data-image-id="0"]').data('image-url');
						hoverImageSrcSet       = $product.find('.wd-product-grid-slide[data-image-id="0"]').data('image-srcset');
						currentImagesIndicator = $imagesIndicator.find('[data-image-id="0"]');
					}

					currentImagesIndicator.siblings().removeClass('wd-active');
					currentImagesIndicator.addClass('wd-active');

					$productImage.attr('src', hoverImageUrl );

					if ( hoverImageSrcSet ) {
						$productImage.attr('srcset', hoverImageSrcSet );
						$productImageSource.attr('srcset', hoverImageSrcSet );
					} else if ( $productImage.attr('srcset' ) ) {
						$productImage.attr('srcset', null);
						$productImageSource.attr('srcset', null);
					}
				})
				.on('click', '.wd-prev, .wd-next', function( e ) {
					e.preventDefault();
					let $navButton          = $(this);
					let $product            = $navButton.closest('.wd-product');
					let $productImage       = $product.find('.wd-product-img-link > img, .wd-product-img-link > picture > img');
					let $productImageSource = $product.find('.wd-product-img-link > picture source');
					let $imagesList         = $product.find('.wd-product-grid-slide');
					let index               = $imagesList.hasClass('wd-active') ? $product.find('.wd-product-grid-slide.wd-active').data('image-id') : 0;

					if ( $(this).hasClass('wd-prev') ) {
						index--;
					} else if ( $(this).hasClass('wd-next') ) {
						index++;
					}

					if ( -1 === index ) {
						index = $imagesList.length - 1;
					} else if ( $imagesList.length === index ) {
						index = 0;
					}

					let $currentImage    = $product.find(`.wd-product-grid-slide[data-image-id="${index}"]`);
					let hoverImageUrl    = $currentImage.data('image-url');
					let hoverImageSrcSet = $currentImage.data('image-srcset');

					$imagesList.removeClass('wd-active');
					$currentImage.addClass('wd-active');

					$productImage.attr('src', hoverImageUrl )

					if ( hoverImageSrcSet ) {
						$productImage.attr('srcset', hoverImageSrcSet );
						$productImageSource.attr('srcset', hoverImageSrcSet );
					} else if ( $productImage.attr('srcset' ) ) {
						$productImage.attr('srcset', null);
						$productImageSource.attr('srcset', null);
					}
				});
		}
		function removeGalleryLoopEvents( neededProduct ) {
			$( neededProduct )
				.off( 'mouseover mouseout', '.wd-product-grid-slide' )
				.off( 'click', '.wd-prev, .wd-next' );
		}

		$('.wd-product')
			.each(function ( key, product ) {
				removeGalleryLoopEvents( product );
				addGalleryLoopEvents( product );
			});

		woodmartThemeModule.$document
			.on('wdImagesGalleryInLoopOff', '.wd-product', function( e, neededProduct = this ) {
				removeGalleryLoopEvents( neededProduct );
			})
			.on('wdImagesGalleryInLoopOn', '.wd-product', function( e, neededProduct = this ) {
				addGalleryLoopEvents( neededProduct );
			});
	};

	$(document).ready(function() {
		woodmartThemeModule.imagesGalleryInLoop();
	});
})(jQuery);
