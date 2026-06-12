<?php
/**
 * Plugin deactivation tasks.
 *
 * @package YablonskyCookieConsent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Deactivation handler.
 */
class YCC_Deactivator {

	/**
	 * Run deactivation tasks.
	 *
	 * @return void
	 */
	public static function deactivate() {
		// No destructive action on deactivation.
	}
}
