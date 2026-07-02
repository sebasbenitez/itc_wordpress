/* global woodmart_settings, wtl_form_data */
(function($) {
	woodmartThemeModule.waitlistSubscribeForm = function() {
		function init() {
			if ('undefined' === typeof wtl_form_data) { // Disable script on Elementor edit mode.
				return;
			}

			var addToCartWrapperSelector = $('.wd-content-layout').hasClass('wd-builder-on') ? '.wd-single-add-cart' : '.summary-inner';
			var $variationsForm          = $(`${addToCartWrapperSelector} .variations_form`);

			if ($variationsForm.length) {
				var $activeVariation    = $(`.wd-single-add-cart .wd-active`);
				var variationsUpdated   = false;
				var formInited          = false;

				$variationsForm
					.on('show_variation', function(e, variation) {
						if (variation.is_in_stock) {
							var form = $(this).parent().find('.wd-wtl-form:not(.wd-wtl-is-template)');

							if (form.length) {
								form.remove();
							}

							return;
						}

						if (! variationsUpdated && wtl_form_data.global.fragments_enable && wtl_form_data.global.is_user_logged_in) {
							const variationsFormNode = this;
							const variationId = variation.variation_id;

							const afterUpdateCallback = () => {
								showVariationForm(variationsFormNode, variationId, getVariationState(variationId));
								variationsUpdated = true;
							};

							updateAjaxFormData(afterUpdateCallback);
						} else {
							showVariationForm(this, variation.variation_id, getVariationState(variation.variation_id));
						}
					})
					.on('click', '.reset_variations', function(e) {
						var $currentVariationsForm = $(e.target).closest('.variations_form');
						var $wtlForm = $currentVariationsForm.parent().find('.wd-wtl-form:not(.wd-wtl-is-template)').first();

						if ($wtlForm.length) {
							$wtlForm.remove();
						}
					});

				if (! formInited && $('.single-product-page').hasClass('has-default-attributes') && $activeVariation.length) {
					$variationsForm.trigger('reload_product_variations');
					formInited = true;
				}
			} else {
				if (wtl_form_data.hasOwnProperty('fragments_enable') && wtl_form_data.fragments_enable && wtl_form_data.is_user_logged_in) {
					updateAjaxFormData();
				}

				var $forms = $('.wd-wtl-form:not(.wd-wtl-is-template)');

				if ($forms.length) {
					$forms.on('click', formEvents);
				}
			}
		}

		function getVariationState(productId) {
			if (wtl_form_data.hasOwnProperty(productId) && wtl_form_data[productId].hasOwnProperty('state')) {
				return wtl_form_data[productId].state;
			}

			if (wtl_form_data.hasOwnProperty('state')) {
				return wtl_form_data.state;
			}

			return 'not-signed';
		}
		
		function showVariationForm(variationsForm, product_id, state = 'not-signed' ) {
			var $variationsForm = $(variationsForm);

			if (! wtl_form_data.global.is_user_logged_in) {
				var cookiesName  = 'woodmart_waitlist_unsubscribe_tokens';

				var cookieData  = Cookies.get(cookiesName) ? JSON.parse(Cookies.get(cookiesName)) : {};
				
				if (cookieData && cookieData.hasOwnProperty(product_id) ) {
					state = 'signed';
				}
			}

			var $templateForm = $(`.wd-wtl-form.wd-wtl-is-template[data-state="${state}"]`).first();

			if (! $templateForm.length || ! $variationsForm.length) {
				return;
			}

			var $oldForm = $variationsForm.parent().find('.wd-wtl-form:not(.wd-wtl-is-template)').first();
			var $cloneNode = $templateForm.clone();

			if ('not-signed' === state) {
				var emailValue = '';

				$cloneNode.find('.wd-wtl-subscribe').attr('data-product-id', product_id);

				if (wtl_form_data.hasOwnProperty('global') && wtl_form_data.global.email) {
					emailValue =  wtl_form_data.global.email;
				} else if (wtl_form_data.hasOwnProperty('email')) {
					emailValue = wtl_form_data.email;
				}

				$cloneNode.find('[name="wd-wtl-user-subscribe-email"]').val(emailValue);

				$cloneNode.on('click', subscribe);
			} else {
				$cloneNode.find('.wd-wtl-unsubscribe').attr('data-product-id', product_id);

				$cloneNode.on('click', unsubscribe);
			}

			$cloneNode.find('[for$="-tmpl"]').each(function() {
				var $node = $(this);

				$node.attr('for', $node.attr('for').replace('-tmpl', ''));
			});

			$cloneNode.find('[id$="-tmpl"]').each(function() {
				var $node = $(this);

				$node.attr('id', $node.attr('id').replace('-tmpl', ''));
			});

			$cloneNode.removeClass('wd-wtl-is-template wd-hide');

			if ($oldForm.length) {
				$oldForm.replaceWith($cloneNode);
			} else {
				$variationsForm.after($cloneNode);
			}

			if (wtl_form_data.hasOwnProperty(product_id)) {
				wtl_form_data[product_id].state = state;
			} else if (wtl_form_data.hasOwnProperty('product_id')) {
				wtl_form_data.product_id = state;
			}

			return $cloneNode;
		}

		function updateAjaxFormData(afterUpdateCallback = null) {
			var productId = getCurrentProductId();

			if (! productId) {
				return null;
			}

			activeLoading();

			jQuery.ajax({
				url     : woodmart_settings.ajaxurl,
				data    : {
					action     : 'woodmart_update_form_data',
					product_id : productId,
				},
				dataType: 'json',
				method  : 'GET',
				success : function(response) {
					if (response.hasOwnProperty('data')) {
						if (response.data.hasOwnProperty('global')) {
							wtl_form_data.global = response.data.global;
						}

						if (response.data.hasOwnProperty('signed_ids')) {
							response.data.signed_ids.forEach(function(signedProdutId) {
								if (wtl_form_data.hasOwnProperty(signedProdutId)) {
									wtl_form_data[signedProdutId].state = 'signed';
								} else if (wtl_form_data.hasOwnProperty('state')) {
									wtl_form_data.state = 'signed';
								}
							});
						}

						if ( 'function' === typeof afterUpdateCallback ) {
							afterUpdateCallback();
						}
					}
				},
				error   : function() {
					console.error('Something wrong with AJAX response. Probably some PHP conflict');
				},
				complete: function() {
					stopLoading();
				}
			});
		}

		function formEvents(e) {
			var $target = $(e.target);
			var $subscribeBtn   = $target.closest('.wd-wtl-subscribe');
			var $unsubscribeBtn = $target.closest('.wd-wtl-unsubscribe');

			if ($subscribeBtn.length) {
				subscribe(e);
			} else if ($unsubscribeBtn.length) {
				unsubscribe(e);
			}
		}

		function subscribe(e) {
			var $actionBtn = $(e.target).closest('.wd-wtl-subscribe');

			if (! $actionBtn.length) {
				return;
			}

			e.preventDefault();

			var $subscribeForm    = $actionBtn.closest('.wd-wtl-form');
			var $policyCheckInput = $subscribeForm.find('[name="wd-wtl-policy-check"]');
			var $userEmailInput   = $subscribeForm.find('[name="wd-wtl-user-subscribe-email"]');
			var userEmail         = $userEmailInput.length ? $userEmailInput.val() : '';

			var data = {
				action     : 'woodmart_add_to_waitlist',
				user_email : userEmail,
				product_id : $actionBtn.data('productId'),
			};

			if ($policyCheckInput.length && ! $policyCheckInput.prop('checked')) {
				var noticeValue = '';

				if (wtl_form_data.hasOwnProperty('global') && wtl_form_data.global.policy_check_notice) {
					noticeValue =  wtl_form_data.global.policy_check_notice;
				} else if (wtl_form_data.hasOwnProperty('policy_check_notice')) {
					noticeValue = wtl_form_data.policy_check_notice;
				}

				if ( ! noticeValue ) {
					return;
				}

				addNotice($subscribeForm, noticeValue, 'warning');
				return;
			}

			sendForm($subscribeForm, data);
		}

		function unsubscribe(e) {
			var $actionBtn = $(e.target).closest('.wd-wtl-unsubscribe');

			if (! $actionBtn.length) {
				return;
			}

			e.preventDefault();

			var cookiesName  = 'woodmart_waitlist_unsubscribe_tokens';
			var $subscribeForm = $actionBtn.closest('.wd-wtl-form');

			var data = {
				action     : 'woodmart_remove_from_waitlist',
				product_id : $actionBtn.data('productId'),
			};

			var productId   = parseInt(data.product_id, 10);
			var cookieData  = Cookies.get(cookiesName) ? JSON.parse(Cookies.get(cookiesName)) : {};
			
			if (cookieData && cookieData.hasOwnProperty(productId) ) {
				data['unsubscribe_token'] = cookieData[productId];
			}

			sendForm($subscribeForm, data);
		}

		function sendForm(subscribeForm, data) {
			var $subscribeForm = $(subscribeForm);

			activeLoading();

			jQuery.ajax({
				url     : woodmart_settings.ajaxurl,
				data,
				method  : 'POST',
				success : function(response) {
					if (!response) {
						return;
					}

					if (response.success) {
						var $variationsForm = $subscribeForm.parent().find('.variations_form').first();

						if ($variationsForm.length) {
							showVariationForm($variationsForm, data.product_id, response.data.state);

							$subscribeForm = $variationsForm.parent().find('.wd-wtl-form:not(.wd-wtl-is-template)').first();
						} else if (response.data.hasOwnProperty('content')) {
							showSimpleForm(response.data.content);
						}
					}

					if (response.data.hasOwnProperty('notice')) {
						var notice_type = ! response.success ? 'warning' : 'success';

						if ( response.data.hasOwnProperty('notice_status') ) {
							notice_type = response.data.notice_status;
						}

						addNotice($subscribeForm, response.data.notice, notice_type);
					}
				},
				error   : function() {
					console.error('ajax adding to waitlist error');
				},
				complete: function() {
					stopLoading();
				}
			});
		}

		function showSimpleForm(content) {
			var $forms = $('.wd-wtl-form:not(.wd-wtl-is-template)').filter(function() {
				return ! $(this).closest('.wd-sticky-spacer').length;
			});

			var $tempDiv = $('<div>').html(content);
			var $tempForm = $tempDiv.find('.wd-wtl-form').first();

			$forms.each(function() {
				var $form = $(this);
				var $cloneForm = $tempForm.clone();

				$form.empty().append($cloneForm.contents());
			});
		}

		function getCurrentProductId() {
			var bodyClasses = $('body').attr('class') || '';
			var matches = bodyClasses.match(/(?:^|\s)postid-([^\s]+)/);

			return matches ? matches[1] : false;
		}

		function addNotice(subscribeForm, message, status) {
			var $subscribeForm = $(subscribeForm);

			if (! $subscribeForm.length) {
				return;
			}

			$subscribeForm.find('.wd-notice').first().remove();

			$('<div>')
				.addClass(`wd-notice wd-${status}`)
				.append(message)
				.appendTo($subscribeForm);
		}
		
		function getOverlays() {
			return $('.wd-wtl-form:not(.wd-hide) .wd-loader-overlay');
		}

		function activeLoading() {
			getOverlays().addClass('wd-loading');
		}

		function stopLoading() {
			getOverlays().removeClass('wd-loading');
		}

		init();
	}

	$(document).ready(function() {
		woodmartThemeModule.waitlistSubscribeForm();
	});
})(jQuery);
