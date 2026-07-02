/* global woodmart_page_css, elementorFrontend */

var woodmartThemeModule = {};

(function($) {
	'use strict';

	woodmartThemeModule.supports_html5_storage = false;

	try {
		woodmartThemeModule.supports_html5_storage = ('sessionStorage' in window && window.sessionStorage !== null);
		window.sessionStorage.setItem('wd', 'test');
		window.sessionStorage.removeItem('wd');
	} catch (err) {
		woodmartThemeModule.supports_html5_storage = false;
	}

	woodmartThemeModule.$window = $(window);
	woodmartThemeModule.$document = $(document);
	woodmartThemeModule.$body = $('body');
	woodmartThemeModule.windowWidth = woodmartThemeModule.$window.width();

	woodmartThemeModule.removeDuplicatedStylesFromHTML = function(html, callback, holder = '') {
		var $data = $('<div class="temp-wrapper"></div>').append(html);
		var $head = $('head');
		var $links = $data.find('link');
		var counter = 0;
		var timeout = false;
		var extraLinksToLoad = 0;

		if (holder) {
			holder.find('link').each(function() {
				extraLinksToLoad++;

				var $clone = $(this).clone();

				$clone.on('load error', function() {
					counter++;

					checkFinish();
				});

				$head.append($clone);
			});
		}

		if (0 === $links.length && 0 === extraLinksToLoad) {
			callback(html);
			return;
		}

		function checkFinish() {
			var total = $links.length + extraLinksToLoad;

			if (counter >= total && !timeout) {
				callback($($data.html()));
				timeout = true;
			}
		}

		$links.each(function() {
			if (
				'undefined' !== typeof $(this).attr('id') &&
				$(this).attr('id').indexOf('theme_settings_') !== -1
			) {
				$head.find('link[id*="theme_settings_"]:not([id*="theme_settings_default"])').remove();
			}
		});

		$links.each(function() {
			var $link = $(this);
			var id = $link.attr('id');
			var href = $link.attr('href');

			if ('undefined' === typeof id) {
				counter++;
				checkFinish();
				return;
			}

			var isThemeSettings = id.indexOf('theme_settings_') !== -1;
			var isThemeSettingsDefault = id.indexOf('theme_settings_default') !== -1;

			$link.remove();

			if ('undefined' === typeof woodmart_page_css[id] && !isThemeSettingsDefault) {
				if (!isThemeSettings) {
					woodmart_page_css[id] = href;
				}

				$head.append(
					$link.on('load error', function() {
						counter++;
						checkFinish();
					})
				);
			} else {
				counter++;
				checkFinish();
			}
		});
	};

	woodmartThemeModule.debounce = function(func, wait, immediate) {
		var timeout;
		return function() {
			var context = this;
			var args = arguments;
			var later = function() {
				timeout = null;

				if (!immediate) {
					func.apply(context, args);
				}
			};
			var callNow = immediate && !timeout;

			clearTimeout(timeout);
			timeout = setTimeout(later, wait);

			if (callNow) {
				func.apply(context, args);
			}
		};
	};

	woodmartThemeModule.wdElementorAddAction = function(name, callback) {
		woodmartThemeModule.$window.on('elementor/frontend/init', function() {
			if (!elementorFrontend.isEditMode()) {
				return;
			}

			elementorFrontend.hooks.addAction(name, callback);
		});
	};

	woodmartThemeModule.slideUp = function(target, duration) {
		duration = duration || 400;

		target.style.transitionProperty = 'height, margin, padding';
		target.style.transitionDuration = duration + 'ms';
		target.style.boxSizing = 'border-box';
		target.style.height = target.offsetHeight + 'px';
		window.getComputedStyle(target).height;
		target.style.overflow = 'hidden';
		target.style.height = 0;
		target.style.paddingTop = 0;
		target.style.paddingBottom = 0;
		target.style.marginTop = 0;
		target.style.marginBottom = 0;

		window.setTimeout(function() {
			target.style.display = 'none';
			target.style.removeProperty('height');
			target.style.removeProperty('padding-top');
			target.style.removeProperty('padding-bottom');
			target.style.removeProperty('margin-top');
			target.style.removeProperty('margin-bottom');
			target.style.removeProperty('overflow');
			target.style.removeProperty('transition-duration');
			target.style.removeProperty('transition-property');
		}, duration);
	};

	woodmartThemeModule.slideDown = function(target, duration) {
		duration = duration || 400;

		target.style.removeProperty('display');
		var display = window.getComputedStyle(target).display;

		if ('none' === display) {
			display = 'block';
		}

		target.style.display = display;
		var height = target.offsetHeight;
		target.style.overflow = 'hidden';
		target.style.height = 0;
		target.style.paddingTop = 0;
		target.style.paddingBottom = 0;
		target.style.marginTop = 0;
		target.style.marginBottom = 0;
		window.getComputedStyle(target).height;
		target.style.boxSizing = 'border-box';
		target.style.transitionProperty = 'height, margin, padding';
		target.style.transitionDuration = duration + 'ms';
		target.style.height = height + 'px';
		target.style.removeProperty('padding-top');
		target.style.removeProperty('padding-bottom');
		target.style.removeProperty('margin-top');
		target.style.removeProperty('margin-bottom');

		window.setTimeout(function() {
			target.style.removeProperty('height');
			target.style.removeProperty('overflow');
			target.style.removeProperty('transition-duration');
			target.style.removeProperty('transition-property');
		}, duration);
	};

	woodmartThemeModule.googleMapsCallback = function() {
		return '';
	};

	var previouslyFocused = null;

	woodmartThemeModule.$document
		.on('wdOpenSide wdOpenSearch', '.wd-side-hidden, .wd-fs-menu, [class*=wd-search-full-screen]', function() {
			var side = $(this);

			previouslyFocused = document.activeElement;

			if (!side.attr('tabindex')) {
				side.attr('tabindex', '-1');
			}

			side.trigger('focus');

			$(document).on('focusin.wd', function(e) {
				if (e.target !== side[0] && !side[0].contains(e.target) && !$(e.target).closest('.mfp-wrap').length) {
					side.trigger('focus');
				}
			});
		})
		.on('wdCloseSide wdCloseSearch', '.wd-side-hidden, .wd-fs-menu, [class*=wd-search-full-screen]', function() {
			$(document).off('focusin.wd');

			if (previouslyFocused && document.contains(previouslyFocused)) {
				if (previouslyFocused.closest('.wd-quick-shop, .wd-quick-shop-2')) {
					previouslyFocused = previouslyFocused.closest('.wd-product').querySelector('.wd-product-img-link');
				}

				$(previouslyFocused).trigger('focus');
				previouslyFocused = null;
			}
		});

	woodmartThemeModule.$document.on('keyup', '.wd-role-btn[tabindex]', function(e) {
		if (e.which === 13) {
			$(this).trigger('click');
			e.preventDefault();
		}
	});
})(jQuery);

window.addEventListener('load', function() {
	var events = [
		'keydown',
		'scroll',
		'mouseover',
		'touchmove',
		'touchstart',
		'mousedown',
		'mousemove'
	];

	var triggerListener = function() {
		window.dispatchEvent(new CustomEvent('wdEventStarted'));
		removeListener();
	};

	var removeListener = function() {
		events.forEach(function(eventName) {
			window.removeEventListener(eventName, triggerListener);
		});
	};

	var addListener = function(eventName) {
		window.addEventListener(eventName, triggerListener);
	};

	setTimeout(function() {
		events.forEach(function(eventName) {
			addListener(eventName);
		});
	}, 100);
});