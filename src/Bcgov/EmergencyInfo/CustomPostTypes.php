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
     * The name of the directory that stores CPT UI JSON files.
     *
     * @var string $cpt_ui_json_dir The name of the directory that stores CPT UI JSON files.
     */
    public static $cpt_ui_json_dir = 'cpt-ui-json';

    /**
     * Adds all filters and actions defined in this class.
     *
     * @return void
     */
    public function init() {
        $loader = new Loader();

        // ACF local json saving/loading. See https://www.advancedcustomfields.com/resources/local-json/#saving-explained.
        $loader->add_filter( 'acf/settings/save_json/key=group_63db3b0481dcc', $this, 'acf_json_save_point', 20 );
        $loader->add_filter( 'acf/settings/save_json/key=group_63f64c160b480', $this, 'acf_json_save_point', 20 );
        $loader->add_filter( 'acf/settings/save_json/key=group_645e5a8f05740', $this, 'acf_json_save_point', 20 );
        $loader->add_filter( 'acf/settings/save_json/key=group_646f96fae68e8', $this, 'acf_json_save_point', 20 );
        $loader->add_filter( 'acf/settings/save_json/key=group_646f98e444751', $this, 'acf_json_save_point', 20 );
        $loader->add_filter( 'acf/settings/save_json/key=group_647e01f789a0c', $this, 'acf_json_save_point', 20 );
        $loader->add_filter( 'acf/settings/save_json/key=group_6453d2a3c0b90', $this, 'acf_json_save_point', 20 );
        $loader->add_filter( 'acf/settings/save_json/key=group_65f377d3803bc', $this, 'acf_json_save_point', 20 );
        $loader->add_filter( 'acf/settings/load_json', $this, 'acf_json_load_point', 20 );

        // Set up CPT UI saving and loading.
        $loader->add_action( 'cptui_after_update_post_type', $this, 'pluginize_local_cptui_data', 20 );
        $loader->add_action( 'cptui_after_update_taxonomy', $this, 'pluginize_local_cptui_data', 20 );
        $loader->add_filter( 'cptui_post_types_override', $this, 'pluginize_load_local_cptui_post_type_data', 20 );
        $loader->add_filter( 'cptui_taxonomies_override', $this, 'pluginize_load_local_cptui_taxonomies_data', 20 );

        // Add columns to Hazard Type index pages.
        $loader->add_filter( 'manage_edit-hazard_type_columns', $this, 'add_hazard_type_column', 10, 1 );
        $loader->add_filter( 'manage_hazard_type_custom_column', $this, 'render_hazard_type_column', 10, 3 );

        // Add columns to Region index pages.
        $loader->add_filter( 'manage_edit-region_columns', $this, 'add_region_column', 10, 1 );
        $loader->add_filter( 'manage_region_custom_column', $this, 'render_region_group_column', 10, 3 );
        $loader->run();
    }

    /**
     * Adds an image column to the Hazard Type admin table.
     *
     * @param array $columns
     * @return array
     */
    public function add_hazard_type_column( array $columns ): array {
        $columns['image'] = 'Image';
        return $columns;
    }

    /**
     * Renders the image column for the Hazard Type admin table.
     *
     * @param string $output
     * @param string $column
     * @param int    $term_id
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
     * Adds the Region Groups column to the Regions admin table.
     *
     * @param array $columns
     * @return array
     */
    public function add_region_column( array $columns ): array {
        $columns['region_groups'] = 'Region Groups';
        return $columns;
    }

    /**
     * Renders the Region Groups column for the Regions admin table.
     *
     * @param string $output
     * @param string $column
     * @param int    $term_id
     * @return string
     */
    public function render_region_group_column( string $output, string $column, int $term_id ): string {
        if ( 'region_groups' === $column ) {
            $region_groups = Plugin::get_field( 'region_groups', 'region_' . $term_id, false );
            $terms         = [];
            if ( is_array( $region_groups ) ) {
                foreach ( $region_groups as $region_group_id ) {
                    $term    = get_term( $region_group_id );
                    $terms[] = $term->name;
                }
                return implode( ', ', $terms );
            }

            return '—';
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
     * @param array $paths
     * @return array
     */
    public static function acf_json_load_point( array $paths ): array {
        unset( $paths[0] );
        $acf_path = Plugin::$plugin_dir . self::$acf_json_dir;
        $paths[]  = $acf_path;
        return $paths;
    }

    /**
     * Saves post type and taxonomy data to JSON files in the theme directory.
     *
     * @param array $data Array of post type data that was just saved.
     */
    public function pluginize_local_cptui_data( array $data = array() ) {
        $cpt_ui_path = Plugin::$plugin_dir . self::$cpt_ui_json_dir;
        if ( ! is_dir( $cpt_ui_path ) ) {
            return;
        }

        $allowed_post_types = [ 'event' ];
        $allowed_taxonomies = [ 'hazard_type', 'region' ];

        if ( array_key_exists( 'cpt_custom_post_type', $data ) && in_array( $data['cpt_custom_post_type']['name'], $allowed_post_types, true ) ) {
            // Fetch all of our post types and encode into JSON.
            $cptui_post_types = get_option( 'cptui_post_types', array() );
            $content          = wp_json_encode( $cptui_post_types, JSON_PRETTY_PRINT );
            $path             = $cpt_ui_path . '/cptui_post_type_data.json';

            // Save the encoded JSON to a primary file holding all of them.
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
            file_put_contents( $path, $content );
        }

        if ( array_key_exists( 'cpt_custom_tax', $data ) && in_array( $data['cpt_custom_tax']['name'], $allowed_taxonomies, true ) ) {
            // Fetch all of our taxonomies and encode into JSON.
            $cptui_taxonomies = get_option( 'cptui_taxonomies', array() );
            $content          = wp_json_encode( $cptui_taxonomies, JSON_PRETTY_PRINT );
            $path             = $cpt_ui_path . '/cptui_taxonomy_data.json';

            // Save the encoded JSON to a primary file holding all of them.
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
            file_put_contents( $path, $content );
        }
    }

    /**
     * Load local post type JSON data.
     *
     * @param array $data Existing CPT data.
     * @return array $value overriding content for CPTUI
     */
    public function pluginize_load_local_cptui_post_type_data( array $data ): array {
        $loaded = $this->pluginize_load_local_cptui_data( 'cptui_post_type_data.json' );

        if ( false === $loaded ) {
            return $data;
        }

        $data_new = json_decode( $loaded, true );

        if ( $data_new ) {
            return array_merge( $data, $data_new );
        }

        return $data;
    }

    /**
     * Load local taxonomy JSON data.
     *
     * @param array $data Existing taxonomy data.
     * @return array $value overriding content for CPTUI
     */
    public function pluginize_load_local_cptui_taxonomies_data( array $data ): array {
        $loaded = $this->pluginize_load_local_cptui_data( 'cptui_taxonomy_data.json' );

        if ( false === $loaded ) {
            return $data;
        }

        $data_new = json_decode( $loaded, true );

        if ( $data_new ) {
            return array_merge( $data, $data_new );
        }

        return $data;
    }

    /**
     * Helper function to load a specific file.
     *
     * @param string $file_name Name of the local JSON file.
     * @return false|string
     */
    private function pluginize_load_local_cptui_data( string $file_name = '' ) {
        if ( empty( $file_name ) ) {
            return false;
        }
        $cpt_ui_path = Plugin::$plugin_dir . self::$cpt_ui_json_dir;
        $path        = $cpt_ui_path . '/' . $file_name;

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
        return file_get_contents( $path );
    }
}
