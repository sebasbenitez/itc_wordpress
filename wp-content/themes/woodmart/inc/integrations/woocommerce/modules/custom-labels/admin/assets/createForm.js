/* global woodmartConfig jQuery */
(function($) {
	'use strict'

	var $wrapper = $('.wd-add-custom-label')
	var $form = $wrapper.find('form')
	var $custom_label = $wrapper.find('.xts-popup')

	const showNotice = function($custom_label, message, status) {
		$custom_label.find('.xts-notices-wrapper').text('')
		$custom_label
			.find('.xts-notices-wrapper')
			.append('<div class="xts-notice xts-' + status + '">' + message + '</div>')
		$custom_label.removeClass('xts-loading')
	}

	// Form.
	$form.on('submit', function(e) {
		e.preventDefault()

		if ($(this).hasClass('xts-disabled') || $(this).prop('disabled')) {
			return false
		}

		var customLabelName = $form.find('.xts-custom-label-name').val()
		var predefinedName = $form.find('.xts-custom-label-predefined-layout.xts-active').data('name')

		$custom_label.addClass('xts-loading')

		$.ajax({
			url: woodmartConfig.ajaxUrl,
			method: 'POST',
			data: {
				action: 'wd_custom_label_create',
				name: customLabelName,
				predefined_name: predefinedName,
				security: woodmartConfig.get_new_template_nonce,
			},
			dataType: 'json',
			success: function(response) {
				if (!response.redirect_url) {
					showNotice($custom_label, woodmartConfig.label_creation_error, 'warning')
				} else {
					window.location.href = response.redirect_url
				}
			},
			error: function() {
				showNotice($custom_label, woodmartConfig.label_creation_error, 'warning')
			},
		})
	})

	// Predefined.
	$('.xts-custom-label-predefined-layout').on('click', function() {
		var $this = $(this)
		$this.siblings().removeClass('xts-active')
		$this.toggleClass('xts-active')

		var label = $this.data('label')

		if (label) {
			$form.find('.xts-custom-label-name').val(label)
		}
	})

	// Custom label.
	$('.page-title-action').on(
		'click',
		function(event) {
			event.preventDefault()
			$wrapper.find('.xts-popup-holder').addClass('xts-opened')
			$('html').addClass('xts-popup-opened')

			setTimeout(function() {
				var $input = $form.find('.xts-custom-label-name')
				var strLength = $input.val().length
				$input.trigger('focus')
				$input[0].setSelectionRange(strLength, strLength)
			}, 100)
		}
	)

	$(document).on('click', '.xts-popup-opener', function() {
		$(this).parent().addClass('xts-opened')
		$('html').addClass('xts-popup-opened')
	})
	
	$(document).on('click', '.xts-popup-close, .xts-popup-overlay', function() {
		$('.xts-popup-holder').removeClass('xts-opened')
		$('html').removeClass('xts-popup-opened')
	})
})(jQuery)