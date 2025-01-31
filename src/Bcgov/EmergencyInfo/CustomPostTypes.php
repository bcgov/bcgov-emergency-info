<?php
/**
 * Custom Post Types for Notify Client.
 *
 * @package Bcgov\NotifyClient
 * @since 1.0.0
 */

namespace Bcgov\EmergencyInfo;

use Bcgov\Common\Loader;

/**
 * CustomPostTypes class sets up custom post types and meta fields.
 */
class CustomPostTypes {

    /**
     * The name of the directory that stores ACF JSON files.
     *
     * @var string $acf_json_dir The name of the directory that stores ACF JSON files.
     */
    public static $acf_json_dir = 'acf-json';


    /**
     * Adds all filters and actions defined in this class.
     *
     * @return void
     */
    public function init() {
        $loader = new Loader();

        // Register post types, taxonomies, metadata.
        $loader->add_action( 'init', $this, 'register_hazard_type_taxonomy' );
        $loader->add_action( 'init', $this, 'register_region_taxonomy' );
        $loader->add_action( 'init', $this, 'register_region_group_taxonomy' );
        $loader->add_action( 'init', $this, 'register_event_post_type' );
        $loader->add_action( 'init', $this, 'register_event_metadata' );
        $loader->add_action( 'rest_api_init', $this, 'register_event_metadata' );
        $loader->add_filter( 'get_block_type_variations', $this, 'register_block_type_variations', 10, 2 );

        // ACF local json saving/loading. See https://www.advancedcustomfields.com/resources/local-json/#saving-explained.
        $loader->add_filter( 'acf/settings/save_json/key=group_63db3b0481dcc', $this, 'acf_json_save_point', 20 );
        $loader->add_filter( 'acf/settings/save_json/key=group_63f64c160b480', $this, 'acf_json_save_point', 20 );
        $loader->add_filter( 'acf/settings/save_json/key=group_645e5a8f05740', $this, 'acf_json_save_point', 20 );
        $loader->add_filter( 'acf/settings/save_json/key=group_646f96fae68e8', $this, 'acf_json_save_point', 20 );
        $loader->add_filter( 'acf/settings/save_json/key=group_646f98e444751', $this, 'acf_json_save_point', 20 );
        $loader->add_filter( 'acf/settings/save_json/key=group_647e01f789a0c', $this, 'acf_json_save_point', 20 );
        $loader->add_filter( 'acf/settings/save_json/key=group_6453d2a3c0b90', $this, 'acf_json_save_point', 20 );
        $loader->add_filter( 'acf/settings/save_json/key=group_65f377d3803bc', $this, 'acf_json_save_point', 20 );
        $loader->add_filter( 'acf/settings/save_json/key=group_6619996f06ce4', $this, 'acf_json_save_point', 20 );
        $loader->add_filter( 'acf/settings/save_json/key=group_6798148fa4948', $this, 'acf_json_save_point', 20 );
        $loader->add_filter( 'acf/settings/load_json', $this, 'acf_json_load_point', 20 );

        // Add columns to Hazard Type index pages.
        $loader->add_filter( 'manage_edit-hazard_type_columns', $this, 'add_hazard_type_column', 10, 1 );
        $loader->add_filter( 'manage_hazard_type_custom_column', $this, 'render_hazard_type_column', 10, 3 );

        // Add columns to Region index pages.
        $loader->add_filter( 'manage_edit-region_groups_columns', $this, 'set_region_groups_columns', 10, 1 );
        $loader->add_filter( 'manage_region_groups_custom_column', $this, 'render_region_groups_columns', 10, 3 );
        $loader->run();
    }

    /**
     * Adds an image column to the Hazard Type admin table.
     *
     * @see https://developer.wordpress.org/reference/hooks/manage_screen-id_columns/
     * @param array $columns The column header labels for the Hazard Type admin index table keyed by column ID.
     * @return array
     */
    public function add_hazard_type_column( array $columns ): array {
        $columns['image'] = 'Image';
        return $columns;
    }

    /**
     * Renders the image column for the Hazard Type admin table.
     *
     * @see https://developer.wordpress.org/reference/hooks/manage_this-screen-taxonomy_custom_column/
     * @param string $output  Custom column output. Default empty.
     * @param string $column  Name of the column.
     * @param int    $term_id Term ID.
     * @return string
     */
    public function render_hazard_type_column( string $output, string $column, int $term_id ): string {
        if ( 'image' === $column ) {
            $hazard_image = Plugin::get_field( 'hazard_image', 'hazard_type_' . $term_id );
            if ( empty( $hazard_image ) ) {
                return 'No image';
            }
            $hazard_image_id     = $hazard_image['id'];
            $hazard_image_srcset = '';
            $hazard_image_src    = '';
            $hazard_image_sizes  = '';
            if ( $hazard_image_id ) {
                $hazard_image_srcset = wp_get_attachment_image_srcset( $hazard_image_id );
                if ( ! $hazard_image_srcset ) {
                    $hazard_image_src = wp_get_attachment_image_url( $hazard_image_id, 'medium' );
                } else {
                    $hazard_image_sizes = wp_get_attachment_image_sizes( $hazard_image_id );
                }
            }
            $hazard_color = Plugin::get_field( 'colour', 'hazard_type_' . $term_id );
            if ( ! $hazard_color ) {
                $hazard_color = 'black';
            }
            return sprintf(
                '<img class="hazard-image-column" style="background-color: %s;" loading="lazy" decoding="async" srcset="%s" src="%s" sizes="%s">',
                $hazard_color,
                $hazard_image_srcset,
                $hazard_image_src,
                $hazard_image_sizes
            );
        }
        return $output;
    }

    /**
     * Sets custom columns for the Region Groups admin table.
     *
     * @see https://developer.wordpress.org/reference/hooks/manage_screen-id_columns/
     * @param array $columns The column header labels for the Regions admin index table keyed by column ID.
     * @return array
     */
    public function set_region_groups_columns( array $columns ): array {
        $columns['included_regions'] = 'Included Regions';
        $columns['group_type']       = 'Group Type';
        // Remove Count column, not useful in this case.
        unset( $columns['posts'] );
        return $columns;
    }

    /**
     * Renders custom columns for the Region Groups admin table.
     *
     * @see https://developer.wordpress.org/reference/hooks/manage_this-screen-taxonomy_custom_column/
     * @param string $output  Custom column output. Default empty.
     * @param string $column  Name of the column.
     * @param int    $term_id Term ID.
     * @return string
     */
    public function render_region_groups_columns( string $output, string $column, int $term_id ): string {
        if ( 'included_regions' === $column ) {
            $included_regions = Plugin::get_field( 'included_regions', 'region_groups_' . $term_id, false );
            $terms            = [];
            if ( is_array( $included_regions ) ) {
                foreach ( $included_regions as $region_id ) {
                    $term    = get_term( $region_id );
                    $terms[] = $term->name;
                }
                return implode( '<br>', $terms );
            }

            return '—';
        } elseif ( 'group_type' === $column ) {
            $type = Plugin::get_field( 'group_type', 'region_groups_' . $term_id )['label'] ?? 'Other';
            return $type;
        }
        return $output;
    }

    /**
     * Defines path to save Advanced Custom Fields' JSON files.
     *
     * @return string
     */
    public static function acf_json_save_point(): string {
        $acf_path = Plugin::$plugin_dir . self::$acf_json_dir;
        return $acf_path;
    }

    /**
     * Defines paths to load Advanced Custom Fields' JSON files.
     *
     * @param array $paths Array of paths to save ACF JSON files to.
     * @return array
     */
    public static function acf_json_load_point( array $paths ): array {
        unset( $paths[0] );
        $acf_path = Plugin::$plugin_dir . self::$acf_json_dir;
        $paths[]  = $acf_path;
        return $paths;
    }

    /**
     * Register Custom Post Type: Event
     */
	public function register_event_post_type() {
        $labels = array(
            'name'              => __( 'Events' ),
            'singular_name'     => __( 'Event' ),
            'search_items'      => __( 'Search Events' ),
            'all_items'         => __( 'All Events' ),
            'parent_item'       => __( 'Parent Event' ),
            'parent_item_colon' => __( 'Parent Event:' ),
            'edit_item'         => __( 'Edit Event' ),
            'update_item'       => __( 'Update Event' ),
            'add_new'           => __( 'Add New Event' ),
            'add_new_item'      => __( 'Add New Event' ),
            'new_item_name'     => __( 'New Event Name' ),
            'menu_name'         => __( 'Events' ),
        );
        $args   = array(
            'labels'              => $labels,
			'description'         => __( 'An emergency event.' ),
			'public'              => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_icon'           => 'dashicons-megaphone',
			'taxonomies'          => [
				'hazard_type',
				'region',
				'region_groups',
			],
			'query_var'           => 'event',
			'rewrite'             => [
				'slug'       => 'event',
				'with_front' => false,
				'pages'      => true,
				'feeds'      => false,
				'ep_mask'    => true,
			],
			'show_in_rest'        => true,
			'supports'            => array(
				'title',
				'editor',
				'thumbnail',
				'excerpt',
				'custom-fields',
				'revisions',
			),
        );

        register_post_type( 'event', $args );
    }

    /**
     * Register taxonomy: Hazard Type
     */
    public function register_hazard_type_taxonomy() {
        $object_types = array(
            'custom-pattern',
            'event',
        );

        $labels = array(
            'name'              => __( 'Hazard Types' ),
            'singular_name'     => __( 'Hazard Type' ),
            'search_items'      => __( 'Search Hazard Types' ),
            'all_items'         => __( 'All Hazard Types' ),
            'parent_item'       => __( 'Parent Hazard Type' ),
            'parent_item_colon' => __( 'Parent Hazard Type:' ),
            'edit_item'         => __( 'Edit Hazard Type' ),
            'update_item'       => __( 'Update Hazard Type' ),
            'add_new_item'      => __( 'Add New Hazard Type' ),
            'new_item_name'     => __( 'New Hazard Type Name' ),
            'menu_name'         => __( 'Hazard Types' ),
        );

        $args = array(
            'labels'               => $labels,
            'description'          => __( 'The type of hazard an emergency event belongs to.' ),
            'hierarchical'         => true,
            'show_tagcloud'        => false,
            'show_admin_column'    => true,
            'meta_box_cb'          => 'post_categories_meta_box',
            'meta_box_sanitize_cb' => 'taxonomy_meta_box_sanitize_cb_checkboxes',
            'object_type'          => array(
				'custom-pattern',
				'event',
			),
            'rewrite'              => array(
				'with_front'   => true,
				'hierarchical' => false,
				'ep_mask'      => false,
				'slug'         => 'hazard',
			),
            'query_var'            => 'hazard_type',
            'show_in_rest'         => true,
        );

        register_taxonomy( 'hazard_type', $object_types, $args );
    }

    /**
     * Register taxonomy: Region
     */
    public function register_region_taxonomy() {
        $labels = array(
            'name'              => __( 'Regions' ),
            'singular_name'     => __( 'Region' ),
            'search_items'      => __( 'Search Regions' ),
            'all_items'         => __( 'All Regions' ),
            'parent_item'       => __( 'Parent Region' ),
            'parent_item_colon' => __( 'Parent Region:' ),
            'edit_item'         => __( 'Edit Region' ),
            'update_item'       => __( 'Update Region' ),
            'add_new_item'      => __( 'Add New Region' ),
            'new_item_name'     => __( 'New Region Name' ),
            'menu_name'         => __( 'Regions' ),
        );

        $args = array(
            'labels'            => $labels,
            'name'              => 'region',
            'label'             => 'Regions',
            'show_tagcloud'     => false,
            'show_admin_column' => true,
            'object_type'       => array(
				'event',
			),
            'rewrite'           => array(
				'with_front'   => true,
				'hierarchical' => false,
				'ep_mask'      => false,
				'slug'         => 'region',
			),

            'query_var'         => 'region',
            'show_in_rest'      => true,
        );

        register_taxonomy( 'region', 'event', $args );
    }

    /**
     * Register taxonomy: Region Group
     */
    public function register_region_group_taxonomy() {
        $labels = array(
            'name'              => __( 'Region Groups' ),
            'singular_name'     => __( 'Region Group' ),
            'search_items'      => __( 'Search Region Groups' ),
            'all_items'         => __( 'All Regions Groups' ),
            'parent_item'       => __( 'Parent Region Group' ),
            'parent_item_colon' => __( 'Parent Region Group:' ),
            'edit_item'         => __( 'Edit Region Group' ),
            'update_item'       => __( 'Update Region Group' ),
            'add_new_item'      => __( 'Add New Region Group' ),
            'new_item_name'     => __( 'New Region Group Name' ),
            'menu_name'         => __( 'Region Groups' ),
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Groups used to organize regions into various buckets, eg. regional districts, tsunami zones.' ),
            'publicly_queryable' => false,
            'show_tagcloud'      => false,
            'show_in_quick_edit' => false,
            'show_admin_column'  => true,
            'object_type'        => array(
				'event',
			),
            'rewrite'            => array(
				'with_front'   => true,
				'hierarchical' => false,
				'ep_mask'      => false,
				'slug'         => 'region_groups',
			),

            'query_var'          => 'region_groups',
            'show_in_rest'       => true,
            'sort'               => true,
        );

        register_taxonomy( 'region_groups', 'event', $args );
    }

    /**
     * Registers post metadata fields.
     *
     * @return void
     */
    public function register_event_metadata() {
        register_post_meta(
            'event',
            'recommended_actions',
            [
                // Needs to be true for block bindings API.
                'show_in_rest' => true,
                'single'       => true,
                'type'         => 'string',
            ]
        );
    }
}
