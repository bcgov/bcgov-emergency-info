<?php

namespace Bcgov\EmergencyInfo\AdminPage;

use Bcgov\Common\Loader;

/**
 * RegionLoader class creates Region and Region Group terms from json.
 */
class RegionLoader {


    /**
     * The page slug.
     *
     * @var string $page_slug The page slug.
     */
    protected $page_slug = 'bcgov-option-region-loader';

    /**
     * Initiates hook creation.
     *
     * @return void
     */
    public function init() {
        $loader = new Loader();
        $loader->add_action( 'admin_menu', $this, 'add_submenu_item', 13 );
        $loader->add_action( 'wp_ajax_emergency_info_load_region_terms', $this, 'load_region_terms' );
        $loader->run();
    }

    /**
     * Adds the Region Loader menu item.
     */
    public function add_submenu_item() {
        add_menu_page(
            __( 'Region Loader' ),
            __( 'Region Loader' ),
            is_multisite() ? 'manage_sites' : 'manage_options',
            $this->page_slug,
            [ $this, 'region_loader_page' ],
            'dashicons-admin-site'
        );
    }

    /**
     * Callback for region loader functionality.
     *
     * @return void;
     */
    public function load_region_terms(): void {
        if ( isset( $_REQUEST['region_loader_form_nonce'] ) && wp_verify_nonce( $_REQUEST['region_loader_form_nonce'], 'region_loader_form_nonce' ) ) {
            // Restrict endpoint to only admin users.
            if ( ! current_user_can( is_multisite() ? 'manage_sites' : 'manage_options' ) ) {
                wp_die( 'Forbidden: cannot load region terms.', [ 'status' => 401 ] );
            }

            // Don't attempt to insert terms if any already exist.
            if ( count(
                get_terms(
                    [
						'taxonomy'   => 'region',
						'hide_empty' => false,
						'number'     => 1,
					]
                )
            ) > 0 ) {
                wp_die( 'Region terms already exist, cannot add from JSON.', [ 'status' => 400 ] );
            }

            $regions = json_decode( stripslashes( $_REQUEST['region_json'] ), false, 512, JSON_THROW_ON_ERROR );

            if ( ! $regions ) {
                wp_die( 'Could not parse region JSON.', [ 'status' => 400 ] );
            }

            // Insert Region terms from json.
            $regional_districts = [];
            foreach ( $regions as $region ) {
                $label       = $region->label;
                $groups      = $region->regionGroups;
                $region_term = wp_insert_term( $label, 'region' );
                foreach ( $groups as $group ) {
                    $regional_districts[ $group ][] = $region_term['term_id'];
                }
            }

            // Insert Region Group terms from json.
            foreach ( $regional_districts as $regional_district => $included_regions ) {
                $rg_term = wp_insert_term( $regional_district, 'region_groups' );
                // ACF update_field function, see: https://www.advancedcustomfields.com/resources/update_field/.
                update_field( 'included_regions', $included_regions, 'region_groups_' . $rg_term['term_id'] );
                update_field( 'group_type', 'regional_district', 'region_groups_' . $rg_term['term_id'] );
            }

            wp_safe_redirect( admin_url( 'admin.php' ) . '?page=' . $this->page_slug );
        } else {
            wp_die( 'Invalid nonce.' );
        }
    }

    /**
     * Renders the Region Loader page.
     */
    public function region_loader_page() {
        include_once 'inc/region-loader.php';
    }
}
