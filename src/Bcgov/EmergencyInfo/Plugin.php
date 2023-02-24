<?php
namespace Bcgov\EmergencyInfo;

use Bcgov\Common\Loader;
use Bcgov\Common\I18n;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 * @package    EmergencyInfo
 * @subpackage EmergencyInfo/Plugin
 * @author     GovWordPress <govwordpress@gov.bc.ca>
 */
class Plugin {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	public static $plugin_name = 'emergency-info';


	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		new I18n( 'bcgov', dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
		new Admin();
		new PublicRender();
        new Blocks();
	}

	/**
	 * Get asset information including path to dist folder, asset dependencies and version.
	 *
	 * @since   1.0.0
	 * @param   string $name Name of the asset (usually 'admin' or 'public').
	 * @return  array
	 */
	public static function get_asset_information( $name ) :array {
		$dist_path       = plugin_dir_path( dirname( __FILE__, 3 ) ) . 'dist/scripts/';
        $dist_url        = plugin_dir_url( dirname( __FILE__, 3 ) ) . 'dist/scripts/';
        $asset_file_path = $dist_path . $name . '.asset.php';
        $dependencies    = [];
        $version         = false;

        if ( file_exists( $asset_file_path ) ) {
            $asset        = require $asset_file_path;
            $dependencies = $asset['dependencies'];
            $version      = $asset['version'];
        }

        return [
            'handle'       => self::$plugin_name . '-' . $name,
            'dist_url'     => $dist_url,
            'dependencies' => $dependencies,
            'version'      => $version,
        ];
	}


    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public static function get_plugin_name() :string {
        return self::$plugin_name;
	}

    /**
     * Gets option name.
     *
     * @return string
     */
    public static function get_option_name() :string {
        return str_replace( '-', '_', self::get_plugin_name() );
    }
}
