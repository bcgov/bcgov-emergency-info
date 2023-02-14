<?php
/**
 * Blocks for Emergency Info BC.
 *
 * @package Bcgov\EmergencyInfo
 * @since 1.0.0
 */

namespace Bcgov\EmergencyInfo;

use Bcgov\Common\Loader;

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
        $blocks_dist_path = plugin_dir_path( dirname( __FILE__, 3 ) ) . 'dist/Bcgov/EmergencyInfo/blocks/';
        register_block_type( $blocks_dist_path . '/example' );
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
