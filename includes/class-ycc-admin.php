<?php
/**
 * WordPress admin settings page.
 *
 * @package YablonskyCookieConsent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin handler.
 */
class YCC_Admin {

	/**
	 * Settings model.
	 *
	 * @var YCC_Settings
	 */
	private $settings;

	/**
	 * Settings page hook suffix.
	 *
	 * @var string
	 */
	private $page_hook = '';

	/**
	 * Constructor.
	 *
	 * @param YCC_Settings $settings Settings model.
	 */
	public function __construct( YCC_Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Register admin hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_init', array( $this->settings, 'register' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Add settings page.
	 *
	 * @return void
	 */
	public function add_settings_page() {
		$this->page_hook = add_options_page(
			__( 'Yablonsky Cookie Consent', 'yablonsky-cookie-consent' ),
			__( 'Yablonsky Cookie Consent', 'yablonsky-cookie-consent' ),
			'manage_options',
			'yablonsky-cookie-consent',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Enqueue admin assets only on the plugin settings page.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( $hook ) {
		if ( $hook !== $this->page_hook ) {
			return;
		}

		wp_enqueue_style(
			'ycc-admin',
			YCC_PLUGIN_URL . 'admin/css/ycc-admin.css',
			array(),
			YCC_VERSION
		);

		wp_enqueue_script(
			'ycc-admin',
			YCC_PLUGIN_URL . 'admin/js/ycc-admin.js',
			array(),
			YCC_VERSION,
			true
		);
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'yablonsky-cookie-consent' ) );
		}

		$options = $this->settings->get_settings();
		?>
		<div class="wrap ycc-admin-page">
			<h1><?php echo esc_html__( 'Yablonsky Cookie Consent', 'yablonsky-cookie-consent' ); ?></h1>

			<p class="description">
				<?php echo esc_html__( 'Configure the cookie consent banner, public labels, policy links, and Google Consent Mode basics.', 'yablonsky-cookie-consent' ); ?>
			</p>

			<form method="post" action="options.php">
				<?php settings_fields( 'ycc_settings_group' ); ?>

				<div class="ycc-settings-card">
					<h2><?php echo esc_html__( 'General', 'yablonsky-cookie-consent' ); ?></h2>

					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><?php echo esc_html__( 'Enable plugin', 'yablonsky-cookie-consent' ); ?></th>
							<td>
								<label>
									<input type="checkbox" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[enabled]" value="1" <?php checked( 1, $options['enabled'] ); ?>>
									<?php echo esc_html__( 'Show the cookie consent banner on the frontend.', 'yablonsky-cookie-consent' ); ?>
								</label>
							</td>
						</tr>

						<tr>
							<th scope="row"><?php echo esc_html__( 'Site mode', 'yablonsky-cookie-consent' ); ?></th>
							<td>
								<select name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[site_mode]">
									<option value="production" <?php selected( 'production', $options['site_mode'] ); ?>><?php echo esc_html__( 'Production', 'yablonsky-cookie-consent' ); ?></option>
									<option value="staging" <?php selected( 'staging', $options['site_mode'] ); ?>><?php echo esc_html__( 'Staging', 'yablonsky-cookie-consent' ); ?></option>
									<option value="development" <?php selected( 'development', $options['site_mode'] ); ?>><?php echo esc_html__( 'Development', 'yablonsky-cookie-consent' ); ?></option>
								</select>
							</td>
						</tr>

						<tr>
							<th scope="row"><?php echo esc_html__( 'Policy version', 'yablonsky-cookie-consent' ); ?></th>
							<td>
								<input type="text" class="regular-text" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[policy_version]" value="<?php echo esc_attr( $options['policy_version'] ); ?>">
								<p class="description"><?php echo esc_html__( 'Changing this value can be used later to request renewed consent.', 'yablonsky-cookie-consent' ); ?></p>
							</td>
						</tr>

						<tr>
							<th scope="row"><?php echo esc_html__( 'Consent expiry days', 'yablonsky-cookie-consent' ); ?></th>
							<td>
								<input type="number" min="1" max="730" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[consent_expiry_days]" value="<?php echo esc_attr( $options['consent_expiry_days'] ); ?>">
							</td>
						</tr>
					</table>
				</div>

				<div class="ycc-settings-card">
					<h2><?php echo esc_html__( 'Policy links', 'yablonsky-cookie-consent' ); ?></h2>

					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><?php echo esc_html__( 'Privacy Policy URL', 'yablonsky-cookie-consent' ); ?></th>
							<td>
								<input type="url" class="regular-text" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[privacy_policy_url]" value="<?php echo esc_url( $options['privacy_policy_url'] ); ?>">
							</td>
						</tr>

						<tr>
							<th scope="row"><?php echo esc_html__( 'Cookie Policy URL', 'yablonsky-cookie-consent' ); ?></th>
							<td>
								<input type="url" class="regular-text" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[cookie_policy_url]" value="<?php echo esc_url( $options['cookie_policy_url'] ); ?>">
							</td>
						</tr>

						<tr>
							<th scope="row"><?php echo esc_html__( 'Terms URL', 'yablonsky-cookie-consent' ); ?></th>
							<td>
								<input type="url" class="regular-text" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[terms_url]" value="<?php echo esc_url( $options['terms_url'] ); ?>">
							</td>
						</tr>

						<tr>
							<th scope="row"><?php echo esc_html__( 'Policy link labels', 'yablonsky-cookie-consent' ); ?></th>
							<td class="ycc-field-stack">
								<input type="text" class="regular-text" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[privacy_policy_label]" value="<?php echo esc_attr( $options['privacy_policy_label'] ); ?>" placeholder="<?php echo esc_attr__( 'Privacy Policy', 'yablonsky-cookie-consent' ); ?>">
								<input type="text" class="regular-text" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[cookie_policy_label]" value="<?php echo esc_attr( $options['cookie_policy_label'] ); ?>" placeholder="<?php echo esc_attr__( 'Cookie Policy', 'yablonsky-cookie-consent' ); ?>">
								<input type="text" class="regular-text" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[terms_label]" value="<?php echo esc_attr( $options['terms_label'] ); ?>" placeholder="<?php echo esc_attr__( 'Terms', 'yablonsky-cookie-consent' ); ?>">
							</td>
						</tr>
					</table>
				</div>

				<div class="ycc-settings-card">
					<h2><?php echo esc_html__( 'Banner text', 'yablonsky-cookie-consent' ); ?></h2>

					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><?php echo esc_html__( 'Banner title', 'yablonsky-cookie-consent' ); ?></th>
							<td>
								<input type="text" class="regular-text" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[banner_title]" value="<?php echo esc_attr( $options['banner_title'] ); ?>">
							</td>
						</tr>

						<tr>
							<th scope="row"><?php echo esc_html__( 'Banner message', 'yablonsky-cookie-consent' ); ?></th>
							<td>
								<textarea class="large-text" rows="4" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[banner_message]"><?php echo esc_textarea( $options['banner_message'] ); ?></textarea>
							</td>
						</tr>

						<tr>
							<th scope="row"><?php echo esc_html__( 'Button labels', 'yablonsky-cookie-consent' ); ?></th>
							<td class="ycc-field-stack">
								<input type="text" class="regular-text" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[accept_all_label]" value="<?php echo esc_attr( $options['accept_all_label'] ); ?>">
								<input type="text" class="regular-text" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[reject_non_essential_label]" value="<?php echo esc_attr( $options['reject_non_essential_label'] ); ?>">
								<input type="text" class="regular-text" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[settings_label]" value="<?php echo esc_attr( $options['settings_label'] ); ?>">
								<input type="text" class="regular-text" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[save_preferences_label]" value="<?php echo esc_attr( $options['save_preferences_label'] ); ?>">
							</td>
						</tr>

						<tr>
							<th scope="row"><?php echo esc_html__( 'Modal and status labels', 'yablonsky-cookie-consent' ); ?></th>
							<td class="ycc-field-stack">
								<input type="text" class="regular-text" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[modal_title]" value="<?php echo esc_attr( $options['modal_title'] ); ?>" placeholder="<?php echo esc_attr__( 'Cookie settings', 'yablonsky-cookie-consent' ); ?>">
								<input type="text" class="regular-text" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[always_active_label]" value="<?php echo esc_attr( $options['always_active_label'] ); ?>" placeholder="<?php echo esc_attr__( 'Always active', 'yablonsky-cookie-consent' ); ?>">
								<input type="text" class="regular-text" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[close_label]" value="<?php echo esc_attr( $options['close_label'] ); ?>" placeholder="<?php echo esc_attr__( 'Close', 'yablonsky-cookie-consent' ); ?>">
							</td>
						</tr>
					</table>
				</div>

				<div class="ycc-settings-card">
					<h2><?php echo esc_html__( 'Category text', 'yablonsky-cookie-consent' ); ?></h2>

					<table class="form-table" role="presentation">
						<?php
						$category_fields = array(
							'necessary'  => __( 'Necessary', 'yablonsky-cookie-consent' ),
							'analytics'  => __( 'Analytics', 'yablonsky-cookie-consent' ),
							'marketing'  => __( 'Marketing', 'yablonsky-cookie-consent' ),
							'functional' => __( 'Functional', 'yablonsky-cookie-consent' ),
						);

						foreach ( $category_fields as $category_key => $category_label ) :
							$label_key       = $category_key . '_label';
							$description_key = $category_key . '_description';
							?>
							<tr>
								<th scope="row"><?php echo esc_html( $category_label ); ?></th>
								<td class="ycc-field-stack">
									<label>
										<span class="screen-reader-text"><?php echo esc_html( $category_label ); ?></span>
										<input type="text" class="regular-text" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[<?php echo esc_attr( $label_key ); ?>]" value="<?php echo esc_attr( $options[ $label_key ] ); ?>">
									</label>
									<label>
										<span class="screen-reader-text"><?php echo esc_html( $category_label ); ?> description</span>
										<textarea class="large-text" rows="2" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[<?php echo esc_attr( $description_key ); ?>]"><?php echo esc_textarea( $options[ $description_key ] ); ?></textarea>
									</label>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>

				<div class="ycc-settings-card">
					<h2><?php echo esc_html__( 'Google Consent Mode and GTM', 'yablonsky-cookie-consent' ); ?></h2>

					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><?php echo esc_html__( 'Google Consent Mode', 'yablonsky-cookie-consent' ); ?></th>
							<td>
								<label>
									<input type="checkbox" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[google_consent_mode_enabled]" value="1" <?php checked( 1, $options['google_consent_mode_enabled'] ); ?>>
									<?php echo esc_html__( 'Prepare Google Consent Mode v2 defaults when frontend functionality is added.', 'yablonsky-cookie-consent' ); ?>
								</label>
							</td>
						</tr>

						<tr>
							<th scope="row"><?php echo esc_html__( 'Enable Google Tag Manager', 'yablonsky-cookie-consent' ); ?></th>
							<td>
								<label>
									<input type="checkbox" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[google_tag_manager_enabled]" value="1" <?php checked( 1, $options['google_tag_manager_enabled'] ); ?>>
									<?php echo esc_html__( 'Allow the plugin to manage Google Tag Manager loading after consent.', 'yablonsky-cookie-consent' ); ?>
								</label>
							</td>
						</tr>

						<tr>
							<th scope="row"><?php echo esc_html__( 'GTM Container ID', 'yablonsky-cookie-consent' ); ?></th>
							<td>
								<input type="text" class="regular-text" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[google_tag_manager_id]" value="<?php echo esc_attr( $options['google_tag_manager_id'] ); ?>" placeholder="GTM-XXXXXXX">
								<p class="description"><?php echo esc_html__( 'Use a valid Google Tag Manager container ID. GTM will be loaded only after analytics or marketing consent is granted.', 'yablonsky-cookie-consent' ); ?></p>
							</td>
						</tr>
					</table>
				</div>

				<div class="ycc-settings-card">
					<h2><?php echo esc_html__( 'Debug and cleanup', 'yablonsky-cookie-consent' ); ?></h2>

					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><?php echo esc_html__( 'Debug mode', 'yablonsky-cookie-consent' ); ?></th>
							<td>
								<label>
									<input type="checkbox" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[debug_mode]" value="1" <?php checked( 1, $options['debug_mode'] ); ?>>
									<?php echo esc_html__( 'Enable debugging information during development.', 'yablonsky-cookie-consent' ); ?>
								</label>
							</td>
						</tr>

						<tr>
							<th scope="row"><?php echo esc_html__( 'Cleanup on uninstall', 'yablonsky-cookie-consent' ); ?></th>
							<td>
								<label>
									<input type="checkbox" name="<?php echo esc_attr( YCC_OPTION_NAME ); ?>[cleanup_on_uninstall]" value="1" <?php checked( 1, $options['cleanup_on_uninstall'] ); ?>>
									<?php echo esc_html__( 'Delete plugin settings when the plugin is uninstalled.', 'yablonsky-cookie-consent' ); ?>
								</label>
							</td>
						</tr>
					</table>
				</div>

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
