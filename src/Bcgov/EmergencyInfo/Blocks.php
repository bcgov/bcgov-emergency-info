<?php
/**
 * Blocks for Emergency Info BC.
 *
 * @package Bcgov\EmergencyInfo
 * @since 1.0.0
 */

namespace Bcgov\EmergencyInfo;

use Bcgov\Common\Loader;
use Bcgov\Common\Hooks\SageHook;
use Bcgov\Common\Utils;
use WP_Block_Type;

/**
 * Blocks class setups dynamic blocks.
 */
class Blocks {

    /**
     * Constructor.
     *
     * @codeCoverageIgnore
     */
    public function __construct() {
        $this->init();
    }

    /**
     * Sets up hooks for Blocks.
     *
     * @codeCoverageIgnore
     * @return void
     */
    public function init() {
        $loader = new Loader();
        $loader->add_action( 'init', $this, 'register_blocks' );
        // $loader->add_action( 'enqueue_block_assets', $this, 'enqueue_block_assets' );
        $loader->add_filter( 'block_categories_all', $this, 'block_categories' );
        $loader->run();
    }

    /**
     * Registers blocks and callbacks for dynamic blocks.
     *
     * @codeCoverageIgnore
     * @return void
     */
    public function register_blocks() :void {

        register_block_type(plugin_dir_path( dirname( __FILE__, 3 ) ) . 'dist/Bcgov/EmergencyInfo/blocks/example');

        logger(plugin_dir_path( dirname( __FILE__, 3 ) ) . 'dist/Bcgov/EmergencyInfo/blocks/example');
        // register_block_type(
		// 	'emergency-info/all',
		// 	[ 'editor_script' => $asset_info['handle'] ]
        // );
    }

    public function enqueue_block_assets() {
        $name       = 'blocks';
        $asset_info = Plugin::get_asset_information( $name );
        wp_enqueue_script( $asset_info['handle'], $asset_info['dist_url'] . $name . '.js', $asset_info['dependencies'], $asset_info['version'], false );
    }

    /**
     * Adds Block category.
     *
     * @codeCoverageIgnore
     * @param array $categories  The existing categories.
     * @return array
     */
    public function block_categories( array $categories ) :array {
        return array_merge(
            $categories,
            [
				[
					'slug'  => 'emergency-info',
					'title' => __( 'Emergency Info BC Blocks' ),
                    'icon'  => 'megaphone',
				],
			]
        );
    }
}