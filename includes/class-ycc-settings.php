<?php
/**
 * Settings defaults and sanitization.
 *
 * @package YablonskyCookieConsent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings model.
 */
class YCC_Settings {

	/**
	 * Get default plugin settings.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			'enabled'                         => 0,
			'site_mode'                       => 'production',
			'default_language'                => 'en',
			'policy_version'                  => '1.0.0',
			'consent_expiry_days'             => 180,
			'privacy_policy_url'              => '',
			'cookie_policy_url'               => '',
			'terms_url'                       => '',
			'privacy_policy_label'            => __( 'Privacy Policy', 'yablonsky-cookie-consent' ),
			'cookie_policy_label'             => __( 'Cookie Policy', 'yablonsky-cookie-consent' ),
			'terms_label'                     => __( 'Terms', 'yablonsky-cookie-consent' ),
			'modal_title'                     => __( 'Cookie settings', 'yablonsky-cookie-consent' ),
			'always_active_label'             => __( 'Always active', 'yablonsky-cookie-consent' ),
			'close_label'                     => __( 'Close', 'yablonsky-cookie-consent' ),
			'banner_title'                    => __( 'Cookie preferences', 'yablonsky-cookie-consent' ),
			'banner_message'                  => __( 'We use necessary cookies to make this website work. With your consent, we may also use analytics and marketing cookies to understand website usage and improve our services. You can accept all cookies, reject non-essential cookies, or manage your preferences.', 'yablonsky-cookie-consent' ),
			'accept_all_label'                => __( 'Accept all', 'yablonsky-cookie-consent' ),
			'reject_non_essential_label'      => __( 'Reject non-essential', 'yablonsky-cookie-consent' ),
			'settings_label'                  => __( 'Cookie settings', 'yablonsky-cookie-consent' ),
			'save_preferences_label'          => __( 'Save preferences', 'yablonsky-cookie-consent' ),
			'necessary_label'                 => __( 'Necessary', 'yablonsky-cookie-consent' ),
			'necessary_description'           => __( 'Required for security and core website functionality. These cannot be disabled.', 'yablonsky-cookie-consent' ),
			'analytics_label'                 => __( 'Analytics', 'yablonsky-cookie-consent' ),
			'analytics_description'           => __( 'Help us understand how visitors use the website so we can improve it.', 'yablonsky-cookie-consent' ),
			'marketing_label'                 => __( 'Marketing', 'yablonsky-cookie-consent' ),
			'marketing_description'           => __( 'Help us measure advertising performance and improve marketing relevance.', 'yablonsky-cookie-consent' ),
			'functional_label'                => __( 'Functional', 'yablonsky-cookie-consent' ),
			'functional_description'          => __( 'Help us remember optional preferences and improve website functionality.', 'yablonsky-cookie-consent' ),
			'banner_layout'                   => 'compact',
			'banner_position'                 => 'bottom',
			'theme_mode'                      => 'wordpress_default',
			'custom_css_class'                => '',
			'custom_css'                      => '',
			'show_reject_button'              => 1,
			'show_settings_button'            => 1,
			'show_close_button'               => 0,
			'google_consent_mode_enabled'     => 1,
			'google_consent_mode_mode'        => 'basic',
			'block_non_essential_by_default'  => 1,
			'google_tag_manager_enabled'      => 0,
			'google_tag_manager_id'           => '',
			'debug_mode'                      => 0,
			'server_side_consent_log_enabled' => 0,
			'anonymize_ip_in_logs'            => 1,
			'cleanup_on_uninstall'            => 0,
		);
	}

	/**
	 * Get merged settings.
	 *
	 * @return array
	 */
	public function get_settings() {
		$saved = get_option( YCC_OPTION_NAME, array() );

		if ( ! is_array( $saved ) ) {
			$saved = array();
		}

		return wp_parse_args( $saved, self::get_defaults() );
	}

	/**
	 * Register settings with WordPress.
	 *
	 * @return void
	 */
	public function register() {
		register_setting(
			'ycc_settings_group',
			YCC_OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => self::get_defaults(),
			)
		);
	}

	/**
	 * Sanitize settings.
	 *
	 * @param mixed $input Raw settings input.
	 * @return array
	 */
	public function sanitize( $input ) {
		$defaults = self::get_defaults();
		$current  = get_option( YCC_OPTION_NAME, array() );

		if ( ! is_array( $current ) ) {
			$current = array();
		}

		$current = wp_parse_args( $current, $defaults );

		if ( ! is_array( $input ) ) {
			return $current;
		}

		/*
		 * Preserve existing values for fields that are not present in a submitted form.
		 * This protects site-specific copy during upgrades and future partial admin forms.
		 * Checkboxes are still read from the raw input so that unchecked boxes can be saved as 0.
		 */
		$merged_input = wp_parse_args( $input, $current );

		$output = array();

		$output['enabled']                         = YCC_Sanitizer::checkbox( $input, 'enabled' );
		$output['site_mode']                       = YCC_Sanitizer::select( $merged_input, 'site_mode', array( 'production', 'staging', 'development' ), 'production' );
		$output['default_language']                = YCC_Sanitizer::text( $merged_input, 'default_language', 'en' );
		$output['policy_version']                  = YCC_Sanitizer::text( $merged_input, 'policy_version', '1.0.0' );
		$output['consent_expiry_days']             = YCC_Sanitizer::number( $merged_input, 'consent_expiry_days', 1, 730, 180 );
		$output['privacy_policy_url']              = YCC_Sanitizer::url( $merged_input, 'privacy_policy_url' );
		$output['cookie_policy_url']               = YCC_Sanitizer::url( $merged_input, 'cookie_policy_url' );
		$output['terms_url']                       = YCC_Sanitizer::url( $merged_input, 'terms_url' );
		$output['privacy_policy_label']            = YCC_Sanitizer::text( $merged_input, 'privacy_policy_label', $defaults['privacy_policy_label'] );
		$output['cookie_policy_label']             = YCC_Sanitizer::text( $merged_input, 'cookie_policy_label', $defaults['cookie_policy_label'] );
		$output['terms_label']                     = YCC_Sanitizer::text( $merged_input, 'terms_label', $defaults['terms_label'] );
		$output['modal_title']                     = YCC_Sanitizer::text( $merged_input, 'modal_title', $defaults['modal_title'] );
		$output['always_active_label']             = YCC_Sanitizer::text( $merged_input, 'always_active_label', $defaults['always_active_label'] );
		$output['close_label']                     = YCC_Sanitizer::text( $merged_input, 'close_label', $defaults['close_label'] );
		$output['banner_title']                    = YCC_Sanitizer::text( $merged_input, 'banner_title', $defaults['banner_title'] );
		$output['banner_message']                  = YCC_Sanitizer::textarea( $merged_input, 'banner_message', $defaults['banner_message'] );
		$output['accept_all_label']                = YCC_Sanitizer::text( $merged_input, 'accept_all_label', $defaults['accept_all_label'] );
		$output['reject_non_essential_label']      = YCC_Sanitizer::text( $merged_input, 'reject_non_essential_label', $defaults['reject_non_essential_label'] );
		$output['settings_label']                  = YCC_Sanitizer::text( $merged_input, 'settings_label', $defaults['settings_label'] );
		$output['save_preferences_label']          = YCC_Sanitizer::text( $merged_input, 'save_preferences_label', $defaults['save_preferences_label'] );
		$output['necessary_label']                 = YCC_Sanitizer::text( $merged_input, 'necessary_label', $defaults['necessary_label'] );
		$output['necessary_description']           = YCC_Sanitizer::textarea( $merged_input, 'necessary_description', $defaults['necessary_description'] );
		$output['analytics_label']                 = YCC_Sanitizer::text( $merged_input, 'analytics_label', $defaults['analytics_label'] );
		$output['analytics_description']           = YCC_Sanitizer::textarea( $merged_input, 'analytics_description', $defaults['analytics_description'] );
		$output['marketing_label']                 = YCC_Sanitizer::text( $merged_input, 'marketing_label', $defaults['marketing_label'] );
		$output['marketing_description']           = YCC_Sanitizer::textarea( $merged_input, 'marketing_description', $defaults['marketing_description'] );
		$output['functional_label']                = YCC_Sanitizer::text( $merged_input, 'functional_label', $defaults['functional_label'] );
		$output['functional_description']          = YCC_Sanitizer::textarea( $merged_input, 'functional_description', $defaults['functional_description'] );
		$output['banner_layout']                   = YCC_Sanitizer::select( $merged_input, 'banner_layout', array( 'compact', 'modal', 'floating' ), 'compact' );
		$output['banner_position']                 = YCC_Sanitizer::select( $merged_input, 'banner_position', array( 'bottom', 'top', 'bottom-left', 'bottom-right' ), 'bottom' );
		$output['theme_mode']                      = YCC_Sanitizer::select( $merged_input, 'theme_mode', array( 'wordpress_default', 'custom_css' ), 'wordpress_default' );
		$output['custom_css_class']                = YCC_Sanitizer::text( $merged_input, 'custom_css_class', '' );
		$output['custom_css']                      = YCC_Sanitizer::css( $merged_input, 'custom_css' );
		$output['show_reject_button']              = YCC_Sanitizer::checkbox( $input, 'show_reject_button' );
		$output['show_settings_button']            = YCC_Sanitizer::checkbox( $input, 'show_settings_button' );
		$output['show_close_button']               = YCC_Sanitizer::checkbox( $input, 'show_close_button' );
		$output['google_consent_mode_enabled']     = YCC_Sanitizer::checkbox( $input, 'google_consent_mode_enabled' );
		$output['google_consent_mode_mode']        = YCC_Sanitizer::select( $merged_input, 'google_consent_mode_mode', array( 'basic' ), 'basic' );
		$output['block_non_essential_by_default']  = YCC_Sanitizer::checkbox( $input, 'block_non_essential_by_default' );
		$output['google_tag_manager_enabled']      = YCC_Sanitizer::checkbox( $input, 'google_tag_manager_enabled' );
		$output['google_tag_manager_id']           = YCC_Sanitizer::gtm_id( $merged_input, 'google_tag_manager_id' );
		$output['debug_mode']                      = YCC_Sanitizer::checkbox( $input, 'debug_mode' );
		$output['server_side_consent_log_enabled'] = YCC_Sanitizer::checkbox( $input, 'server_side_consent_log_enabled' );
		$output['anonymize_ip_in_logs']            = YCC_Sanitizer::checkbox( $input, 'anonymize_ip_in_logs' );
		$output['cleanup_on_uninstall']            = YCC_Sanitizer::checkbox( $input, 'cleanup_on_uninstall' );

		return $output;
	}
}
