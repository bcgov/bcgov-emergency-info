<?php
use Bcgov\EmergencyInfo\Plugin;

/**
 * Renders the `notify-client/subscribe-form` block on the server.
 *
 * @param array $attributes Block attributes.
 * @return string Returns the Subscribe Form block markup.
 */
function render_block_emergency_info_subscribe_form(
    array $attributes
): string {
    $wrapper_attributes = get_block_wrapper_attributes();

    $is_in_maintenance_mode = get_option( 'des_notify_maintenance_mode' );
    if ( $is_in_maintenance_mode ) {
        return '<p>Sorry! Our subscription service is currently undergoing maintenance.</p>';
    }

    // Get subscription criteria field names and extract values from GET params.
    $filter_fields     = apply_filters( 'notify_subscription_fields', [] ) ?? [];
    $filter_field_keys = array_keys( $filter_fields );
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $get_params = $_GET;

    // Check to see if there is a valid email address in the URL parameters.
    $email     = sanitize_email( $get_params['email'] ?? '' );
    $is_update = false;

    // If there is a valid email, modify the form to reflect updating instead of subscribing.
    if ( $email ) {
        $is_update = true;
    }

    // Check to see if there are terms in the URL parameters.
    $preselected_term_ids = [];
    foreach ( $filter_field_keys as $param ) {
        if ( array_key_exists( $param, $get_params ) ) {
            $value = $get_params[ $param ];
            // Values come as comma-separated strings, split them into an array.
            $value_array          = explode( ',', $value );
            $value_int_array      = array_map( 'intval', $value_array );
            $preselected_term_ids = array_merge( $preselected_term_ids, $value_int_array );
        }
    }
    $select_all_regions = 0;
    if ( array_key_exists( 'tax_region_all', $get_params ) ) {
        $select_all_regions = $get_params['tax_region_all'];
    }

    // Extract term ids from term strings.
    $excluded_terms    = $attributes['excludedTerms'] ?? [];
    $excluded_term_ids = array_map(
        function ( $term ) {
            $term_id = explode( '(ID:', $term );
            return intval( array_pop( $term_id ) );
        },
        $excluded_terms
    );

    // Get all terms belonging to the region group taxonomy.
    $region_groups        = get_terms(
        [
            'taxonomy'   => 'region_groups',
            'hide_empty' => false,
        ]
    );
    $parsed_region_groups = array_map(
        function ( $region_group ) {
			return (object) [
				'term'             => $region_group,
				'included_regions' => Plugin::get_field( 'included_regions', 'region_groups_' . $region_group->term_id, false ),
			];
		},
        $region_groups
    );

    // Get all childless terms belonging to the region taxonomy.
    $regions = get_terms(
        [
            'taxonomy'   => 'region',
            'hide_empty' => false,
            'childless'  => true,
        ]
    );

    $term_options   = '';
    $parsed_regions = [];
    foreach ( $regions as $region ) {
        // Use the excludedTerms attribute to skip over those terms.
        if ( in_array( $region->term_id, $excluded_term_ids, true ) ) {
            continue;
        }

        $is_region_group_term = Plugin::get_field( 'is_region_group_term', 'region_' . $region->term_id, false );
        $is_region_group_term = '1' === $is_region_group_term;

        // Add the region's region groups.
        $region_region_groups = [];
        foreach ( $parsed_region_groups as $region_group ) {
            if ( in_array( $region->term_id, $region_group->included_regions, true ) ) {
                $region_region_groups[] = $region_group->term->name;
            }
        }

        $parsed_regions[] = [
            'label'             => $region->name,
            'value'             => $region->term_id,
            'regionGroups'      => $region_region_groups,
            'isRegionGroupTerm' => $is_region_group_term,
        ];

        $is_selected   = in_array( $region->term_id, $preselected_term_ids, true );
        $term_options .= sprintf( '<option value="%d" %s>%s</option>', $region->term_id, $is_selected ? 'selected' : '', $region->name );
    }

    // Pass the array of region terms to JS.
    add_action(
        'wp_enqueue_scripts',
        function () use ( $parsed_regions, $is_update ) {
			wp_register_script( 'subscribe_form_script', '', [], true, true );
			wp_enqueue_script( 'subscribe_form_script' );
			wp_add_inline_script(
                'subscribe_form_script',
                sprintf(
                    '
                        const regions = %s;
                        const update = %s;
                    ',
                    wp_json_encode( $parsed_regions ),
                    $is_update ? 'true' : 'false'
                ),
                'before'
			);
		}
    );

    return sprintf(
        '
        <div %1$s>
            <form id="subscribe-form" action="%2$s" method="post" class="needs-validation">
                %3$s
                <input id="post-type" type="hidden" name="post_type[]" value="event" />
                <input type="hidden" name="action" value="notify_create_subscription" />
                <div class="get-updates">
                    <strong>Get email updates for:</strong>
                </div>
                <label class="radio" for="tax-region-all-1">
                    <input id="tax-region-all-1" type="radio" name="tax_region_all" value="1" %7$s>All locations in B.C.</input>
                    <span class="dot"></span>
                </label>
                <div class="all-region-section">
                    You will get email updates about Evacuation Orders and Alerts for %9$s municipalities, regional districts and First Nations.
                </div>
                <label class="radio" for="tax-region-all-0">
                    <input id="tax-region-all-0" type="radio" name="tax_region_all" value="0" %8$s>Select location(s)</input>
                    <span class="dot"></span>
                </label>
                <div class="region-section">
                    <strong>
                        <div class="region-autocomplete-label"></div>
                    </strong>
                    <ul class="region-list" aria-role="presentation"></ul>
                    <div class="region-group-autocomplete-label"></div>
                    <ul class="region-group-list" aria-role="presentation"></ul>
                    <div class="region-autocomplete input-group">
                        <span class="input-group-text"><i class="geo-icon bi bi-geo-alt-fill"></i>
                        </span>
                        <input id="region-autocomplete-input"
                            class="form-control"
                            type="search"
                            placeholder="Search for municipalities, regional districts and First Nations"
                            role="searchbox"
                            aria-description="Search results will appear below"
                            aria-controls="ui-id-1"
                            aria-autocomplete="list"
                            aria-activedescendant=""
                            />
                        <span class="input-group-text"></span>
                    </div>
                    <div id="listbox-wrapper"></div>
                    <select id="region-select" name="tax_region[]" style="display: none" multiple>
                        %6$s
                    </select>
                </div>
                <div class="email-section">
                    <div class="text_label">
                        <strong>
                            <label for="email-input">%4$s</label>
                        </strong>
                    </div>
                    <input id="email-input" class="text_input" type="email" name="email" value="%11$s" placeholder="Type your email address" required><br>
                </div>
                <div class="consent-section">
                    <label class="checkbox" for="consent">
                        <input type="checkbox" id="consent" name="consent" value="1" required>
                        <span class="checkmark"></span>
                        %10$s
                    </label>
                </div>
                <button class="BC-Gov-PrimaryButton" type="submit">%5$s</button>
            </form>
        </div>
        ',
        $wrapper_attributes,
        esc_url( admin_url( 'admin-ajax.php' ) ),
        wp_nonce_field( 'subscribe_form_nonce', 'subscribe_nonce', true, false ),
        __( 'Your email address:' ),
        $is_update ? __( 'Update locations' ) : __( 'Notify me' ),
        $term_options,
        '1' === $select_all_regions ? 'checked' : '',
        '1' !== $select_all_regions ? 'checked' : '',
        count( $parsed_regions ),
        __( 'I have read and understood the Privacy and Collection Notice, Service Disclaimer and Terms of Use' ),
        $email
    );
}

/**
 * Registers the `notify-client/subscribe-form` block on the server.
 */
register_block_type_from_metadata(
    $path . '/subscribe-form',
    array(
        'render_callback' => 'render_block_emergency_info_subscribe_form',
    )
);
