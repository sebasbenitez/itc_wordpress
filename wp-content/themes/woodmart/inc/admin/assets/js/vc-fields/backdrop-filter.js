(function($) {
	$('#vc_ui-panel-edit-element').on('vcPanel.shown', function() {
		let init = function() {
			$('.wpb_el_type_wd_backdrop_filter').each(function() {
				let $backdropControl = $(this);

				initSliders($backdropControl);

				$backdropControl
					.on('click', '.xts-backdrop-filter-opener', openerButtonHandler)
					.on('click', '.xts-backdrop-filter-reset', resetButtonHandler)
					.on('click', '.xts-backdrop-filter-content .xts-backdrop-preset', presetButtonHandler)
					.on('change', '.wd-slider-value-preview', previewInputChangeHandler)
					.on('change', '.xts-backdrop-filter-content .xts-backdrop-filter .xts-dropdown-control', changeSliderHandler);
			});
		}

		let initSliders = function($backdropControl) {
			let $sliderControls = $backdropControl.find('.xts-backdrop-filter-content .xts-backdrop-filter .xts-dropdown-control');

			if (0 === $sliderControls.length) {
				return;
			}

			$sliderControls.each(function() {
				let $sliderControl = $(this);
				let $slider        = $sliderControl.find('.wd-slider-field');
				let $valueInput    = $sliderControl.find('.wd-slider-field-value');
				let $previewInput  = $sliderControl.find('.wd-slider-value-preview');
				let initialValue   = parseFloat($valueInput.data('default-value')) || 0;
				let currentValue   = '' !== $valueInput.val() ? parseFloat( $valueInput.val() ) : initialValue;

				if ('' === $valueInput.val()) {
					$valueInput.val(currentValue);
					$previewInput.val(currentValue);
				}

				$slider.slider({
					range: 'min',
					value: currentValue,
					min: parseFloat( $valueInput.attr('min') ),
					max: parseFloat( $valueInput.attr('max') ),
					step: parseFloat( $valueInput.attr('step') ) || 1,
					slide: function(event, ui) {
						$valueInput.val(ui.value);
						$previewInput.val(ui.value);

						$sliderControl.trigger('change');
					}
				});
			});
		}

		let openerButtonHandler = function(e) {
			e.preventDefault();

			let $opener  = $(this);
			let $content = $opener.siblings('.xts-backdrop-filter-content')

			$opener.addClass('xts-changed');

			$content.toggleClass('xts-hidden');

			function closeHandler(e) {
				if (
					! $content.is(e.target) &&
					0 === $content.has(e.target).length &&
					! $opener.is(e.target) && 
					! $('.vc_ui-panel-content-container').is(e.target)
				) {
					$content.addClass('xts-hidden');
					$(document).off('mouseup', closeHandler);
				}
			}

			$(document).on('mouseup', closeHandler);
		}

		let resetButtonHandler = function () {
			let $resetButton  = $(this);
			let $content      = $resetButton.parent();

			resetSlidersValues($content.find('.xts-dropdown-control'));

			$content.siblings('.xts-backdrop-filter-value').val('');
			$content.siblings('.xts-backdrop-filter-opener').removeClass('xts-changed');
			$content.addClass('xts-hidden');
		}

		let resetSlidersValues = function($sliderControls) {
			$sliderControls.each(function() {
				let $sliderControl = $(this);
				let $valueInput    = $sliderControl.find('.wd-slider-field-value');
				let defaultValue   = $valueInput.data('default-value');
				let initialValue   = 0;

				if ( defaultValue && '' !== defaultValue ) {
					initialValue = parseFloat(defaultValue);
				}

				updateSliderValue($sliderControl, initialValue);
			});
		}

		let presetButtonHandler = function () {
			let $presetButton    = $(this);
			let $backdropControl = $presetButton.closest('.wpb_el_type_wd_backdrop_filter');
			let presetValues     = getPresetValues($presetButton);

			Object.entries(presetValues).forEach(function([key, presetValue]) {
				let $sliderControl = $backdropControl.find(`.xts-backdrop-filter-content .xts-backdrop-filter .xts-dropdown-control [name="${key}"]`).closest('.xts-dropdown-control');

				if (0 === $sliderControl.length) {
					return;
				}

				let value = '' !== presetValue ? parseFloat(presetValue) : '';

				updateSliderValue($sliderControl, value);
				$sliderControl.trigger('change');
			});
		}

		let previewInputChangeHandler = function(e) {
			let $this = $(e.target);
			let $sliderControl = $this.closest('.xts-dropdown-control');

			let value = '' !== $this.val() ? parseFloat($this.val()) : '';

			updateSliderValue($sliderControl, value);
			$sliderControl.trigger('change');
		}

		let changeSliderHandler = function () {
			let $sliderControl  = $(this);
			let $backdropControl = $sliderControl.closest('.wpb_el_type_wd_backdrop_filter');
			let $backdropControlValueInput = $backdropControl.find('.xts-backdrop-filter-value');
			let changedValue = getChangeValue($backdropControl);

			if (0 === Object.keys(changedValue).length) {
				$backdropControlValueInput.val('');
			} else {
				changedValue = {
					devices: {
						desktop: changedValue,
					}
				}

				let encodedValue = window.btoa(unescape(encodeURIComponent(JSON.stringify(changedValue))));

				$backdropControlValueInput.val(encodedValue);
			}

			$backdropControlValueInput.trigger('change');
		}

		let updateSliderValue = function($sliderControl, value) {
			$sliderControl.find('.wd-slider-field').slider('value', value);
			$sliderControl.find('.wd-slider-field-value').val(value);
			$sliderControl.find('.wd-slider-value-preview').val(value);
		}

		let getCurrentValues = function($backdropControl) {
			let values = {};

			$backdropControl
				.find('.xts-backdrop-filter-content .xts-backdrop-filter .wd-slider-field-value')
				.each(function() {
					let paramName     = $(this).attr('name');
					values[paramName] = $(this).val();
				});

			return values;
		}

		let getDefaultValue = function($backdropControl, sliderName) {
			let $slider = $backdropControl.find('.xts-backdrop-filter-content .xts-backdrop-filter .wd-slider-field-value[name="' + sliderName + '"]');

			if ($slider.length) {
				return $slider.data('default-value');
			}

			return '';
		}
		
		let getDefaultValues = function( $backdropControl ) {
			let defaultValues = {};

			$backdropControl
				.find('.wd-slider-field-value')
				.each(function() {
					let paramName     = $(this).attr('name');
					let defaultValue  = $(this).data('default-value');

					if ('undefined' === typeof defaultValue) {
						defaultValue = '';
					}

					defaultValues[paramName] = defaultValue;
				});

			return defaultValues;
		}

		let getChangeValue = function($backdropControl) {
			let currentValues = getCurrentValues($backdropControl);
			let changedValue = {};

			Object.entries(currentValues).forEach(function([key, value]) {
				let defaultData = getDefaultValue($backdropControl, key);

				if (parseFloat(value) !== parseFloat(defaultData)) {
					changedValue[key] = value;
				}
			});

			return changedValue;
		}

		let getPresetValues = function($presetButton) {
			let presetName  = $presetButton.attr('data-id');
			let presetValue = $presetButton.attr('data-value');			

			if (!presetName || !presetValue) {
				return null;
			}

			let $backdropControl = $presetButton.closest('.wpb_el_type_wd_backdrop_filter');
			let defaultValues    = getDefaultValues($backdropControl);			

			return Object.assign({}, defaultValues, { [presetName]: JSON.parse(presetValue) });
		}

		init();
	});
})(jQuery);
