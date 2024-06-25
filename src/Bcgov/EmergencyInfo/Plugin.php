<?php
namespace Bcgov\EmergencyInfo;

use Bcgov\Common\Loader;
use Bcgov\Common\I18n;
use WP_Theme_JSON_Data;
use WP_REST_Response;

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
     * Array of event statuses that should display with inactive styling.
     *
     * @var array $inactive_statuses Array of event statuses that should display with inactive styling.
     */
    public static $inactive_statuses = [ 'expired', 'referred' ];

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
        $cpt = new CustomPostTypes( self::$plugin_name, self::$plugin_dir );
        $cpt->init();

        remove_filter( 'notify_subscription_fields', [ 'Bcgov\NotifyClient\NotifyController', 'build_subscription_criteria_list' ], 10 );

        $loader = new Loader();
        $loader->add_filter( 'aioseo_limit_modified_date_post_types', $this, 'disable_limit_modified_date' );

        $loader->add_filter( 'query_loop_block_query_vars', $this, 'query_loop_block_query_vars' );
        $loader->add_filter( 'wp_theme_json_data_theme', $this, 'filter_theme_json_theme' );
        $loader->add_filter( 'body_class', $this, 'add_custom_classes_to_single' );
        $loader->add_filter( 'admin_body_class', $this, 'add_custom_classes_to_admin' );
        $loader->add_action( 'wp_head', $this, 'build_hazard_styles' );
        $loader->add_action( 'admin_head', $this, 'build_hazard_styles' );
        $loader->add_action( 'admin_menu', $this, 'remove_menu_items' );
        $loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_jquery_ui' );
        $loader->add_filter( 'notify_subscription_fields', $this, 'set_notify_subscription_fields', 11, 1 );
        $loader->add_filter( 'notify_subscription_criteria_list', $this, 'set_subscription_criteria_list', 11, 2 );
        $loader->add_filter( 'notify_can_post_be_notified', $this, 'can_post_be_notified' );
        $loader->add_action( 'rest_api_init', $this, 'register_rest_routes' );

        $loader->run();
    }

    /**
     * Gets a meta field value by key and post id.
     * Prevents fatal errors when Advanced Custom Fields plugin is not enabled.
     *
     * @param string $selector
     * @param mixed  $post_id
     * @param bool   $format_value
     * @return mixed
     * @see https://www.advancedcustomfields.com/resources/get_field/
     */
    public static function get_field( $selector, $post_id = false, $format_value = true ) {
        if ( ! function_exists( 'get_field' ) ) {
            $result = get_post_meta( $post_id, $selector, true );
            // Leaving error_log() here to indicate the problem in server logs.
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
            error_log( 'Emergency Info Plugin: ACF required but not enabled.' );
            return $result;
        }
        return get_field( $selector, $post_id, $format_value );
    }

	/**
	 * Get asset information including path to dist folder, asset dependencies and version.
	 *
	 * @since   1.0.0
	 * @param   string $name Name of the asset (usually 'admin' or 'public').
	 * @return  array
	 */
	public static function get_asset_information( string $name ): array {
		$dist_path       = self::$plugin_dir . 'dist/scripts/';
        $dist_url        = plugin_dir_url( dirname( __DIR__, 2 ) ) . 'dist/scripts/';
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
     * Removes the "Don't update the modified date" checkbox from the Event settings panel in the editor.
     * This feature comes from the All In One SEO plugin.
     *
     * @param array $post_types
     * @return array
     */
    public function disable_limit_modified_date( array $post_types ): array {
        return array_diff( $post_types, [ 'event' ] );
    }

    /**
     * Changes Query Loop Block's query to add additional filtering logic.
     *
     * @param array $query Query used by the Query Loop Block on the frontend.
     * @return array The query with additional filtering added.
     */
	public function query_loop_block_query_vars( array $query ): array {
		// Skip if the query loop isn't querying events.
		if ( 'event' !== $query['post_type'] ) {
			return $query;
		}

		// Add a tax_query for the hazard type if it's the queried object.
		// This allows the query loop to be used on hazard type archive pages.
		$queried_object = get_queried_object();
		if ( 'hazard_type' === $queried_object->taxonomy ) {
			$query['tax_query'] = [
				[
					'taxonomy' => $queried_object->taxonomy,
					'terms'    => $queried_object->term_id,
				],
			];
		}

		$query['meta_key']     = 'status';
		$query['meta_value']   = 'active';
		$query['meta_compare'] = '=';

		$query['meta_query'] = [
			'relation'                             => 'AND',
			'event_updated_date_clause'            => [
				'key'     => 'updated_date',
				'compare' => 'EXISTS',
			],
			'event_updated_time_clause'            => [
				'key'     => 'updated_time',
				'compare' => 'EXISTS',
			],
			'event_hidden_clause'                  => [
				'relation'                => 'OR',
				'event_hidden_not_exists' => [
					'key'     => 'hidden',
					'compare' => 'NOT EXISTS',
				],
				'event_not_hidden'        => [
					'key'     => 'hidden',
					'compare' => '!=',
					'value'   => '1',
				],
			],
			'provincial_state_of_emergency_clause' => [
				'key'     => 'provincial_state_of_emergency',
				'compare' => 'EXISTS',
			],
		];

		$query['orderby'] = [
			'provincial_state_of_emergency_clause' => 'DESC',
			'event_updated_date_clause'            => 'DESC',
			'event_updated_time_clause'            => 'DESC',
		];

		return $query;
	}

    /**
     * Adds hazard_type colors to theme's preset colors array.
     *
     * @param WP_Theme_JSON_Data $theme_json Object containing theme's JSON configuration.
     * @return WP_Theme_JSON_Data
     */
    public function filter_theme_json_theme( WP_Theme_JSON_Data $theme_json ) {
        $current_data = $theme_json->get_data();
        $old_colours  = $current_data['settings']['color']['palette']['custom'] ?? [];

        // Add theme colors.
        $new_colours = [
            [
                'slug'  => 'support-gray-00',
                'color' => '#FAFBFF',
                'name'  => __( 'Support Gray 00' ),
            ],
            [
                'slug'  => 'support-gray-01',
                'color' => '#F9FAFF',
                'name'  => __( 'Support Gray 01' ),
            ],
            [
                'slug'  => 'support-gray-02',
                'color' => '#DBDFF0',
                'name'  => __( 'Support Gray 02' ),
            ],
            [
                'slug'  => 'support-green',
                'color' => '#388A5E',
                'name'  => __( 'Support Green' ),
            ],
            [
                'slug'  => 'support-yellow',
                'color' => '#F88907',
                'name'  => __( 'Support Yellow' ),
            ],
            [
                'slug'  => 'support-red',
                'color' => '#D90932',
                'name'  => __( 'Support Red' ),
            ],
            [
                'slug'  => 'emergency-alert',
                'color' => '#EAA00B',
                'name'  => __( 'Emergency Alert' ),
            ],
            [
                'slug'  => 'emergency-alert-secondary',
                'color' => '#F8CD77',
                'name'  => __( 'Emergency Alert Secondary' ),
            ],
        ];

        // Get all terms from hazard_type taxonomy.
        $hazard_types = get_terms(
            [
				'taxonomy'   => 'hazard_type',
				'hide_empty' => false,
                'number'     => 0,
			]
        );
        // Add primary and secondary colors for each hazard_type.
        foreach ( $hazard_types as $hazard_type ) {
            $hazard_colour           = self::get_field( 'colour', 'hazard_type_' . $hazard_type->term_id );
            $hazard_secondary_colour = self::get_field( 'secondary_colour', 'hazard_type_' . $hazard_type->term_id );
            $hazard_name             = $hazard_type->name;
            $new_colours[]           = [
                'slug'  => 'hazard-' . $hazard_type->slug,
                'color' => $hazard_colour,
                'name'  => $hazard_name . ' primary',
            ];
            $new_colours[]           = [
                'slug'  => 'hazard-' . $hazard_type->slug . '-secondary',
                'color' => $hazard_secondary_colour,
                'name'  => $hazard_name . ' secondary',
            ];
        }

        // Add inactive hazard colors.
        $new_colours[] = [
            'slug'  => 'hazard-inactive',
            'color' => '#6B6A85',
            'name'  => __( 'Hazard inactive primary' ),
        ];
        $new_colours[] = [
            'slug'  => 'hazard-inactive-secondary',
            'color' => '#9393A9',
            'name'  => __( 'Hazard inactive secondary' ),
        ];

        $current_data['settings']['color']['palette']['custom'] = array_merge( $old_colours, $new_colours );

        return $theme_json->update_with( $current_data );
    }

    /**
     * Builds styles for each hazard type term.
     */
    public function build_hazard_styles() {
        // CSS for striped borders.
        $striped_border_css = '
            background: linear-gradient(white, white) padding-box,
                        repeating-linear-gradient(
                            -45deg,
                            var(--wp--preset--color--hazard-%1$s),
                            var(--wp--preset--color--hazard-%1$s) 12px,
                            var(--wp--preset--color--hazard-%1$s-secondary) 12px,
                            var(--wp--preset--color--hazard-%1$s-secondary) 24px
                        ) border-box;
            border-color: transparent;
        ';

        // Get hazard type terms.
        $hazard_types = get_terms(
            [
				'taxonomy'   => 'hazard_type',
				'hide_empty' => false,
			]
        );
        $styles       = [];
        foreach ( $hazard_types as $hazard_type ) {
            // Base hazard type styles (text color, background colors).
            $hazard_type_styles = '
                .hazard_type-%1$s:not(.inactive) .hazard-text {
                    color:var(--wp--preset--color--hazard-%1$s)
                }
                .hazard_type-%1$s:not(.inactive) .hazard-text i {
                    color:var(--wp--preset--color--hazard-%1$s)!important
                }
                .hazard_type-%1$s:not(.inactive) .hazard-background {
                    background-color:var(--wp--preset--color--hazard-%1$s)
                }
                .hazard_type-%1$s:not(.inactive) .hazard-background-secondary {
                    background-color:var(--wp--preset--color--hazard-%1$s-secondary)
                }
                .hazard_type-%1$s:not(.inactive) .hazard-border {
                    border-color:var(--wp--preset--color--hazard-%1$s)
                }
            ';

            // Add the styles for striped borders if the hazard should use them.
            $has_striped_border = self::get_field( 'has_striped_border', 'hazard_type_' . $hazard_type->term_id );
            if ( $has_striped_border ) {
                $hazard_type_styles .= "
                    .hazard_type-%1\$s:not(.inactive) #Event-Information.hazard-border {
                        $striped_border_css
                    }
                    .event-query-loop .hazard_type-%1\$s:not(.inactive) .hazard-border {
                        $striped_border_css
                    }
                ";
            }

            $styles[] = sprintf( $hazard_type_styles, $hazard_type->slug );
        }
        if ( count( $styles ) > 0 ) {
            $css = sprintf( '<style id="hazard-styles">%s</style>', implode( '', $styles ) );
            echo wp_kses( $css, [ 'style' => [ 'id' ] ] );
        }
    }

    /**
     * Removes admin pages from appearing in the wp-admin menu.
     *
     * @return void
     */
    public function remove_menu_items() {
        remove_menu_page( 'edit-comments.php' );
    }

    /**
     * Adds custom hazard type and inactive classes to frontend event pages.
     *
     * @param array $classes Array containing body element classes.
     * @return array
     */
    public function add_custom_classes_to_single( array $classes ) {
        if ( is_single() ) {
            global $post;
            // Add hazard_type and inactive classes to body if post is of event type.
            if ( 'event' === $post->post_type ) {
                $my_terms = get_the_terms( $post->ID, 'hazard_type' );
                if ( $my_terms && ! is_wp_error( $my_terms ) ) {
                    foreach ( $my_terms as $term ) {
                        $classes[] = 'hazard_type-' . $term->slug;
                    }
                }

                $status = self::get_field( 'status', $post->ID, false );
                if ( in_array( $status, self::$inactive_statuses, true ) ) {
                    $classes[] = 'inactive';
                }
            }
        }
        return $classes;
    }

    /**
     * Adds custom hazard type and inactive classes to editor event pages.
     *
     * @param string $classes String containing body element classes.
     * @return string
     */
    public function add_custom_classes_to_admin( string $classes ) {
        global $pagenow;

        if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
            global $post;
            // Add hazard_type and inactive classes to body if post is of event type.
            if ( 'event' === $post->post_type ) {
                $my_terms = get_the_terms( $post->ID, 'hazard_type' );
                if ( $my_terms && ! is_wp_error( $my_terms ) ) {
                    foreach ( $my_terms as $term ) {
                        $classes .= ' hazard_type-' . $term->slug;
                    }
                }

                $status = self::get_field( 'status', $post->ID, false );
                if ( in_array( $status, self::$inactive_statuses, true ) ) {
                    $classes .= ' inactive';
                }
            }
        }
        return $classes;
    }

    /**
     * Enqueues jQueryUI scripts used by blocks.
     *
     * @return void
     */
    public function enqueue_jquery_ui() {
        wp_enqueue_script( 'jquery-ui-autocomplete' );
    }

    /**
     * Sets notify subscription fields. Used in subscribe form block to set subscription
     * data from query params.
     *
     * @param array $data Array of subscription fields.
     * @return array
     */
    public function set_notify_subscription_fields( array $data ): array {
        $data = array_merge( $data, [ 'tax_region' => [] ] );
        return $data;
    }

    /**
     * Builds an HTML list based on taxonomies and term ids given in criteria.
     *
     * @param string $html
     * @param array  $criteria Subscription criteria array of taxonomy slug and term ids, ex. ['tax_taxonomy_name' => [1, 2, 3]].
     * @return string
     */
    public function set_subscription_criteria_list( string $html, array $criteria ): string {
        // Loop through criteria and create an agnostic array for both taxonomy and metadata criteria.
        $subscription_objects = [];
        $meta_criteria        = apply_filters( 'notify_subscription_fields', [] ) ?? [];

        foreach ( $criteria as $slug => $term_ids ) {

            // Handle select all criteria.
            $is_select_all = false;
            if ( str_ends_with( $slug, '_all' ) ) {
                $slug          = str_replace( '_all', '', $slug );
                $is_select_all = true;
            } elseif ( array_key_exists( $slug . '_all', $criteria ) && '1' === $criteria[ $slug . '_all' ] ) {
                // If the corresponding _all key exists and is true, continue. This will be handled when the loop gets to that key.
                continue;
            }

            // Check if it's a taxonomy or term.
            if ( str_starts_with( $slug, 'tax_' ) ) {
                $taxonomy_slug = str_replace( 'tax_', '', $slug );
                $taxonomy      = get_taxonomy( $taxonomy_slug );

                // Get taxonomy information now that we know it is a taxonomy.
                $taxonomy = get_taxonomy( $taxonomy_slug );
                if ( empty( $taxonomy ) ) {
                    continue;
                }

                $taxonomy_object = [
                    'label'  => $taxonomy->label,
                    'values' => [],
                ];

                if ( $is_select_all ) {
                    // If this is a select all, set the values to All.
                    if ( 'region' === $taxonomy_slug ) {
                        $taxonomy_object['values'] = [ 'All locations' ];
                    } else {
                        $taxonomy_object['values'] = [ 'All ' . $taxonomy->label ];
                    }
                } else {
                    // Build list, printing the labels of the taxonomy and each term.
                    foreach ( $term_ids as $term_id ) {
                        $term = get_term( $term_id );
                        if ( empty( $term ) || is_wp_error( $term ) ) {
                            continue;
                        }
                        $taxonomy_object['values'][ $term_id ] = $term->name;
                    }
                }

                $subscription_objects[ $taxonomy_slug ] = $taxonomy_object;

				// Alternatively, check if it is a meta type.
            } elseif ( str_starts_with( $slug, 'meta_' ) ) {
                $meta_slug = $slug;

                $meta = $meta_criteria[ $meta_slug ] ?? null;
                if ( ! isset( $meta ) ) {
                    continue;
                }

                $meta_object = [
                    'label'  => $meta['label'],
                    'values' => [],
                ];

                if ( $is_select_all ) {
                    // If this is a select all, set the values to All.
                    $meta_object['values'] = [ 'All ' . $meta['label'] ];
                } else {
                    foreach ( $term_ids as $term_id ) {
                        // Find the matching criteria value with the same key.
                        $term_label = $meta['values'][ $term_id ] ?? null;
                        if ( ! isset( $term_label ) ) {
                            continue;
                        }
                        $meta_object['values'][ $term_id ] = $term_label;
                    }
                }

                $subscription_objects[ $meta_slug ] = $meta_object;

				// Does not handle criteria which are neither taxonomy nor metadata.
            } else {
                continue;
            }

            // Build list, printing the labels of each subscription type and each term.
            $html = '';
            foreach ( $subscription_objects as $slug => $subscription_type ) {
                $terms_html = '';
                foreach ( $subscription_type['values'] as $term ) {
                    $terms_html .= sprintf( '<li>%s</li>', $term );
                }

                // Add to html if there were any terms found.
                if ( ! empty( $terms_html ) ) {
                    $html .= sprintf( '<ul>%s</ul>', $terms_html );
                }
            }
        }

        return $html;
    }

    /**
     * Determines whether the send notification input should appear in the editor for a given post.
     * Should only appear if: the post is an Event AND the post is new or the post is a wildfire or flood.
     *
     * @param boolean $can_post_be_notified
     * @return boolean Whether the post should have the ability to trigger notifications.
     */
    public function can_post_be_notified( bool $can_post_be_notified ) {
        // If a previous filter has already determined post can't be notified, return.
        if ( ! $can_post_be_notified ) {
            return $can_post_be_notified;
        }

        global $post;

        // Wildfire and flood are the only allowed hazard types.
        $allowed_hazard_types = [ 'wildfire', 'flood' ];

        // Post must exist and be an Event.
        if ( $post && 'event' === $post->post_type ) {

            // New posts have auto-draft status and don't have a hazard type set yet, return true.
            if ( 'auto-draft' === $post->post_status ) {
                return true;
            }

            $terms = get_the_terms( $post, 'hazard_type' );
            // If no terms or we get an error, return false.
            if ( ! $terms || is_wp_error( $terms ) ) {
                return false;
            }

            $term_slugs = array_column( (array) $terms, 'slug' );
            // Only allow notifications if event is in the allowed hazard types.
            if ( array_intersect( $term_slugs, $allowed_hazard_types ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Registers new routes for the WordPress REST API.
     */
    public function register_rest_routes() {
        $version   = '2';
        $namespace = 'wp/v' . $version;
        $base      = 'region-group';
        register_rest_route(
            $namespace,
            '/' . $base,
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'get_region_groups' ],
                'permission_callback' => '__return_true',
            ]
        );
    }

    /**
     * Gets terms belonging to the region_groups taxonomy.
     *
     * @return WP_REST_Response
     */
    public function get_region_groups() {
        // Get terms for region_groups taxonomy.
        $terms = get_terms(
            [
                'taxonomy'   => 'region_groups',
                'hide_empty' => false,
            ]
        );

        if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
            return new WP_REST_Response( $terms, 500 );
        }

        // Add the included_regions and group_type meta fields to terms.
        $response = [];
        foreach ( $terms as $term ) {
            $processed_term                   = (object) $term;
            $included_regions                 = self::get_field( 'included_regions', 'region_groups_' . $term->term_id, false ) ?? [];
            $type                             = self::get_field( 'group_type', 'region_groups_' . $term->term_id ) ?? [
				'value' => 'other',
				'label' => 'Other',
			];
            $processed_term->included_regions = $included_regions;
            $processed_term->group_type       = $type;
            $response[]                       = $processed_term;
        }

        return new WP_REST_Response( array_values( $response ) );
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public static function get_plugin_name(): string {
        return self::$plugin_name;
	}

    /**
     * Gets option name.
     *
     * @return string
     */
    public static function get_option_name(): string {
        return str_replace( '-', '_', self::get_plugin_name() );
    }
}
