<?php
namespace Bcgov\Plugin_Name;

use \Bcgov\Plugin_Name\Plugin;

/**
 * The admin-specific functionality of Plugin_Name.
 *
 * @link       http://example.com
 * @since      1.0.0
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

/**
 * The admin-specific functionality of Plugin_Name.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     GovWordPress <govwordpress@gov.bc.ca>
 */
class Admin {

    /**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
     */
	public function __construct() { }

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
