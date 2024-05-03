<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://apps.itsm.gov.bc.ca/bitbucket/projects/WPP/repos/bcgov-emergency-info
 * @since             1.0.0
 * @package           EmergencyInfo
 *
 * @wordpress-plugin
 * Plugin Name:       BCGov Emergency Info BC
 * Plugin URI:        https://apps.itsm.gov.bc.ca/bitbucket/projects/WPP/repos/bcgov-emergency-info
 * Description:       Provides custom functionality for the Emergency Info BC (EIBC) site.
 * Version:           1.2.0
 * Author:            Michael Haswell
 * Author URI:        https://apps.itsm.gov.bc.ca/bitbucket/users/mhaswell
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       emergency-info
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
* Loads the autoloader.
*/
if ( ! class_exists( 'Bcgov\\EmergencyInfo\\Plugin' ) ) {
    $local_composer  = __DIR__ . '/vendor/autoload.php';
    $server_composer = __DIR__ . '/../../../../vendor/autoload.php';
    if ( file_exists( $local_composer ) || file_exists( $server_composer ) ) {
        if ( file_exists( $server_composer ) ) {
            require_once $server_composer;
        }
        if ( ! class_exists( 'Bcgov\\EmergencyInfo\\Plugin' ) ) {
            require_once $local_composer;
        }
    }
}

/**
 * The code that runs during plugin activation.
 */
function activate_emergency_info() {}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_emergency_info() {}

register_activation_hook( __FILE__, 'activate_emergency_info' );
register_deactivation_hook( __FILE__, 'deactivate_emergency_info' );


/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_emergency_info() {
	if ( class_exists( 'Bcgov\EmergencyInfo\Plugin' ) ) {
		new Bcgov\EmergencyInfo\Plugin();
	}
}

/** This is to ensure that the common-plugin gets loaded before this plugin, otherwise admin functions will not work. */
add_action(
    'plugins_loaded',
    function () {
		run_emergency_info();
	}
);
