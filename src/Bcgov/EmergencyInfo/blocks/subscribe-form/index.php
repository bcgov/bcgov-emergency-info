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

    // Get subscription criteria field names and extract values from GET params.
    $filter_params = apply_filters( 'notify_subscription_fields', [] ) ?? [];
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $get_params           = $_GET;
    $preselected_term_ids = [];
    foreach ( $filter_params as $param ) {
        if ( array_key_exists( $param, $get_params ) ) {
            $value = $get_params[ $param ];
            // Values come as comma-separated strings, split them into an array.
            $value_array          = explode( ',', $value );
            $value_int_array      = array_map( 'intval', $value_array );
            $preselected_term_ids = array_merge( $preselected_term_ids, $value_int_array );
        }
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

    return sprintf(
        '
        <div %1$s>
            <form action="%2$s" method="post">
                %3$s
                
                <label class="region-autocomplete-label" for="region-autocomplete-input"></label>
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
                    <span class="input-group-text">
                        <button type="button" aria-label="Clear search" class="clear-input btn btn-link">
                            <i class="bi bi-x"></i>
                        </button>
                    </span>
                </div>
                <div id="listbox-wrapper"></div>
                <select id="region-select" name="tax_region[]" multiple>
                    %6$s
                </select>
                <input id="post-type" type="hidden" name="post_type[]" value="event" />
                <input type="hidden" name="action" value="notify_create_subscription" />
                <div class="text_label">
                    <label for="email-input">%4$s</label>
                </div>
                <input id="email-input" class="text_input" type="email" name="email" required><br>
                <button class="BC-Gov-PrimaryButton" type="submit">%5$s</button>
                <div>* Required field</div>
            </form>
        </div>
        ',
        $wrapper_attributes,
        esc_url( admin_url( 'admin-post.php' ) ),
        wp_nonce_field( 'subscribe_form_nonce', 'subscribe_nonce', true, false ),
        __( 'Enter your email address*' ),
        __( 'Subscribe' ),
        $term_options
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
