<?php
/**
 * Frontend consent banner and Google Consent Mode bootstrap.
 *
 * @package YablonskyCookieConsent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend handler.
 */
class YCC_Frontend {

	/**
	 * Settings model.
	 *
	 * @var YCC_Settings
	 */
	private $settings;

	/**
	 * Constructor.
	 *
	 * @param YCC_Settings $settings Settings model.
	 */
	public function __construct( YCC_Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Register frontend hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'wp_head', array( $this, 'print_consent_bootstrap' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_head', array( $this, 'print_custom_css' ), 100 );
		add_action( 'wp_footer', array( $this, 'render_root' ) );
	}

	/**
	 * Print early Google Consent Mode default and returning visitor GTM logic.
	 *
	 * @return void
	 */
	public function print_consent_bootstrap() {
		$options = $this->settings->get_settings();

		if ( empty( $options['enabled'] ) || empty( $options['google_consent_mode_enabled'] ) ) {
			return;
		}

		$config = array(
			'storageKey'    => 'yablonsky_cookie_consent',
			'policyVersion' => (string) $options['policy_version'],
			'gtmEnabled'    => ! empty( $options['google_tag_manager_enabled'] ),
			'gtmId'         => (string) $options['google_tag_manager_id'],
			'debug'         => ! empty( $options['debug_mode'] ),
		);
		?>
<script id="ycc-consent-bootstrap">
window.dataLayer = window.dataLayer || [];
function gtag(){window.dataLayer.push(arguments);}
(function(){
	'use strict';
	var config = <?php echo wp_json_encode( $config ); ?>;

	function yccLog(message, data) {
		if (config.debug && window.console && window.console.log) {
			window.console.log('[YCC] ' + message, data || '');
		}
	}

	function mapConsent(consent) {
		var analyticsGranted = !!(consent && consent.analytics);
		var marketingGranted = !!(consent && consent.marketing);
		var functionalGranted = !!(consent && consent.functional);

		return {
			'ad_storage': marketingGranted ? 'granted' : 'denied',
			'analytics_storage': analyticsGranted ? 'granted' : 'denied',
			'ad_user_data': marketingGranted ? 'granted' : 'denied',
			'ad_personalization': marketingGranted ? 'granted' : 'denied',
			'functionality_storage': functionalGranted ? 'granted' : 'denied',
			'security_storage': 'granted',
			'personalization_storage': functionalGranted ? 'granted' : 'denied'
		};
	}

	function loadGtm(containerId) {
		if (!containerId || window.yccGtmLoaded) {
			return;
		}

		window.yccGtmLoaded = true;
		window.dataLayer.push({
			'gtm.start': new Date().getTime(),
			event: 'gtm.js'
		});

		var firstScript = document.getElementsByTagName('script')[0];
		var script = document.createElement('script');
		script.async = true;
		script.src = 'https://www.googletagmanager.com/gtm.js?id=' + encodeURIComponent(containerId);
		firstScript.parentNode.insertBefore(script, firstScript);
		yccLog('GTM loaded', containerId);
	}

	function getStoredConsent() {
		try {
			var raw = window.localStorage ? window.localStorage.getItem(config.storageKey) : null;
			if (!raw) {
				return null;
			}

			var parsed = JSON.parse(raw);
			if (!parsed || parsed.version !== config.policyVersion) {
				return null;
			}

			return parsed;
		} catch (error) {
			return null;
		}
	}

	window.yccLoadGtm = loadGtm;
	window.yccApplyConsent = function(consent) {
		var mapped = mapConsent(consent);
		gtag('consent', 'update', mapped);

		window.yccConsentApplied = true;
		window.yccCurrentConsent = consent;

		yccLog('Consent updated', mapped);

		if (config.gtmEnabled && config.gtmId && (consent.analytics || consent.marketing)) {
			loadGtm(config.gtmId);
		}
	};

	gtag('consent', 'default', {
		'ad_storage': 'denied',
		'analytics_storage': 'denied',
		'ad_user_data': 'denied',
		'ad_personalization': 'denied',
		'functionality_storage': 'granted',
		'security_storage': 'granted',
		'personalization_storage': 'denied',
		'wait_for_update': 500
	});
	yccLog('Default consent denied state applied');

	var storedConsent = getStoredConsent();
	if (storedConsent) {
		window.yccApplyConsent(storedConsent);
	}
})();
</script>
		<?php
	}

	/**
	 * Enqueue frontend assets when enabled.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		$options = $this->settings->get_settings();

		if ( empty( $options['enabled'] ) ) {
			return;
		}

		wp_enqueue_style(
			'ycc-cookie-banner',
			YCC_PLUGIN_URL . 'public/css/cookie-banner.css',
			array(),
			YCC_VERSION
		);

		wp_enqueue_script(
			'ycc-cookie-banner',
			YCC_PLUGIN_URL . 'public/js/cookie-banner.js',
			array(),
			YCC_VERSION,
			true
		);

		wp_localize_script(
			'ycc-cookie-banner',
			'yccSettings',
			$this->get_frontend_config( $options )
		);
	}

	/**
	 * Print custom CSS from settings.
	 *
	 * @return void
	 */
	public function print_custom_css() {
		$options = $this->settings->get_settings();

		if ( empty( $options['enabled'] ) || empty( $options['custom_css'] ) ) {
			return;
		}

		echo '<style id="ycc-custom-css">' . esc_html( $options['custom_css'] ) . '</style>';
	}

	/**
	 * Render frontend root placeholder.
	 *
	 * @return void
	 */
	public function render_root() {
		$options = $this->settings->get_settings();

		if ( empty( $options['enabled'] ) ) {
			return;
		}

		echo '<div id="yablonsky-cookie-consent-root"></div>';
	}

	/**
	 * Get frontend configuration.
	 *
	 * @param array $options Settings array.
	 * @return array
	 */
	private function get_frontend_config( $options ) {
		return array(
			'storageKey'              => 'yablonsky_cookie_consent',
			'policyVersion'           => (string) $options['policy_version'],
			'expiryDays'              => absint( $options['consent_expiry_days'] ),
			'debug'                   => ! empty( $options['debug_mode'] ),
			'customCssClass'          => (string) $options['custom_css_class'],
			'privacyPolicyUrl'        => esc_url_raw( $options['privacy_policy_url'] ),
			'cookiePolicyUrl'         => esc_url_raw( $options['cookie_policy_url'] ),
			'termsUrl'                => esc_url_raw( $options['terms_url'] ),
			'googleTagManagerEnabled' => ! empty( $options['google_tag_manager_enabled'] ),
			'googleTagManagerId'      => (string) $options['google_tag_manager_id'],
			'labels'                  => array(
				'bannerTitle'        => (string) $options['banner_title'],
				'bannerMessage'      => (string) $options['banner_message'],
				'acceptAll'          => (string) $options['accept_all_label'],
				'rejectNonEssential' => (string) $options['reject_non_essential_label'],
				'settings'           => (string) $options['settings_label'],
				'savePreferences'    => (string) $options['save_preferences_label'],
				'close'              => (string) $options['close_label'],
				'privacyPolicy'      => (string) $options['privacy_policy_label'],
				'cookiePolicy'       => (string) $options['cookie_policy_label'],
				'terms'              => (string) $options['terms_label'],
				'modalTitle'         => (string) $options['modal_title'],
				'locked'             => (string) $options['always_active_label'],
			),
			'categories'              => array(
				'necessary'  => array(
					'label'       => (string) $options['necessary_label'],
					'description' => (string) $options['necessary_description'],
				),
				'analytics'  => array(
					'label'       => (string) $options['analytics_label'],
					'description' => (string) $options['analytics_description'],
				),
				'marketing'  => array(
					'label'       => (string) $options['marketing_label'],
					'description' => (string) $options['marketing_description'],
				),
				'functional' => array(
					'label'       => (string) $options['functional_label'],
					'description' => (string) $options['functional_description'],
				),
			),
		);
	}
}
