/**
 * Admin scripts for Simple SMTP
 *
 * @package SimpleSmtp
 * @since   1.0.0
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		var smtpAuthCheckbox = $('#smtp_auth');
		var adminWrapper = $('.simple-smtp-admin');

		function toggleAuthFields() {
			if (smtpAuthCheckbox.is(':checked')) {
				adminWrapper.removeClass('smtp-auth-disabled');
			} else {
				adminWrapper.addClass('smtp-auth-disabled');
			}
		}

		toggleAuthFields();

		smtpAuthCheckbox.on('change', function() {
			toggleAuthFields();
		});
	});
})(jQuery);
