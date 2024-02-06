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

    // Build post information html.
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $post_id = sanitize_key( $_GET['post_id'] ?? null );

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
    // Get terms for the linked post so we can set them to selected by default.
    $post_terms    = get_the_terms( $post_id, $taxonomy_slug );
    $post_term_ids = [];
    if ( ! empty( $post_terms ) ) {
        $post_term_ids = array_column( $post_terms, 'term_id' );
    }

    // Get all terms belonging to the taxonomy.
    $terms = get_terms(
        [
            'taxonomy'   => $taxonomy_slug,
            'hide_empty' => false,
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
        $is_selected    = in_array( $term->term_id, $post_term_ids, true );
        $term_options  .= sprintf( '<option value="%d" %s>%s</option>', $term->term_id, $is_selected ? 'selected' : '', $term->name );
    }

    // Pass the array of region terms to JS.
    wp_register_script( 'subscribe_form_script', '', [], true, true );
    wp_enqueue_script( 'subscribe_form_script' );
    wp_add_inline_script(
        'subscribe_form_script',
        'const terms = ' . wp_json_encode( $parsed_terms ),
        'before'
    );

    return sprintf(
        '
        <div %1$s>
            <form action="%2$s" method="post">
                %3$s
                
                <div class="text_label">
                    <label for="region-autocomplete">Regions</label>
                </div>
                <ul class="region-list"></ul>
                <input id="region-autocomplete" class="text_input" />
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
            </form>
        </div>
        ',
        $wrapper_attributes,
        esc_url( admin_url( 'admin-post.php' ) ),
        wp_nonce_field( 'subscribe_form_nonce', 'subscribe_nonce', true, false ),
        __( 'Email Address' ),
        __( 'Submit' ),
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
