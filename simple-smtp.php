<?php
/**
 * Plugin Name: Simple MT Protocol
 * Description: Lightweight SMTP plugin that redirects all WordPress emails through your SMTP server with easy configuration and testing capabilities.
 * Version: 1.0.1
 * Text Domain: simple-smtp
 * Domain Path: /languages
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SIMPLE_SMTP_VERSION', '1.0.1' );
define( 'SIMPLE_SMTP_DIR', plugin_dir_path( __FILE__ ) );
define( 'SIMPLE_SMTP_URL', plugin_dir_url( __FILE__ ) );

require_once SIMPLE_SMTP_DIR . 'includes/class-database.php';
require_once SIMPLE_SMTP_DIR . 'includes/class-core.php';
require_once SIMPLE_SMTP_DIR . 'includes/class-admin.php';

function simple_smtp_init() {
	$database = new Simple_SMTP_Database();
	$core     = new Simple_SMTP_Core( $database );

	if ( is_admin() ) {
		$admin = new Simple_SMTP_Admin( $database, $core );
	}
}
add_action( 'plugins_loaded', 'simple_smtp_init' );

register_activation_hook( __FILE__, array( 'Simple_SMTP_Database', 'activate' ) );
