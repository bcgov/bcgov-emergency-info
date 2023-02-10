<?php
namespace Bcgov\EmergencyInfo;

use \Bcgov\EmergencyInfo\Plugin;

/**
 * The public-facing functionality of EmergencyInfo.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    EmergencyInfo
 * @subpackage EmergencyInfo/public
 * @author     GovWordPress <govwordpress@gov.bc.ca>
 */
class PublicRender {

    /**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
     */
	public function __construct() { }

	/**
	 * Register the stylesheets for the public-facing side of the site.
     *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
        $name       = 'public';
        $asset_info = Plugin::get_asset_information( $name );
        wp_enqueue_style( $asset_info['handle'], $asset_info['dist_url'] . $name . '.css', [], $asset_info['version'] );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
     *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
        $name       = 'public';
        $asset_info = Plugin::get_asset_information( $name );
        wp_enqueue_script( $asset_info['handle'], $asset_info['dist_url'] . $name . '.js', $asset_info['dependencies'], $asset_info['version'], false );
	}

}
