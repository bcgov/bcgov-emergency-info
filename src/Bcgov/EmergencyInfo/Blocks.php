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
        $this->deregister_block_theme_patterns();
    }

    /**
     * Registers blocks and callbacks for dynamic blocks.
     *
     * @codeCoverageIgnore
     * @return void
     */
    public function register_blocks(): void {
        $path        = plugin_dir_path( dirname( __FILE__, 3 ) ) . 'dist/Bcgov/EmergencyInfo/blocks';
        $render_path = plugin_dir_path( dirname( __FILE__, 3 ) ) . 'src/Bcgov/EmergencyInfo/blocks';
        register_block_type_from_metadata( $path . '/active-events', [ 'render_callback' => [ $this, 'active_events_block_render' ] ] );
        register_block_type_from_metadata( $path . '/resource-list', [ 'render_callback' => [ $this, 'resource_list_block_render' ] ] );
        register_block_type_from_metadata(
            $path . '/event-meta',
            [
                // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
				'render_callback' => function ( $attributes ) use ( $render_path ) {
					ob_start();
					require_once $render_path . '/event-meta/render.php';
					$ret = ob_get_contents();
					ob_end_clean();
					return $ret;
				},
			]
        );
        register_block_type_from_metadata( $path . '/post-social-share', [ 'render_callback' => [ $this, 'post_social_share_block_render' ] ] );
    }

    /**
     * Render callback for the Active Events block.
     *
     * @return string
     */
    public function active_events_block_render(): string {
        // Get recent active Events.
        $recent_posts = wp_get_recent_posts(
            array(
                'post_type'   => 'event',
                'post_status' => 'publish',
                'meta_query'  => [
                    'key'   => 'status',
                    'value' => 'active',
                ],
                'orderby'     => 'meta_value',
                'meta_key'    => 'urgency',
            )
        );
        if ( count( $recent_posts ) === 0 ) {
            return '';
        }

        // Build HTML list of Events from above query.
        $ret = '<ul class="is-layout-flow is-flex-container columns-3 wp-block-post-template">';
        foreach ( $recent_posts as $post ) {
            $excerpt = $post['post_excerpt'];
            if ( empty( $excerpt ) ) {
                $excerpt = substr( wp_strip_all_tags( $post['post_content'] ), 0, 100 ) . '...';
            }
            $ret .= sprintf(
                '<li class="wp-block-post event type-event status-publish hentry">
                <h2 class="wp-block-post-title"><a href="%1$s" target="_self">%2$s</a></h2>
                <div class="wp-block-post-excerpt"><p class="wp-block-post-excerpt__excerpt">%3$s</p></div>
                </li>',
                esc_url( get_permalink( $post['ID'] ) ),
                esc_html( $post['post_title'] ),
                $excerpt,
            );
        }
        $ret .= '</ul>';
        return $ret;
    }

    /**
     * Render callback for the Resource List block.
     *
     * @param array $args Block arguments.
     * @return string
     */
    public function resource_list_block_render( $args ): string {
        // Get Resources per query arguments.
        $query_args = [
            'post_type'   => 'resource',
            'post_status' => 'publish',
        ];
        if ( ! empty( $args['hazard_types'] ) ) {
            $query_args['tax_query'] = [
                [
                    'taxonomy' => 'hazard_type',
                    'field'    => 'slug',
                    'terms'    => $args['hazard_types'],
                ],
            ];
        }
        $recent_posts = get_posts( $query_args );
        if ( count( $recent_posts ) === 0 ) {
            return '';
        }

        // Build HTML list of Resources from above query.
        $ret = '<ul class="is-layout-flow is-flex-container columns-3 wp-block-post-template">';
        foreach ( $recent_posts as $post ) {
            $excerpt = $post->post_excerpt;
            if ( empty( $excerpt ) ) {
                $excerpt = substr( wp_strip_all_tags( $post->post_content ), 0, 100 ) . '...';
            }
            $ret .= sprintf(
                '<li class="wp-block-post event type-event status-publish hentry">
                <h2 class="wp-block-post-title"><a href="%1$s" target="_self">%2$s</a></h2>
                <div class="wp-block-post-excerpt"><p class="wp-block-post-excerpt__excerpt">%3$s</p></div>
                </li>',
                esc_url( get_permalink( $post->ID ) ),
                esc_html( $post->post_title ),
                $excerpt,
            );
        }
        $ret .= '</ul>';
        return $ret;
    }

    // Ignore unused fuction arguments error. We only need to use $block.
    // phpcs:disable

    /**
     * Render callback for the Post Social Share block.
     *
     * @param array  $attributes Block attributes that get passed to the render callback.
     * @param string $content The content of the block.
     * @param object $block  Block object containing context data.
     * @return string
     */
    public function post_social_share_block_render( array $attributes, string $content, object $block ): string {
        // phpcs:enable
        if ( ! isset( $block->context['postId'] ) ) {
            return '';
        }

        $post               = get_post( $block->context['postId'] );
        $link               = get_the_permalink( $post );
        $wrapper_attributes = get_block_wrapper_attributes();

        if ( ! $link ) {
            return '';
        }

        return sprintf(
            '<div %1$s> 
                <ul>
                    <li><a href="https://www.facebook.com/sharer/sharer.php?u=%2$s">Facebook</a></li>
                    <li><a href="https://twitter.com/intent/tweet?url=%2$s">Twitter</a></li>
                </ul>
            </div>',
            $wrapper_attributes,
            $link
        );
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
     * Deregisters default Block Theme patterns.
     *
     * @return void
     */
    public function deregister_block_theme_patterns() {
        $block_theme_pattern_categories = [
			'bcgov-blocks-theme-general',
			'bcgov-blocks-theme-header-footer',
			'bcgov-blocks-theme-page-layouts',
			'bcgov-blocks-theme-query',
		];

        $pattern_registry = \WP_Block_Patterns_Registry::get_instance();
        $patterns         = $pattern_registry->get_all_registered();
        foreach ( $patterns as $pattern ) {
            if ( array_intersect( $block_theme_pattern_categories, $pattern['categories'] ) ) {
                unregister_block_pattern( $pattern['name'] );
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
}
