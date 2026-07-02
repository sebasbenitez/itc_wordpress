/* global elementor, noUiSlider */

jQuery(window).on('elementor:init', function() {
	var backdropFilter = elementor.modules.controls.BaseMultiple.extend({
		defaultValues: {},
		presets: {},

		ui: function() {
			var ui = elementor.modules.controls.BaseMultiple.prototype.ui.apply(this, arguments);

			ui.sliders = '.elementor-slider';
			ui.presets = '.xts-set-item.xts-set-btn';

			return ui;
		},

		events: function() {
			var events = elementor.modules.controls.BaseMultiple.prototype.events.apply(this, arguments);

			events[ 'click @ui.presets' ] = 'onPresetsClick';

			return events;
		},

		onReady: function() {
			this.setDefaultValues();
			this.setPresets();
			this.initSliders();
		},

		onPresetsClick: function(event) {
			var presetId = event.currentTarget.dataset.id;
			var presetValue = this.presets[presetId] ?? null;

			this.resetSliders();
			this.applyPresetValues(presetId, presetValue);
		},

		onInputChange: function(event) {
			var sliderName = event.currentTarget.dataset.setting;
			var $slider = this.ui.sliders.filter('[data-input="' + sliderName + '"]');
			var value = event.currentTarget.value;
			var numericValue = '' !== value ? +value : '';

			this.setValue(sliderName, numericValue);

			if ($slider.length && $slider[0].noUiSlider) {
				$slider[0].noUiSlider.set(numericValue);
			}
		},

		setDefaultValues: function() {
			var self = this;

			this.ui.sliders.each(function(index, slider) {
				var $input = jQuery(slider).next('.elementor-slider-input').find('input');
				self.defaultValues[slider.dataset.input] = +$input.data('default-value') || +$input.attr('min') || 0;
			});
		},

		setPresets: function() {
			var self = this;

			this.ui.presets.each(function(index, preset) {
				var $preset  = jQuery(preset);
				var presetId = $preset.data('id');

				self.presets[presetId] = $preset.data('value');
			});
		},

		initSliders: function() {
			var self = this;

			this.ui.sliders.each(function(index, slider) {
				var $input       = jQuery(slider).next('.elementor-slider-input').find('input');
				var type         = slider.dataset.input;
				var defaultValue = self.defaultValues[type] ?? null;
				var storedValue  = $input.val();
				var hasValue     = '' !== storedValue && 'undefined' !== typeof storedValue && null !== storedValue;
				var inputValue   = hasValue ? +storedValue : defaultValue;

				if (!hasValue && null !== defaultValue) {
					$input.attr('placeholder', defaultValue);
				}

				var sliderInstance = noUiSlider.create(slider, {
					start: [inputValue],
					step: +($input.attr('step') || 1),
					range: {
						min: +$input.attr('min'),
						max: +$input.attr('max'),
					},
					format: {
						to: function(sliderValue) {
							return +sliderValue.toFixed(2);
						},
						from: function(sliderValue) {
							return +sliderValue;
						},
					},
				});

				sliderInstance.on('slide', function(sliderValues) {
					var valueType = sliderInstance.target.dataset.input;
					var sliderValue = Array.isArray(sliderValues) ? sliderValues[0] : sliderValues;

					$input.attr('placeholder', '');
					$input.val(sliderValue);

					self.setValue(valueType, sliderValue);
				});
			});
		},

		resetSliders: function() {
			var self = this;

			this.ui.sliders.each(function(index, slider) {
				var $input = jQuery(slider).next('.elementor-slider-input').find('input');
				var defaultValue = self.defaultValues[slider.dataset.input] ?? null;

				$input.val('');

				self.setValue(slider.dataset.input, '');

				if (null !== defaultValue) {
					$input.attr('placeholder', defaultValue);

					var sliderInstance = slider.noUiSlider;

					if (sliderInstance) {
						sliderInstance.set(defaultValue);
					}
				}
			});
		},

		applyPresetValues: function(presetId, presetValue) {
			var $slider = this.ui.sliders.filter('[data-input="' + presetId + '"]');

			if (0 !== $slider.length) {
				$slider = $slider[0];

				var $input = jQuery($slider).next('.elementor-slider-input').find('input');
				var sliderInstance = $slider.noUiSlider;

				$input.val(presetValue);
				this.setValue(presetId, presetValue);

				if (sliderInstance) {
					sliderInstance.set(presetValue);
				}
			}
		},

	}, {
		getStyleValue: function(placeholder, controlValue) {
			if (!controlValue || 'object' !== typeof controlValue) {
				return '__EMPTY__';
			}

			var parts = [];

			Object.keys(controlValue).forEach(function(key) {
				var value = controlValue[key];
				var unit  = '%';

				if ('' === value || 'undefined' === typeof value || null === value) {
					return;
				}

				if ( 'hue-rotate' === key ) {
					unit = 'deg';
				} else if ( 'blur' === key ) {
					unit = 'px';
				} else if ( 'brightness' === key ) {
					unit = '';
				}

				parts.push(key + '(' + value + unit + ')');
			});

			return parts.length ? parts.join(' ') : '__EMPTY__';
		},
	});

	elementor.addControlView( 'wd_backdrop_filter', backdropFilter );
});
