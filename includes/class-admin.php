<?php
/**
 * Admin interface for Simple SMTP
 *
 * @package SimpleSmtp
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Simple_SMTP_Admin {

	private $database;
	private $core;

	public function __construct( $database, $core ) {
		$this->database = $database;
		$this->core     = $core;

		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'handle_form_submission' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	public function add_admin_menu() {
		add_submenu_page(
			'tools.php',
			__( 'Simple SMTP', 'simple-smtp' ),
			__( 'Simple SMTP', 'simple-smtp' ),
			'manage_options',
			'simple-smtp',
			array( $this, 'render_settings_page' )
		);
	}

	public function enqueue_admin_assets( $hook ) {
		$allowed_pages = array( 'tools_page_simple-smtp' );

		if ( ! in_array( $hook, $allowed_pages, true ) ) {
			return;
		}

		wp_enqueue_style(
			'simple-smtp-admin',
			SIMPLE_SMTP_URL . 'assets/admin.css',
			array(),
			SIMPLE_SMTP_VERSION
		);

		wp_enqueue_script(
			'simple-smtp-admin',
			SIMPLE_SMTP_URL . 'assets/admin.js',
			array( 'jquery' ),
			SIMPLE_SMTP_VERSION,
			true
		);
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized access', 'simple-smtp' ) );
		}

		$settings = $this->database->get_all_settings();

		$defaults = array(
			'smtp_enabled'         => '0',
			'smtp_host'            => '',
			'smtp_port'            => '587',
			'smtp_encryption'      => 'tls',
			'smtp_auth'            => '1',
			'smtp_username'        => '',
			'smtp_password'        => '',
			'from_email'           => '',
			'from_name'            => '',
			'cleanup_on_uninstall' => '1',
		);

		$settings = wp_parse_args( $settings, $defaults );
		?>
		<div class="wrap simple-smtp-admin">
			<h1><?php esc_html_e( 'Simple SMTP Settings', 'simple-smtp' ); ?></h1>

			<?php
			if ( isset( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'] ) {
				?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Settings saved successfully!', 'simple-smtp' ); ?></p>
				</div>
				<?php
			}

			if ( isset( $_GET['test-sent'] ) && 'true' === $_GET['test-sent'] ) {
				?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Test email sent successfully!', 'simple-smtp' ); ?></p>
				</div>
				<?php
			}

			if ( isset( $_GET['test-error'] ) && ! empty( $_GET['test-error'] ) ) {
				?>
				<div class="notice notice-error is-dismissible">
					<p><?php echo esc_html( sanitize_text_field( wp_unslash( $_GET['test-error'] ) ) ); ?></p>
				</div>
				<?php
			}
			?>

			<form method="post" action="">
				<?php wp_nonce_field( 'simple_smtp_save_settings', 'simple_smtp_nonce' ); ?>

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">
								<label for="smtp_enabled">
									<?php esc_html_e( 'Enable SMTP', 'simple-smtp' ); ?>
								</label>
							</th>
							<td>
								<label>
									<input 
										type="checkbox" 
										id="smtp_enabled" 
										name="smtp_enabled"
										value="1"
										<?php checked( '1', $settings['smtp_enabled'] ); ?>
									/>
									<?php esc_html_e( 'Use SMTP server instead of PHP mail() function', 'simple-smtp' ); ?>
								</label>
								<p class="description">
									<?php esc_html_e( 'When disabled, WordPress will use the default PHP mail() function.', 'simple-smtp' ); ?>
								</p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="smtp_host">
									<?php esc_html_e( 'SMTP Host', 'simple-smtp' ); ?>
								</label>
							</th>
							<td>
								<input 
									type="text" 
									id="smtp_host" 
									name="smtp_host"
									value="<?php echo esc_attr( $settings['smtp_host'] ); ?>"
									class="regular-text"
									placeholder="smtp.gmail.com"
								/>
								<p class="description">
									<?php esc_html_e( 'Your SMTP server hostname (e.g., smtp.gmail.com, smtp-mail.outlook.com)', 'simple-smtp' ); ?>
								</p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="smtp_port">
									<?php esc_html_e( 'SMTP Port', 'simple-smtp' ); ?>
								</label>
							</th>
							<td>
								<input 
									type="number" 
									id="smtp_port" 
									name="smtp_port"
									value="<?php echo esc_attr( $settings['smtp_port'] ); ?>"
									class="small-text"
									min="1"
									max="65535"
								/>
								<p class="description">
									<?php esc_html_e( 'Common ports: 587 (TLS), 465 (SSL), 25 (None)', 'simple-smtp' ); ?>
								</p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="smtp_encryption">
									<?php esc_html_e( 'Encryption', 'simple-smtp' ); ?>
								</label>
							</th>
							<td>
								<select id="smtp_encryption" name="smtp_encryption">
									<option value="none" <?php selected( 'none', $settings['smtp_encryption'] ); ?>>
										<?php esc_html_e( 'None', 'simple-smtp' ); ?>
									</option>
									<option value="ssl" <?php selected( 'ssl', $settings['smtp_encryption'] ); ?>>
										<?php esc_html_e( 'SSL', 'simple-smtp' ); ?>
									</option>
									<option value="tls" <?php selected( 'tls', $settings['smtp_encryption'] ); ?>>
										<?php esc_html_e( 'TLS', 'simple-smtp' ); ?>
									</option>
								</select>
								<p class="description">
									<?php esc_html_e( 'Choose the encryption type supported by your SMTP server', 'simple-smtp' ); ?>
								</p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="smtp_auth">
									<?php esc_html_e( 'SMTP Authentication', 'simple-smtp' ); ?>
								</label>
							</th>
							<td>
								<label>
									<input 
										type="checkbox" 
										id="smtp_auth" 
										name="smtp_auth"
										value="1"
										<?php checked( '1', $settings['smtp_auth'] ); ?>
									/>
									<?php esc_html_e( 'Enable SMTP authentication', 'simple-smtp' ); ?>
								</label>
								<p class="description">
									<?php esc_html_e( 'Most SMTP servers require authentication', 'simple-smtp' ); ?>
								</p>
							</td>
						</tr>

						<tr class="smtp-auth-field">
							<th scope="row">
								<label for="smtp_username">
									<?php esc_html_e( 'Username', 'simple-smtp' ); ?>
								</label>
							</th>
							<td>
								<input 
									type="text" 
									id="smtp_username" 
									name="smtp_username"
									value="<?php echo esc_attr( $settings['smtp_username'] ); ?>"
									class="regular-text"
									placeholder="your-email@example.com"
									autocomplete="off"
								/>
								<p class="description">
									<?php esc_html_e( 'Your SMTP username (usually your email address)', 'simple-smtp' ); ?>
								</p>
							</td>
						</tr>

						<tr class="smtp-auth-field">
							<th scope="row">
								<label for="smtp_password">
									<?php esc_html_e( 'Password', 'simple-smtp' ); ?>
								</label>
							</th>
							<td>
								<input 
									type="password" 
									id="smtp_password" 
									name="smtp_password"
									value="<?php echo esc_attr( $settings['smtp_password'] ); ?>"
									class="regular-text"
									placeholder="<?php esc_attr_e( 'Enter password', 'simple-smtp' ); ?>"
									autocomplete="new-password"
								/>
								<p class="description">
									<?php esc_html_e( 'Your SMTP password or app-specific password', 'simple-smtp' ); ?>
								</p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="from_email">
									<?php esc_html_e( 'From Email', 'simple-smtp' ); ?>
								</label>
							</th>
							<td>
								<input 
									type="email" 
									id="from_email" 
									name="from_email"
									value="<?php echo esc_attr( $settings['from_email'] ); ?>"
									class="regular-text"
									placeholder="noreply@yourdomain.com"
								/>
								<p class="description">
									<?php esc_html_e( 'The email address that emails will appear to come from', 'simple-smtp' ); ?>
								</p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="from_name">
									<?php esc_html_e( 'From Name', 'simple-smtp' ); ?>
								</label>
							</th>
							<td>
								<input 
									type="text" 
									id="from_name" 
									name="from_name"
									value="<?php echo esc_attr( $settings['from_name'] ); ?>"
									class="regular-text"
									placeholder="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
								/>
								<p class="description">
									<?php esc_html_e( 'The name that will appear as the sender', 'simple-smtp' ); ?>
								</p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="cleanup_on_uninstall">
									<?php esc_html_e( 'Cleanup on Uninstall', 'simple-smtp' ); ?>
								</label>
							</th>
							<td>
								<label>
									<input 
										type="checkbox" 
										id="cleanup_on_uninstall" 
										name="cleanup_on_uninstall"
										value="1"
										<?php checked( '1', $settings['cleanup_on_uninstall'] ); ?>
									/>
									<?php esc_html_e( 'Remove all plugin data when uninstalling', 'simple-smtp' ); ?>
								</label>
							</td>
						</tr>
					</tbody>
				</table>

				<?php submit_button( __( 'Save Settings', 'simple-smtp' ) ); ?>
			</form>

			<hr>

			<h2><?php esc_html_e( 'Send Test Email', 'simple-smtp' ); ?></h2>
			<p><?php esc_html_e( 'Send a test email to verify your SMTP configuration is working correctly.', 'simple-smtp' ); ?></p>

			<form method="post" action="">
				<?php wp_nonce_field( 'simple_smtp_send_test', 'simple_smtp_test_nonce' ); ?>

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">
								<label for="test_email">
									<?php esc_html_e( 'Send To', 'simple-smtp' ); ?>
								</label>
							</th>
							<td>
								<input 
									type="email" 
									id="test_email" 
									name="test_email"
									value="<?php echo esc_attr( wp_get_current_user()->user_email ); ?>"
									class="regular-text"
									required
								/>
								<p class="description">
									<?php esc_html_e( 'Enter the email address where you want to receive the test email', 'simple-smtp' ); ?>
								</p>
							</td>
						</tr>
					</tbody>
				</table>

				<?php submit_button( __( 'Send Test Email', 'simple-smtp' ), 'secondary', 'send_test_email' ); ?>
			</form>
		</div>
		<?php
	}

	public function handle_form_submission() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_POST['send_test_email'] ) ) {
			$this->handle_test_email();
			return;
		}

		if ( ! isset( $_POST['simple_smtp_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['simple_smtp_nonce'] ) ), 'simple_smtp_save_settings' ) ) {
			wp_die( esc_html__( 'Security check failed', 'simple-smtp' ) );
		}

		$settings_to_save = array(
			'smtp_enabled'    => isset( $_POST['smtp_enabled'] ) ? '1' : '0',
			'smtp_host'       => isset( $_POST['smtp_host'] ) ? sanitize_text_field( wp_unslash( $_POST['smtp_host'] ) ) : '',
			'smtp_port'       => isset( $_POST['smtp_port'] ) ? absint( $_POST['smtp_port'] ) : '587',
			'smtp_encryption' => isset( $_POST['smtp_encryption'] ) ? sanitize_text_field( wp_unslash( $_POST['smtp_encryption'] ) ) : 'tls',
			'smtp_auth'       => isset( $_POST['smtp_auth'] ) ? '1' : '0',
			'smtp_username'   => isset( $_POST['smtp_username'] ) ? sanitize_text_field( wp_unslash( $_POST['smtp_username'] ) ) : '',
			'smtp_password'   => isset( $_POST['smtp_password'] ) ? sanitize_text_field( wp_unslash( $_POST['smtp_password'] ) ) : '',
			'from_email'      => isset( $_POST['from_email'] ) ? sanitize_email( wp_unslash( $_POST['from_email'] ) ) : '',
			'from_name'       => isset( $_POST['from_name'] ) ? sanitize_text_field( wp_unslash( $_POST['from_name'] ) ) : '',
			'cleanup_on_uninstall' => isset( $_POST['cleanup_on_uninstall'] ) ? '1' : '0',
		);

		foreach ( $settings_to_save as $key => $value ) {
			$this->database->save_setting( $key, $value );
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'             => 'simple-smtp',
					'settings-updated' => 'true',
				),
				admin_url( 'tools.php' )
			)
		);
		exit;
	}

	private function handle_test_email() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized access', 'simple-smtp' ) );
		}

		if ( ! isset( $_POST['simple_smtp_test_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['simple_smtp_test_nonce'] ) ), 'simple_smtp_send_test' ) ) {
			wp_die( esc_html__( 'Security check failed', 'simple-smtp' ) );
		}

		if ( ! isset( $_POST['test_email'] ) ) {
			wp_die( esc_html__( 'Email address is required', 'simple-smtp' ) );
		}

		$test_email = sanitize_email( wp_unslash( $_POST['test_email'] ) );

		$result = $this->core->send_test_email( $test_email );

		if ( is_wp_error( $result ) ) {
			wp_safe_redirect(
				add_query_arg(
					array(
						'page'       => 'simple-smtp',
						'test-error' => rawurlencode( $result->get_error_message() ),
					),
					admin_url( 'tools.php' )
				)
			);
			exit;
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'      => 'simple-smtp',
					'test-sent' => 'true',
				),
				admin_url( 'tools.php' )
			)
		);
		exit;
	}
}
