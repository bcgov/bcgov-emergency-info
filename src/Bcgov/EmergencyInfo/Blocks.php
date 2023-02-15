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
        $loader->add_filter( 'block_categories_all', $this, 'block_categories' );
        $loader->add_action( 'init', $this, 'register_all' );
        $loader->run();
    }

    /**
     * Registers all blocks and block patterns.
     *
     * @return void
     */
    public function register_all(): void {
        $this->register_blocks();
        $this->register_patterns();
    }

    /**
     * Registers blocks and callbacks for dynamic blocks.
     *
     * @codeCoverageIgnore
     * @return void
     */
    public function register_blocks() :void {
        $path = plugin_dir_path( dirname( __FILE__, 3 ) ) . 'dist/Bcgov/EmergencyInfo/blocks';
        register_block_type( $path . '/example' );
    }

    /**
     * Registers block patterns.
     *
     * @codeCoverageIgnore
     * @return void
     */
    public function register_patterns() {
        $path = plugin_dir_path( __FILE__ ) . 'patterns/';

        // Register pattern categories.
        $block_pattern_categories = [
            'emergency-info-bc-general' => [ 'label' => __( 'Emergency Info BC General' ) ],
        ];
        foreach ( $block_pattern_categories as $name => $properties ) {
            register_block_pattern_category( $name, $properties );
        }

        // Register all block patterns found in patterns directory.
        $block_patterns = glob( $path . '*.php' );
        if ( function_exists( 'register_block_pattern' ) ) {
            foreach ( $block_patterns as $block_pattern ) {
                $pattern_name = basename( $block_pattern, '.php' );
                register_block_pattern(
                    'emergency-info-bc/' . $pattern_name,
                    require $block_pattern
                );
            }
        }
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
