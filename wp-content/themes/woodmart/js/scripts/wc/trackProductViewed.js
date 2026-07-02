/* global woodmart_settings */

woodmartThemeModule.trackProductViewed = function() {
	if ('visible' !== document.visibilityState) {
		return;
	}

	var singleProduct = document.querySelector('.single-product-page');
	var cookiesName = 'woodmart_recently_viewed_products';
	var maxProducts = parseInt(woodmart_settings.max_recently_viewed_products, 10);

	if ( ! singleProduct || 'undefined' === typeof Cookies ) {
		return;
	}

	var singleProductID = singleProduct.id.replace('product-', '');
	var recentlyProduct = Cookies.get(cookiesName);

	if ( ! recentlyProduct ) {
		recentlyProduct = singleProductID;
	} else {
		recentlyProduct = recentlyProduct.split('|');

		var existingIndex = recentlyProduct.indexOf(singleProductID);
		if (existingIndex !== -1) {
			recentlyProduct.splice(existingIndex, 1);
		}

		recentlyProduct.unshift(singleProductID);

		if (recentlyProduct.length > maxProducts) {
			recentlyProduct = recentlyProduct.slice(0, maxProducts);
		}

		recentlyProduct = recentlyProduct.join('|');
	}

	Cookies.set(cookiesName, recentlyProduct, {
		expires: parseInt(woodmart_settings.cookie_expires, 10),
		path   : woodmart_settings.cookie_path,
		secure : woodmart_settings.cookie_secure_param
	});
};

window.addEventListener('load',function() {
	woodmartThemeModule.trackProductViewed();
});
