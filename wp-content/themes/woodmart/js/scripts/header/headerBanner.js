/* global woodmart_settings */
(function($) {
	woodmartThemeModule.headerBanner = function() {
		var banner_version = woodmart_settings.header_banner_version;

		if ( typeof Cookies === 'undefined' ) {
			return;
		}

		if ('closed' === Cookies.get('woodmart_tb_banner_' + banner_version) || 'no' === woodmart_settings.header_banner_close_btn || 'no' === woodmart_settings.header_banner_enabled) {
			return;
		}

		$banner = $('.wd-hb-wrapp');

		if (!woodmartThemeModule.$body.hasClass('page-template-maintenance') && $banner.length > 0) {
			$banner.addClass('wd-display');
		}

		$banner.on('click', '.wd-hb-close', function(e) {
			e.preventDefault();

			$thisBanner = $(this).closest('.wd-hb-wrapp');

			$thisBanner.removeClass('wd-display');

			Cookies.set('woodmart_tb_banner_' + banner_version, 'closed', {
				expires: parseInt(woodmart_settings.banner_version_cookie_expires),
				path   : '/',
				secure : woodmart_settings.cookie_secure_param
			});
		});
	};

	$(document).ready(function() {
		woodmartThemeModule.headerBanner();
	});
})(jQuery);
