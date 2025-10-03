<?php
/**
 * Core functionality for Simple SMTP
 *
 * @package SimpleSmtp
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Simple_SMTP_Core {

	private $database;

	public function __construct( $database ) {
		$this->database = $database;

		$smtp_enabled = $this->database->get_setting( 'smtp_enabled' );
		if ( '1' === $smtp_enabled ) {
			add_action( 'phpmailer_init', array( $this, 'configure_smtp' ) );
		}

		add_filter( 'wp_mail_from', array( $this, 'set_from_email' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'set_from_name' ) );
	}

	public function configure_smtp( $phpmailer ) {
		$smtp_host       = $this->database->get_setting( 'smtp_host' );
		$smtp_port       = $this->database->get_setting( 'smtp_port' );
		$smtp_encryption = $this->database->get_setting( 'smtp_encryption' );
		$smtp_auth       = $this->database->get_setting( 'smtp_auth' );
		$smtp_username   = $this->database->get_setting( 'smtp_username' );
		$smtp_password   = $this->database->get_setting( 'smtp_password' );

		if ( empty( $smtp_host ) || empty( $smtp_port ) ) {
			return;
		}

		$phpmailer->isSMTP();
		$phpmailer->Host = $smtp_host;
		$phpmailer->Port = absint( $smtp_port );

		if ( 'none' !== $smtp_encryption ) {
			$phpmailer->SMTPSecure = $smtp_encryption;
		}

		if ( '1' === $smtp_auth ) {
			$phpmailer->SMTPAuth = true;
			$phpmailer->Username = $smtp_username;
			$phpmailer->Password = $smtp_password;
		} else {
			$phpmailer->SMTPAuth = false;
		}
	}

	public function set_from_email( $original_email_address ) {
		$from_email = $this->database->get_setting( 'from_email' );

		if ( ! empty( $from_email ) && is_email( $from_email ) ) {
			return $from_email;
		}

		return $original_email_address;
	}

	public function set_from_name( $original_email_from ) {
		$from_name = $this->database->get_setting( 'from_name' );

		if ( ! empty( $from_name ) ) {
			return $from_name;
		}

		return $original_email_from;
	}

	public function send_test_email( $to_email ) {
		if ( ! is_email( $to_email ) ) {
			return new WP_Error( 'invalid_email', __( 'Invalid email address', 'simple-smtp' ) );
		}

		$subject = __( 'Simple SMTP Test Email', 'simple-smtp' );
		$message = __( 'This is a test email sent from Simple SMTP plugin. If you receive this, your SMTP configuration is working correctly!', 'simple-smtp' );

		$sent = wp_mail( $to_email, $subject, $message );

		if ( $sent ) {
			return true;
		} else {
			return new WP_Error( 'send_failed', __( 'Failed to send test email. Please check your SMTP settings.', 'simple-smtp' ) );
		}
	}
}
