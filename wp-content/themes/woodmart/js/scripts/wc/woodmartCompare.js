/* global woodmart_settings */
(function($) {
	woodmartThemeModule.$document.on('wdShopPageInit wdUpdateWishlist wdArrowsLoadProducts wdLoadMoreLoadProducts wdProductsTabsLoaded wdSearchFullScreenContentLoaded wdBackHistory wdRecentlyViewedProductLoaded', function() {
		woodmartThemeModule.woodmartCompare();
	});

	woodmartThemeModule.woodmartCompare = function() {
		var $body         = woodmartThemeModule.$body;
		var cookiesName   = 'woodmart_compare_list';
		var compareCookie = '';

		function init() {
			if (woodmart_settings.is_multisite) {
				cookiesName += '_' + woodmart_settings.current_blog_id;
			}

			if ( typeof Cookies === 'undefined' ) {
				return;
			}

			compareCookie = Cookies.get(cookiesName);

			updateState();
			widgetElements();

			$body.off('.wdCompare');

			$body.on('click.wdCompare', '.wd-compare-btn a', addProductHandler);
			$body.on('click.wdCompare', '.wd-compare-remove', removeProductFromComparePageHandler);
			$body.on('change.wdCompare', '.wd-compare-select', productCategoryChangeHandler);
			$body.on('click.wdCompare', '.wd-compare-remove-cat', removeProductCategoryHandler);
		}

		function updateState() {
			if (
				'undefined' === typeof woodmart_settings.compare_save_button_state ||
				'yes' !== woodmart_settings.compare_save_button_state ||
				'undefined' === typeof Cookies
			) {
				return;
			}

			var products = compareCookie ? Object.values( JSON.parse(compareCookie) ) : [];
			var $buttons = products.length ? $(products.map(id => `.wd-compare-btn a[data-id='${id}']`).join(', ')) : [];

			if ( ! $buttons.length ) {
				return;
			}

			$.each($buttons, function( index, button ) {
				var $button = $(button);

				if ( ! $button.length || $button.hasClass('added') ) {
					return;
				}

				$button.addClass('added');

				if ($button.find('.wd-action-text').length > 0) {
					$button.find('.wd-action-text').text(woodmart_settings.compare_removed_button_text);
				} else {
					$button.text(woodmart_settings.compare_removed_button_text);
				}

				$button
					.off('click.wdCompareSaved')
					.on('click.wdCompareSaved', removeProductFromSavedStateHandler);

				woodmartThemeModule.$document.trigger('wdUpdateTooltip', $button);
			});
		}

		function widgetElements() {
			var $widget = $('.wd-header-compare');

			if ($widget.length <= 0) {
				return;
			}

			if ('undefined' !== typeof compareCookie) {
				try {
					var ids = JSON.parse(compareCookie);
					$widget.find('.wd-tools-count').text(ids.length);
				}
				catch (e) {
					console.log('cant parse cookies json');
				}
			} else {
				$widget.find('.wd-tools-count').text(0);
			}

			if ( 'undefined' !== typeof woodmart_settings.compare_by_category && 'yes' === woodmart_settings.compare_by_category ) {
				try {
					getProductsCategory();
				}
				catch (e) {
					getAjaxProductCategory();
				}
			}
		}

		function addProductHandler(e) {
			var $this    = $(this);
			var id       = $this.data('id');
			var $buttons = $(`.wd-compare-btn a[data-id='${id}']`);
			var $widget  = $('.wd-header-compare');

			if ($buttons.hasClass('added')) {
				return true;
			}

			e.preventDefault();

			if ( ! $widget.find('.wd-dropdown-compare').length ) {
				var products = [];
				var productsCookies = Cookies.get(cookiesName);

				if ( 'undefined' !== typeof productsCookies && productsCookies ) {
					products = Object.values( JSON.parse(productsCookies) );
				}

				if ( ! products.length || -1 === products.indexOf(id.toString()) ) {
					products.push( id.toString() );
				}

				var count = products.length;

				updateCountWidget(count);

				Cookies.set(cookiesName, JSON.stringify(products), {
					expires: parseInt(woodmart_settings.cookie_expires),
					path   : woodmart_settings.cookie_path,
					secure : woodmart_settings.cookie_secure_param
				});

				updateButton( $buttons );

				return;
			}

			$this.addClass('loading');

			jQuery.ajax({
				url     : woodmart_settings.ajaxurl,
				data    : {
					action: 'woodmart_add_to_compare',
					id    : id
				},
				dataType: 'json',
				method  : 'GET',
				success : function(response) {
					if ( response.count ) {
						var $widget = $('.wd-header-compare');

						if ($widget.length > 0) {
							$widget.find('.wd-tools-count').text(response.count);
						}

						updateButton( $buttons );
					} else {
						console.log('something wrong loading compare data ', response);
					}

					if (response.fragments) {
						$.each( response.fragments, function( key, value ) {
							$( key ).replaceWith(value);
						});

						sessionStorage.setItem( cookiesName + '_fragments', JSON.stringify( response.fragments ) );
					}
				},
				error   : function() {
					console.log('We cant add to compare. Something wrong with AJAX response. Probably some PHP conflict.');
				},
				complete: function() {
					$buttons.removeClass('loading');
				}
			});
		}

		function removeProductFromSavedStateHandler(e) {
			e.stopImmediatePropagation();
			e.preventDefault();

			var $this    = $(this);
			var productId = $this.data('id').toString();
			var $buttons  = $(`.wd-compare-btn a[data-id='${productId}']`);

			var currentProducts = [];
			if ( compareCookie ) {
				currentProducts = Object.values( JSON.parse(compareCookie) );
			}

			currentProducts = currentProducts.filter(function(number) {
				return number !== productId;
			});

			Cookies.set(cookiesName, JSON.stringify(currentProducts), {
				expires: parseInt(woodmart_settings.cookie_expires),
				path   : woodmart_settings.cookie_path,
				secure : woodmart_settings.cookie_secure_param
			});

			compareCookie = Cookies.get(cookiesName);

			$buttons.removeClass('added');

			if ($buttons.find('.wd-action-text').length > 0) {
				$buttons.find('.wd-action-text').text(woodmart_settings.compare_origin_button_text);
			} else {
				$buttons.text(woodmart_settings.compare_origin_button_text);
			}

			$buttons.off('click', removeProductFromSavedStateHandler);
			$buttons.on('click', addProductHandler);

			woodmartThemeModule.$document.trigger('wdUpdateTooltip', $buttons);

			updateCountWidget(currentProducts.length);
		}

		function removeProductFromComparePageHandler(e) {
			e.preventDefault();
			var $this      = $(this),
			    id         = $this.data('id'),
				categoryId = '';

			if ('undefined' !== typeof woodmart_settings.compare_by_category && 'yes' === woodmart_settings.compare_by_category) {
				categoryId = $this.parents('.wd-compare-table').data('category-id');

				if ( categoryId && 1 >= $this.parents('.compare-value').siblings().length ) {
					removeProductCategory( categoryId, $this.parents('.wd-compare-page') );
					return;
				}
			}

			$this.addClass('loading');

			jQuery.ajax({
				url     : woodmart_settings.ajaxurl,
				data    : {
					action     : 'woodmart_remove_from_compare',
					id         : id,
					category_id: categoryId,
					key        : woodmart_settings.compare_page_nonce,
				},
				dataType: 'json',
				method  : 'GET',
				success : function(response) {
					if (response.table) {
						updateCompare(response);

						if (response.fragments) {
							$.each( response.fragments, function( key, value ) {
								$( key ).replaceWith(value);
							});

							sessionStorage.setItem( cookiesName + '_fragments', JSON.stringify( response.fragments ) );
						}
					} else {
						console.log('something wrong loading compare data ', response);
					}
				},
				error   : function() {
					console.log('We cant remove product compare. Something wrong with AJAX response. Probably some PHP conflict.');
				},
				complete: function() {
					$this.removeClass('loading');
				}
			});
		}

		function productCategoryChangeHandler(e) {
			e.preventDefault();

			var $this = $(this);
			var $wrapper = $this.parents('.wd-compare-page');
			var $activeCompareTable = $wrapper.find('.wd-compare-table[data-category-id=' + $this.val() + ']');
			var $oldActiveCompareTable = $wrapper.find('.wd-compare-table.wd-active');
			var animationTime = 100;

			$wrapper.find('.wd-compare-cat-link').attr( 'href', $activeCompareTable.data('category-url') );

			$oldActiveCompareTable.removeClass('wd-in');

			setTimeout(function() {
				$oldActiveCompareTable.removeClass('wd-active');
			}, animationTime);

			setTimeout(function() {
				$activeCompareTable.addClass('wd-active');
			}, animationTime);

			setTimeout(function() {
				$activeCompareTable.addClass('wd-in');
				woodmartThemeModule.$document.trigger('wood-images-loaded');
			}, animationTime * 2);
		}

		function removeProductCategoryHandler(e) {
			e.preventDefault();

			var $this = $(this);
			var activeCategory = $this.parents('.wd-compare-header').find('.wd-compare-select').val();
			var $wrapper = $this.parents('.wd-compare-page');

			removeProductCategory( activeCategory, $wrapper );
		}

		function removeProductCategory( activeCategory, $wrapper ) {
			var $loader = $wrapper.find('.wd-loader-overlay');

			$loader.addClass('wd-loading');

			jQuery.ajax({
				url     : woodmart_settings.ajaxurl,
				data    : {
					action     : 'woodmart_remove_category_from_compare',
					category_id: activeCategory,
					key        : woodmart_settings.compare_page_nonce,
				},
				dataType: 'json',
				method  : 'GET',
				success : function(response) {
					if (response.table) {
						updateCompare(response);

						if (response.fragments) {
							$.each( response.fragments, function( key, value ) {
								$( key ).replaceWith(value);
							});

							sessionStorage.setItem( cookiesName + '_fragments', JSON.stringify( response.fragments ) );
						}
					} else {
						console.log('something wrong loading compare data ', response);
					}
				},
				error   : function() {
					console.log('We cant remove product compare. Something wrong with AJAX response. Probably some PHP conflict.');
				},
				complete: function() {
					$loader.removeClass('wd-loading');

					var $compareTable = $('.wd-compare-table').first();

					setTimeout(function() {
						$compareTable.addClass('wd-active');
					}, 100);

					setTimeout(function() {
						$compareTable.addClass('wd-in');
						woodmartThemeModule.$document.trigger('wood-images-loaded');
					}, 200);
				}
			});
		}

		function updateCompare(data) {
			var $widget = $('.wd-header-compare');
			var $wcCompareTemplate = $('.wd-compare-table, .wd-compare-page');

			if ($widget.length > 0) {
				$widget.find('.wd-tools-count').text(data.count);
			}

			woodmartThemeModule.removeDuplicatedStylesFromHTML(data.table, function(html) {
				$wcCompareTemplate.replaceWith(html);
			}, $wcCompareTemplate);

			if ('undefined' !== typeof woodmart_settings.compare_by_category && 'yes' === woodmart_settings.compare_by_category) {
				woodmartThemeModule.$document.trigger('wdTabsInit');
			}
		}

		function getProductsCategory() {
			if ( woodmartThemeModule.supports_html5_storage ) {
				var fragmentProductCategory = JSON.parse( sessionStorage.getItem( cookiesName + '_fragments' ) );

				// eslint-disable-next-line no-undef -- `actions` is localized variable from WPML plugin.
				if ( 'undefined' !== typeof actions && ( actions.is_lang_switched === '1' || actions.force_reset === '1' ) ) {
					fragmentProductCategory = '';
				}

				if ( fragmentProductCategory ) {
					$.each( fragmentProductCategory, function( key, value ) {
						$( key ).replaceWith(value);
					});
				} else {
					getAjaxProductCategory();
				}
			} else {
				getAjaxProductCategory();
			}
		}

		function getAjaxProductCategory() {
			jQuery.ajax({
				url     : woodmart_settings.ajaxurl,
				data    : {
					action : 'woodmart_get_fragment_product_category_compare',
				},
				dataType: 'json',
				method  : 'GET',
				success : function(response) {
					if (response.fragments) {
						$.each( response.fragments, function( key, value ) {
							$( key ).replaceWith(value);
						});

						sessionStorage.setItem( cookiesName + '_fragments', JSON.stringify( response.fragments ) );
					} else {
						console.log('something wrong loading compare data ', response);
					}
				},
				error   : function() {
					console.log('We cant remove product compare. Something wrong with AJAX response. Probably some PHP conflict.');
				},
			});
		}

		function updateButton( $button ) {
			var addedText = woodmart_settings.compare_added_button_text;

			if ($button.find('.wd-action-text').length > 0) {
				$button.find('.wd-action-text').text(addedText);
			} else {
				$button.text(addedText);
			}

			$button.addClass('added');

			woodmartThemeModule.$document.trigger('added_to_compare');
			woodmartThemeModule.$document.trigger('wdUpdateTooltip', $button);
		}

		function updateCountWidget(count) {
			var $widget = $('.wd-header-compare');

			if ($widget.length > 0) {
				$widget.find('.wd-tools-count').text(count);
			}
		}

		init();
	};

	$(document).ready(function() {
		woodmartThemeModule.woodmartCompare();
	});
})(jQuery);
