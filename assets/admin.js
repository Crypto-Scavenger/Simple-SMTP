/**
 * Admin JavaScript for Simple SMTP
 *
 * @package SimpleSmtp
 * @since   1.0.0
 */

jQuery(document).ready(function($) {
	'use strict';

	const $smtpAuth = $('#smtp_auth');
	const $adminWrap = $('.simple-smtp-admin');

	function toggleAuthFields() {
		if ($smtpAuth.is(':checked')) {
			$adminWrap.removeClass('smtp-auth-disabled');
		} else {
			$adminWrap.addClass('smtp-auth-disabled');
		}
	}

	toggleAuthFields();

	$smtpAuth.on('change', function() {
		toggleAuthFields();
	});
});
