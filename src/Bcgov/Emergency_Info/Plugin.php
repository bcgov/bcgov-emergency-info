<?php
namespace Bcgov\Emergency_Info;

use Exception;
use Bcgov\Common\Loader;
use Bcgov\Common\Utils;
use Bcgov\Common\i18n;

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
 * @package    Emergency_Info
 * @subpackage Emergency_Info/Plugin
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
		$loader = new Loader();
		new I18n( 'bcgov', dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
		$this->define_admin_hooks( $loader );
		$this->define_public_hooks( $loader );
		$loader->run();
	}


	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @param    Loader $loader   The loader class.
	 * @access   private
	 * @return   void;
	 */
	private function define_admin_hooks( $loader ) :void {
		$plugin_admin = new Admin();
		$loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Options Hooks.
		// See [Bcgov/Common/Options/Options](https://apps.itsm.gov.bc.ca/bitbucket/projects/WP/repos/bcgov-wordpress-common/browse/src/Options) for details required.
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @param    Loader $loader   The loader class.
	 * @access   private
	 * @return   void;
	 */
	private function define_public_hooks( $loader ) :void {
		$plugin_public = new PublicRender();
        if ( ! is_admin() ) {
            $loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles', 20 );
    		$loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', 20 );
        }
	}

	/**
	 * Get asset information including path to dist folder, asset dependencies and version.
	 *
	 * @since   1.0.0
	 * @param   string $name Name of the asset (usually 'admin' or 'public').
	 * @return  array
	 */
	public static function get_asset_information( $name ) :array {
		$dist_path       = plugin_dir_path( dirname( __FILE__, 3 ) ) . 'dist/';
        $dist_url        = plugin_dir_url( dirname( __FILE__, 3 ) ) . 'dist/';
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
