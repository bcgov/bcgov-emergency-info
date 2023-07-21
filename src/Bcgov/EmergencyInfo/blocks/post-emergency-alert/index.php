<?php
// phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundBeforeLastUsed
/**
 * Renders the `emergency-info/post-emergency-alert` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the Post Emergency Alert block markup.
 */
function render_block_post_emergency_alert(
    array $attributes,
    string $content,
    WP_Block $block
): string {
    // phpcs:enable
    // Get Event.
    $post_id             = $block->context['postId'];
    $wrapper_attributes  = get_block_wrapper_attributes();
    $has_emergency_alert = get_field( 'has_emergency_alert', $post_id );

    // Don't show block when there's no value.
    if ( ! $has_emergency_alert ) {
        return '';
    }

    // Build final block html.
    return sprintf(
        '
        <div %s>
            <div class="emergency-alert-pill badge rounded-pill">
                <i class="bi bi-broadcast"></i>
                <span> BC Emergency Alert<span class="d-none d-sm-inline-block">&nbsp;Issued</span></span>
            </div>
        </div>
        ',
        $wrapper_attributes,
    );
};

/**
 * Registers the `emergency-info/post-emergency-alert` block on the server.
 */
register_block_type_from_metadata(
    $path . '/post-emergency-alert',
    array(
        'render_callback' => 'render_block_post_emergency_alert',
    )
);
