<?php
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
    $get_params           = $_GET;
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

    $taxonomy_slug = 'region';
    // Get all childless terms belonging to the taxonomy.
    $terms = get_terms(
        [
            'taxonomy'   => $taxonomy_slug,
            'hide_empty' => false,
            'childless'  => true,
        ]
    );

    $term_options = '';
    $parsed_terms = [];
    foreach ( $terms as $term ) {
        // Use the excludedTerms attribute to skip over those terms.
        if ( in_array( $term->term_id, $excluded_term_ids, true ) ) {
            continue;
        }
        $parsed_terms[] = [
            'label' => $term->name,
            'value' => $term->term_id,
        ];
        $is_selected    = in_array( $term->term_id, $preselected_term_ids, true );
        $term_options  .= sprintf( '<option value="%d" %s>%s</option>', $term->term_id, $is_selected ? 'selected' : '', $term->name );
    }

    // Pass the array of region terms to JS.
    add_action(
        'wp_enqueue_scripts',
        function () use ( $parsed_terms ) {
			wp_register_script( 'subscribe_form_script', '', [], true, true );
			wp_enqueue_script( 'subscribe_form_script' );
			wp_add_inline_script(
                'subscribe_form_script',
                'const terms = ' . wp_json_encode( $parsed_terms ),
                'before'
			);
		}
    );

    // Check to see if there is a valid email address in the URL parameters.
    $email       = sanitize_email( $get_params['email'] ?? '' );
    $button_text = __( 'Notify me' );

    // If there is a valid email, modify the form to reflect updating instead of subscribing.
    if ( $email ) {
        $button_text = __( 'Update locations' );
    }

    return sprintf(
        '
        <div %1$s>
            <form action="%2$s" method="post" class="needs-validation">
                %3$s
                <label class="radio" for="tax-region-all-1">
                    <input id="tax-region-all-1" type="radio" name="tax_region_all" value="1" %7$s><strong>Get updates for all locations in B.C.</strong></input>
                    <span class="dot"></span>
                </label>
                <div class="all-region-section">
                    <p>You will get email updates about Evacuation Orders and Alerts for %9$s municipalities, unincorporated communities and First Nations.</p>
                </div>
                <label class="radio" for="tax-region-all-0">
                    <input id="tax-region-all-0" type="radio" name="tax_region_all" value="0" %8$s><strong>Choose location(s) you\'d like updates for:</strong></input>
                    <span class="dot"></span>
                </label>
                <div class="region-section">
                    <div class="region-autocomplete-label"></div>
                    <ul class="region-list" aria-role="presentation"></ul>
                    <div class="region-autocomplete input-group">
                        <span class="input-group-text"><i class="geo-icon bi bi-geo-alt-fill"></i>
                        </span>
                        <input id="region-autocomplete-input"
                            class="form-control"
                            type="search"
                            placeholder="Type location(s)"
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
                <input id="post-type" type="hidden" name="post_type[]" value="event" />
                <input type="hidden" name="action" value="notify_create_subscription" />
                <hr>
                <div class="text_label">
                    <strong>
                        <label for="email-input">%4$s</label>
                    </strong>
                </div>
                <input id="email-input" class="text_input form-control-lg" type="email" name="email" value="%11$s" required><br>
                <label class="checkbox" for="consent">
                    <input type="checkbox" id="consent" name="consent" value="1" required>
                    <span class="checkmark"></span>
                    %10$s
                </label>
                <button class="BC-Gov-PrimaryButton" type="submit">%5$s</button>
            </form>
        </div>
        ',
        $wrapper_attributes,
        esc_url( admin_url( 'admin-ajax.php' ) ),
        wp_nonce_field( 'subscribe_form_nonce', 'subscribe_nonce', true, false ),
        __( 'Enter your email address' ),
        $button_text,
        $term_options,
        '1' === $select_all_regions ? 'checked' : '',
        '1' !== $select_all_regions ? 'checked' : '',
        count( $parsed_terms ),
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
