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

    // Get taxonomy terms and build checkbox inputs from them.
    $checkbox_html = '<div class="post-type-section">';
    $taxonomy_slug = 'region';
    // Get terms for the linked post so we can set them to checked by default.
    $post_terms    = get_the_terms( $post_id, $taxonomy_slug );
    $post_term_ids = [];
    if ( ! empty( $post_terms ) ) {
        $post_term_ids = array_column( $post_terms, 'term_id' );
    }

    // Get all terms belonging to the taxonomy.
    $terms        = get_terms(
        [
            'taxonomy'   => $taxonomy_slug,
            'hide_empty' => false,
        ]
    );
    $section_html = '';

    foreach ( $terms as $term ) {
        // Use the excludedTerms attribute to skip over rendering those terms.
        if ( in_array( $term->term_id, $excluded_term_ids, true ) ) {
            continue;
        }
        $checked       = in_array( $term->term_id, $post_term_ids, true ) ? 'checked' : '';
        $section_html .= sprintf(
            '
            <label class="checkbox" for="%1$s-%2$s">
                <input type="checkbox" id="%1$s-%2$s" name="tax_%1$s[]" value="%3$s" %4$s>
                <span class="checkmark"></span>
                %5$s
            </label>
            ',
            $term->taxonomy,
            $term->slug,
            $term->term_id,
            $checked,
            $term->name,
        );
    }

    // Only add the section to the checkbox html if at least one term checkbox exists.
    if ( ! empty( $section_html ) ) {
        $taxonomy       = get_taxonomy( $taxonomy_slug );
        $checkbox_html .= sprintf( '<fieldset><legend>%s</legend>%s</fieldset>', $taxonomy->label, $section_html );
    }
    $checkbox_html .= '<hr /></div>';

    return sprintf(
        '
        <div %1$s>
            <form action="%2$s" method="post">
                %3$s
                %4$s
                <input type="hidden" name="action" value="notify_create_subscription" />
                <div class="text_label">
                    <label for="email-input">%5$s</label>
                </div>
                <input id="email-input" class="text_input" type="email" name="email" required><br>
                <button class="BC-Gov-PrimaryButton" type="submit">%6$s</button>
            </form>
        </div>
        ',
        $wrapper_attributes,
        esc_url( admin_url( 'admin-post.php' ) ),
        $checkbox_html,
        wp_nonce_field( 'subscribe_form_nonce', 'subscribe_nonce', true, false ),
        __( 'Email Address' ),
        __( 'Submit' ),
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
