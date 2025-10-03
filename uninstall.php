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
		"SELECT setting_value FROM $table_name WHERE setting_key = %s",
		'cleanup_on_uninstall'
	)
);

if ( '1' === $cleanup ) {
	$wpdb->query( "DROP TABLE IF EXISTS $table_name" );

	wp_cache_flush();
}
