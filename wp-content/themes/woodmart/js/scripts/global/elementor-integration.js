/* global woodmartThemeModule, elementorFrontend, elementor, woodmart_settings */

(function($) {
	'use strict';

	woodmartThemeModule.wdElementorAddAction('frontend/element_ready/global', function($wrapper) {
		$wrapper.removeClass('wd-animation-ready wd-animated wd-in');
		woodmartThemeModule.$document.trigger('wdElementorGlobalReady');
	});

	$.each([
		'frontend/element_ready/column',
		'frontend/element_ready/container'
	], function(index, value) {
		woodmartThemeModule.wdElementorAddAction(value, function($wrapper) {
			$wrapper.removeClass('wd-animation-ready wd-animated wd-in');

			setTimeout(function() {
				woodmartThemeModule.$document.trigger('wdElementorColumnReady');
			}, 100);
		});
	});

	woodmartThemeModule.$window.on('elementor/frontend/init', function() {
		if (!elementorFrontend.isEditMode()) {
			return;
		}

		if ('enabled' === woodmart_settings.elementor_no_gap) {
			handleElementorNoGap();
		}
	});

	function handleElementorNoGap() {
		$.each([
			'frontend/element_ready/section',
			'frontend/element_ready/container'
		], function(index, value) {
			woodmartThemeModule.wdElementorAddAction(value, function($wrapper) {
				$wrapper.removeClass('wd-animation-ready wd-animated wd-in');
				woodmartThemeModule.$document.trigger('wdElementorSectionReady');
			});

			elementorFrontend.hooks.addAction(value, function($wrapper) {
				var cid = $wrapper.data('model-cid');

				if (typeof elementorFrontend.config.elements.data[cid] !== 'undefined') {
					var size = getElementSize(elementorFrontend.config.elements.data[cid]);

					if (!size) {
						$wrapper.addClass('wd-negative-gap');
					}
				}
			});
		});

		elementor.channels.editor.on('change:section change:container', function(view) {
			handleSectionChange(view);
		});
	}

	function getElementSize(elementData) {
		var size = '';

		if ('undefined' !== typeof elementData.attributes.elType) {
			if ('container' === elementData.attributes.elType) {
				if ('boxed' === elementData.attributes.content_width) {
					size = elementData.attributes.boxed_width.size;
				} else {
					size = true;
				}
			} else if ('section' === elementData.attributes.elType) {
				size = elementData.attributes.content_width.size;
			}
		}

		return size;
	}

	function handleSectionChange(view) {
		var changed = view.elementSettingsModel.changed;

		if (typeof changed.content_width === 'undefined' && typeof changed.boxed_width === 'undefined') {
			return;
		}

		var size = [];

		if ('container' === view.elementSettingsModel.attributes.elType) {
			if (typeof changed.boxed_width !== 'undefined') {
				size = changed.boxed_width.size;
			}
		} else if (typeof changed.content_width !== 'undefined') {
			size = changed.content_width.size;
		}

		var sectionId = view._parent.model.id;
		var $section = $('.elementor-element-' + sectionId);

		if (size) {
			$section.removeClass('wd-negative-gap');
		} else {
			$section.addClass('wd-negative-gap');
		}
	}
})(jQuery);