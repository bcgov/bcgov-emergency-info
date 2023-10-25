<?php
namespace Bcgov\EmergencyInfo;

use Bcgov\EmergencyInfo\Plugin;
use Bcgov\Common\Loader;

/**
 * The admin-specific functionality of EmergencyInfo.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    EmergencyInfo
 * @subpackage EmergencyInfo/admin
 * @author     GovWordPress <govwordpress@gov.bc.ca>
 */
class Admin {

    /**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
     */
	public function __construct() {
        $this->init();
    }

    /**
     * Sets up hooks for admin area.
     *
     * @codeCoverageIgnore
     * @return void
     */
    public function init() {
        $loader = new Loader();
        $loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_styles' );
        $loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_scripts' );

        // Options Hooks.
		// See [Bcgov/Common/Options/Options](https://apps.itsm.gov.bc.ca/bitbucket/projects/WP/repos/bcgov-wordpress-common/browse/src/Options) for details required.
        $loader->run();
    }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
        $name       = 'admin';
        $asset_info = Plugin::get_asset_information( $name );
        wp_enqueue_style( $asset_info['handle'], $asset_info['dist_url'] . $name . '.css', [], $asset_info['version'] );
	}

	/**
	 * Register the JavaScript for the admin area.
     *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
        $name       = 'admin';
        $asset_info = Plugin::get_asset_information( $name );
        wp_enqueue_script( $asset_info['handle'], $asset_info['dist_url'] . $name . '.js', $asset_info['dependencies'], $asset_info['version'], false );
	}
}
