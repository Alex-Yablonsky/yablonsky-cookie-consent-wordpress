<?php
/**
 * Plugin Name:       Yablonsky Cookie Consent for WordPress
 * Plugin URI:        https://yablonsky.io/yablonsky-cookie-consent-for-wordpress/
 * Description:       Open-source cookie consent and Google Consent Mode plugin for WordPress.
 * Version:           1.0.5
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Yablonsky.io
 * Author URI:        https://yablonsky.io/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       yablonsky-cookie-consent
 * Domain Path:       /languages
 *
 * @package YablonskyCookieConsent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'YCC_VERSION', '1.0.5' );
define( 'YCC_PLUGIN_FILE', __FILE__ );
define( 'YCC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'YCC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'YCC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'YCC_OPTION_NAME', 'ycc_settings' );
define( 'YCC_TEXT_DOMAIN', 'yablonsky-cookie-consent' );

require_once YCC_PLUGIN_DIR . 'includes/class-ycc-activator.php';
require_once YCC_PLUGIN_DIR . 'includes/class-ycc-deactivator.php';
require_once YCC_PLUGIN_DIR . 'includes/class-ycc-sanitizer.php';
require_once YCC_PLUGIN_DIR . 'includes/class-ycc-settings.php';
require_once YCC_PLUGIN_DIR . 'includes/class-ycc-admin.php';
require_once YCC_PLUGIN_DIR . 'includes/class-ycc-frontend.php';
require_once YCC_PLUGIN_DIR . 'includes/class-ycc-plugin.php';

register_activation_hook( __FILE__, array( 'YCC_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'YCC_Deactivator', 'deactivate' ) );

/**
 * Start the plugin.
 *
 * @return void
 */
function ycc_run_plugin() {
	$plugin = new YCC_Plugin();
	$plugin->run();
}

ycc_run_plugin();
