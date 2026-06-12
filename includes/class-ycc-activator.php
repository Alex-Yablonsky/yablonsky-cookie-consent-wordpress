<?php
/**
 * Plugin activation tasks.
 *
 * @package YablonskyCookieConsent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Activation handler.
 */
class YCC_Activator {

	/**
	 * Run activation tasks.
	 *
	 * @return void
	 */
	public static function activate() {
		$defaults = YCC_Settings::get_defaults();

		if ( false === get_option( YCC_OPTION_NAME ) ) {
			add_option( YCC_OPTION_NAME, $defaults );
		}
	}
}
