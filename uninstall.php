<?php
/**
 * Uninstall handler for Simple SMTP
 *
 * @package SimpleSmtp
 * @since   1.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$table_name = $wpdb->prefix . 'simple_smtp_settings';

$cleanup_enabled = $wpdb->get_var(
	$wpdb->prepare(
		"SELECT setting_value FROM {$table_name} WHERE setting_key = %s",
		'cleanup_on_uninstall'
	)
);

if ( '1' === $cleanup_enabled ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-database.php';
	Simple_SMTP_Database::drop_table();
}
