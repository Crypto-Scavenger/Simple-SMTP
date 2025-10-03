<?php
/**
 * Database operations for Simple SMTP
 *
 * @package SimpleSmtp
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Simple_SMTP_Database {

	private $settings_cache = null;

	public static function activate() {
		global $wpdb;

		$table_name      = $wpdb->prefix . 'simple_smtp_settings';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			setting_key varchar(191) NOT NULL,
			setting_value longtext NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY setting_key (setting_key)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		$defaults = array(
			'smtp_enabled'       => '0',
			'smtp_host'          => '',
			'smtp_port'          => '587',
			'smtp_encryption'    => 'tls',
			'smtp_auth'          => '1',
			'smtp_username'      => '',
			'smtp_password'      => '',
			'from_email'         => '',
			'from_name'          => '',
			'cleanup_on_uninstall' => '1',
		);

		$instance = new self();
		foreach ( $defaults as $key => $value ) {
			if ( false === $instance->get_setting( $key ) ) {
				$instance->save_setting( $key, $value );
			}
		}
	}

	public function get_setting( $key ) {
		global $wpdb;

		if ( null === $this->settings_cache ) {
			$this->settings_cache = array();
		}

		if ( isset( $this->settings_cache[ $key ] ) ) {
			return $this->settings_cache[ $key ];
		}

		$table_name = $wpdb->prefix . 'simple_smtp_settings';

		$result = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT setting_value FROM $table_name WHERE setting_key = %s",
				$key
			)
		);

		if ( null !== $result ) {
			$this->settings_cache[ $key ] = $result;
			return $result;
		}

		return false;
	}

	public function save_setting( $key, $value ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'simple_smtp_settings';

		$existing = $this->get_setting( $key );

		if ( false !== $existing ) {
			$result = $wpdb->update(
				$table_name,
				array( 'setting_value' => $value ),
				array( 'setting_key' => $key ),
				array( '%s' ),
				array( '%s' )
			);
		} else {
			$result = $wpdb->insert(
				$table_name,
				array(
					'setting_key'   => $key,
					'setting_value' => $value,
				),
				array( '%s', '%s' )
			);
		}

		if ( false !== $result ) {
			$this->settings_cache[ $key ] = $value;
			return true;
		}

		return false;
	}

	public function get_all_settings() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'simple_smtp_settings';

		$results = $wpdb->get_results(
			"SELECT setting_key, setting_value FROM $table_name",
			ARRAY_A
		);

		$settings = array();
		if ( $results ) {
			foreach ( $results as $row ) {
				$settings[ $row['setting_key'] ] = $row['setting_value'];
			}
		}

		return $settings;
	}

	public static function drop_table() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'simple_smtp_settings';

		$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
	}
}
