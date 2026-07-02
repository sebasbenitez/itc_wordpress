(function($) {
	$('#vc_ui-panel-edit-element').on('vcPanel.shown', function() {
		$('.xts-text-inputs').each(function() {
			let $wrapper = $(this);

			$wrapper.find('.wd-device').on('click', function() {
				let $this = $(this);

				updateActiveClass($this);
				updateActiveClass($wrapper.find('.xts-text-input[data-device="' + $this.data('value') + '"]'));
			});

			$wrapper.find('.xts-text-value-preview').each(function() {
				let $this = $(this);

				$this.on('change keyup', function() {
					setMainValue();
				}).trigger('change');
			});

			function setMainValue() {
				let $valueInput = $wrapper.find('.wpb_vc_param_value');

				let $results = {
					devices : {}
				};

				var flag = false;

				$wrapper.find('.xts-text-input').each(function() {
					let $this = $(this);
					let $input = $this.find('input');

					if ( $input.val() ) {
						flag = true;
					}

					$results.devices[$this.attr('data-device')] = {
						value: $input.val()
					};
				});

				if ( flag ) {
					$valueInput.val(window.btoa(JSON.stringify($results))).trigger('change');
				} else {
					$valueInput.val('').trigger('change');
				}
			}
		});

		function updateActiveClass($this) {
			$this.siblings().removeClass('xts-active');
			$this.addClass('xts-active');
		}
	});
})(jQuery);
