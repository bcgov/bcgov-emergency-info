<?php
// phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundBeforeLastUsed
/**
 * Renders the `emergency-info/post-hazard-image` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the Post Hazard Image block markup.
 */
function render_block_post_hazard_image(
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
			'class' => 'hazard-image hazard-background p-3',
		]
    );

    if ( is_wp_error( $hazard_types ) || empty( $hazard_types ) ) {
        return '';
    }

    // Get Hazard Type image.
    $hazard_type         = $hazard_types[0];
    $hazard_image        = get_field( 'hazard_image', 'hazard_type_' . $hazard_type->term_id );
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

    // Build final block html.
    return sprintf(
        '
        <img %s loading="lazy" decoding="async" alt="%s" srcset="%s" src="%s" sizes="%s">
        ',
        $wrapper_attributes,
        $hazard_type->name,
        $hazard_image_srcset,
        $hazard_image_src,
        $hazard_image_sizes
    );
};

/**
 * Registers the `emergency-info/post-hazard-image` block on the server.
 */
register_block_type_from_metadata(
    $path . '/post-hazard-image',
    array(
        'render_callback' => 'render_block_post_hazard_image',
    )
);
