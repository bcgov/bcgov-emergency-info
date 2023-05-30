<?php
// phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundBeforeLastUsed
/**
 * Renders the `emergency-info/post-meta-display` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the Post Meta Display block markup.
 */
function render_block_post_meta_display(
    array $attributes,
    string $content,
    WP_Block $block
): string {
    // phpcs:enable
    // Get Event.
    $post_id            = $block->context['postId'];
    $value_num          = $attributes['valueNum'];
    $wrapper_attributes = get_block_wrapper_attributes(
        [
			'class' => 'is-layout-flex flex-nowrap',
		]
    );

    $icon  = get_field( 'card_icon_' . $value_num, $post_id );
    $label = get_field( 'card_label_' . $value_num, $post_id );
    $value = get_field( 'card_value_' . $value_num, $post_id );

    // Don't show block when there's no value.
    if ( ! $value ) {
        return '';
    }

    // Only show the icon if an icon class is given.
    $icon_html = '';
    if ( $icon ) {
        $icon_html = sprintf( '<div class="wp-block-areoi-icon areoi-icon"><i class="text-dark %s" style="font-size:24px"></i></div>', $icon );
    }

    // Build final block html.
    return sprintf(
        '
        <div %s>
            %s
            <p><strong>%s:</strong> %s</p>
        </div>',
        $wrapper_attributes,
        $icon_html,
        $label,
        $value
    );
};

/**
 * Registers the `emergency-info/post-meta-display` block on the server.
 */
register_block_type_from_metadata(
    $path . '/post-meta-display',
    array(
        'render_callback' => 'render_block_post_meta_display',
    )
);
