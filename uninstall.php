<?php
/**
 * Uninstall Simple SMTP
 *
 * @package SimpleSmtp
 * @since   1.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$table_name = $wpdb->prefix . 'simple_smtp_settings';

$cleanup = $wpdb->get_var(
	$wpdb->prepare(
		'SELECT setting_value FROM %i WHERE setting_key = %s',
		$table_name,
		'cleanup_on_uninstall'
	)
);

if ( '1' === $cleanup ) {
	$wpdb->query(
		$wpdb->prepare(
			'DROP TABLE IF EXISTS %i',
			$table_name
		)
	);

	wp_cache_flush();
}
