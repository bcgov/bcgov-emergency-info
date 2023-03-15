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
     * The plugin root directory.
     *
     * @var string $plugin_dir The path to this plugin's root directory.
     */
    public static $plugin_dir = WP_PLUGIN_DIR . '/bcgov-emergency-info/';

    /**
     * The name of the directory that stores ACF JSON files.
     *
     * @var string $acf_json_directory The name of the directory that stores ACF JSON files.
     */
    public static $acf_json_directory = 'acf-json';

    /**
     * The name of the directory that stores CPT UI JSON files.
     *
     * @var string $cpt_ui_json_directory The name of the directory that stores CPT UI JSON files.
     */
    public static $cpt_ui_json_directory = 'cpt-ui-json';

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

        $loader = new Loader();
        $loader->add_filter( 'acf/settings/save_json', $this, 'acf_json_save_point' );
        $loader->add_filter( 'acf/settings/load_json', $this, 'acf_json_load_point' );
        $loader->add_action( 'cptui_after_update_post_type', $this, 'pluginize_local_cptui_data' );
        $loader->add_action( 'cptui_after_update_taxonomy', $this, 'pluginize_local_cptui_data' );
        $loader->add_filter( 'cptui_post_types_override', $this, 'pluginize_load_local_cptui_post_type_data' );
        $loader->add_filter( 'cptui_taxonomies_override', $this, 'pluginize_load_local_cptui_taxonomies_data' );
        $loader->run();
	}

	/**
	 * Get asset information including path to dist folder, asset dependencies and version.
	 *
	 * @since   1.0.0
	 * @param   string $name Name of the asset (usually 'admin' or 'public').
	 * @return  array
	 */
	public static function get_asset_information( $name ) :array {
		$dist_path       = self::$plugin_dir . 'dist/scripts/';
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
     * Defines path to save Advanced Custom Fields' JSON files.
     *
     * @param string $path
     * @return string
     */
    public static function acf_json_save_point( $path ) {
        $acf_path = self::$plugin_dir . self::$acf_json_directory;
        return $acf_path;
    }

    /**
     * Defines paths to load Advanced Custom Fields' JSON files.
     *
     * @param array $paths
     * @return array
     */
    public static function acf_json_load_point( $paths ) {
        unset( $paths[0] );
        $acf_path = self::$plugin_dir . self::$acf_json_directory;
        $paths[]  = $acf_path;
        return $paths;
    }

    /**
     * Saves post type and taxonomy data to JSON files in the theme directory.
     *
     * @param array $data Array of post type data that was just saved.
     */
    public function pluginize_local_cptui_data( $data = array() ) {
        $cpt_ui_path = self::$plugin_dir . self::$cpt_ui_json_directory;
        if ( ! is_dir( $cpt_ui_path ) ) {
            mkdir( $cpt_ui_path, 0755 );
        }

        if ( array_key_exists( 'cpt_custom_post_type', $data ) ) {
            // Fetch all of our post types and encode into JSON.
            $cptui_post_types = get_option( 'cptui_post_types', array() );
            $content          = wp_json_encode( $cptui_post_types, JSON_PRETTY_PRINT );
            // Save the encoded JSON to a primary file holding all of them.
            file_put_contents( $cpt_ui_path . '/cptui_post_type_data.json', $content );
        }

        if ( array_key_exists( 'cpt_custom_tax', $data ) ) {
            // Fetch all of our taxonomies and encode into JSON.
            $cptui_taxonomies = get_option( 'cptui_taxonomies', array() );
            $content          = wp_json_encode( $cptui_taxonomies, JSON_PRETTY_PRINT );
            // Save the encoded JSON to a primary file holding all of them.
            file_put_contents( $cpt_ui_path . '/cptui_taxonomy_data.json', $content );
        }
    }

    /**
     * Load local post type JSON data.
     *
     * @param array $data Existing CPT data.
     * @return string $value overriding content for CPTUI
     */
    public function pluginize_load_local_cptui_post_type_data( $data ) {
        $loaded = $this->pluginize_load_local_cptui_data( 'cptui_post_type_data.json' );

        if ( false === $loaded ) {
            return $data;
        }

        $data_new = json_decode( $loaded, true );

        if ( $data_new ) {
            return $data_new;
        }

        return $data;
    }

    /**
     * Load local taxonomy JSON data.
     *
     * @param array $data Existing taxonomy data.
     * @return string $value overriding content for CPTUI
     */
    public function pluginize_load_local_cptui_taxonomies_data( $data ) {
        $loaded = $this->pluginize_load_local_cptui_data( 'cptui_taxonomy_data.json' );

        if ( false === $loaded ) {
            return $data;
        }

        $data_new = json_decode( $loaded, true );

        if ( $data_new ) {
            return $data_new;
        }

        return $data;
    }

    /**
     * Helper function to load a specific file.
     *
     * @param string $file_name Name of the local JSON file.
     * @return false|string
     */
    private function pluginize_load_local_cptui_data( $file_name = '' ) {
        if ( empty( $file_name ) ) {
            return false;
        }
        $cpt_ui_path = self::$plugin_dir . self::$cpt_ui_json_directory;
        $path        = $cpt_ui_path . '/' . $file_name;

        return file_get_contents( $path );
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
