<?php
/**
 * Plugin uninstall cleanup.
 *
 * @package YablonskyCookieConsent
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$settings = get_option( 'ycc_settings', array() );

if ( is_array( $settings ) && ! empty( $settings['cleanup_on_uninstall'] ) ) {
	delete_option( 'ycc_settings' );
}
