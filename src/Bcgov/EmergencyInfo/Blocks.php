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
        add_filter( 'bcgov_blocks_theme_block_patterns', '__return_empty_array' );
        $loader = new Loader();
        $loader->add_filter( 'block_categories_all', $this, 'block_categories' );
        $loader->add_action( 'init', $this, 'register_all' );
        $loader->add_filter( 'render_block', $this, 'render_block', 10, 2 );
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
    public function register_blocks(): void {
        $path = plugin_dir_path( dirname( __DIR__, 2 ) ) . 'dist/Bcgov/EmergencyInfo/blocks';
        include_once __DIR__ . '/blocks/post-emergency-alert/index.php';
        include_once __DIR__ . '/blocks/post-hazard-image/index.php';
        include_once __DIR__ . '/blocks/post-hazard-title/index.php';
        include_once __DIR__ . '/blocks/post-meta-display/index.php';
        include_once __DIR__ . '/blocks/post-social-share/index.php';
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
    public function block_categories( array $categories ): array {
        return array_merge(
            $categories,
            [
                [
                    'slug'  => 'emergency-info',
                    'title' => __( 'Emergency Info BC Blocks' ),
                ],
            ]
        );
    }

    /**
     * Prevents rendering of blocks with hideBlock attribute set to true.
     *
     * @see src/scripts/admin/hide-block.js
     *
     * @param string $block_content
     * @param array  $block
     * @return string Original block content or empty string if hideBlock is true.
     */
    public function render_block( string $block_content = '', array $block = [] ): string {
        $attrs      = $block['attrs'] ?? [];
        $hide_block = $attrs['hideBlock'] ?? false;
        if ( true === $hide_block ) {
            $block_content = '';
        }
        return $block_content;
    }
}
