<?php
// phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundBeforeLastUsed
/**
 * Renders the `emergency-info/post-hazard-title` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the Post Hazard Title block markup.
 */
function render_block_post_hazard_title(
    array $attributes,
    string $content,
    WP_Block $block
): string {
    // phpcs:enable
    // Get Event.
    $post_id            = $block->context['postId'];
    $event              = get_post( $post_id );
    $hazard_types       = get_the_terms( $event, 'hazard_type' );
    $wrapper_attributes = get_block_wrapper_attributes(
        [
			'class' => 'hazard-text',
		]
    );

    if ( is_wp_error( $hazard_types ) || empty( $hazard_types ) ) {
        return '';
    }

    // Get Hazard Type title.
    $hazard_type  = $hazard_types[0];
    $hazard_title = $hazard_type->name;

    // Build final block html.
    return sprintf(
        '
        <h1 %s>%s</h1>
        ',
        $wrapper_attributes,
        $hazard_title,
    );
};

/**
 * Registers the `emergency-info/post-hazard-title` block on the server.
 */
register_block_type_from_metadata(
    $path . '/post-hazard-title',
    array(
        'render_callback' => 'render_block_post_hazard_title',
    )
);
