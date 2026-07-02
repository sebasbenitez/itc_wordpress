/* global woodmart_settings, woodmartThemeModule */

(function($) {
	'use strict';

	woodmartThemeModule.preloader = function() {
		var preloaderDelay = parseInt(woodmart_settings.preloader_delay, 10);

		$('.wd-preloader').delay(preloaderDelay).addClass('preloader-hide');
		$('.wd-preloader-style').remove();

		setTimeout(function() {
			$('.wd-preloader').remove();
		}, 200);
	};

	woodmartThemeModule.$window.on('load', function() {
		woodmartThemeModule.preloader();
	});
})(jQuery);