<?php
/**
 * Main plugin coordinator.
 *
 * @package YablonskyCookieConsent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
class YCC_Plugin {

	/**
	 * Admin handler.
	 *
	 * @var YCC_Admin
	 */
	private $admin;

	/**
	 * Frontend handler.
	 *
	 * @var YCC_Frontend
	 */
	private $frontend;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$settings       = new YCC_Settings();
		$this->admin    = new YCC_Admin( $settings );
		$this->frontend = new YCC_Frontend( $settings );
	}

	/**
	 * Register plugin hooks.
	 *
	 * @return void
	 */
	public function run() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		if ( is_admin() ) {
			$this->admin->register_hooks();
		}

		$this->frontend->register_hooks();
	}

	/**
	 * Load translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			YCC_TEXT_DOMAIN,
			false,
			dirname( YCC_PLUGIN_BASENAME ) . '/languages'
		);
	}
}
