<?php
/**
 * The plugin bootstrap file
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Plugin_Name
 *
 * @wordpress-plugin
 * Plugin Name:       BCGov WordPress Plugin Boilerplate
 * Plugin URI:        http://example.com
 * Description:       Plugin_Description.
 * Version:           1.3.0
 * Author:            Your Name
 * Author URI:        http://author.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       plugin-name
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
* Loads the autoloader.
*/
if ( ! class_exists( 'Bcgov\\Plugin_Name\\Plugin' ) ) {
    $local_composer  = __DIR__ . '/vendor/autoload.php';
    $server_composer = __DIR__ . '/../../../../vendor/autoload.php';
    if ( file_exists( $local_composer ) || file_exists( $server_composer ) ) {
        if ( file_exists( $server_composer ) ) {
            require_once $server_composer;
        }
        if ( ! class_exists( 'Bcgov\\Plugin_Name\\Plugin' ) ) {
            require_once $local_composer;
        }
    }
}

/**
 * The code that runs during plugin activation.
 */
function activate_plugin_name() {}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_plugin_name() {}

register_activation_hook( __FILE__, 'activate_plugin_name' );
register_deactivation_hook( __FILE__, 'deactivate_plugin_name' );


/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_plugin_name() {
	if ( class_exists( 'Bcgov\Plugin_Name\Plugin' ) ) {
		new Bcgov\Plugin_Name\Plugin();
	}
}

/** This is to ensure that the common-plugin gets loaded before this plugin, otherwise admin functions will not work. */
add_action(
    'plugins_loaded',
    function() {
		run_plugin_name();
	}
);
